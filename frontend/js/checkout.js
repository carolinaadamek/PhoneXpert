document.addEventListener("DOMContentLoaded", function () {
  const totalPriceSpan = document.getElementById("total-price");
  const voucherInput = document.getElementById("voucher");
  const feedback = document.getElementById("voucher-feedback");
  const form = document.getElementById("checkout-form");

  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  let rabattProzent = 0;

  function berechneGesamt() {
    let gesamt = 0;
    cart.forEach(p => gesamt += parseFloat(p.price) * p.quantity);
    if (rabattProzent > 0) {
      const rabatt = gesamt * (rabattProzent / 100);
      feedback.textContent = `Rabatt angewendet: -${rabatt.toFixed(2)} € (${rabattProzent}%)`;
      gesamt -= rabatt;
    }
    totalPriceSpan.textContent = gesamt.toFixed(2);
  }

  berechneGesamt();

  voucherInput.addEventListener("blur", () => {
    const code = voucherInput.value.trim();
    if (!code) return;

    fetch("../../backend/cart/check_voucher.php?code=" + encodeURIComponent(code))
      .then(res => res.json())
      .then(data => {
        if (data.valid) {
          rabattProzent = parseFloat(data.percent);
        } else {
          rabattProzent = 0;
          feedback.textContent = "Code ungültig.";
        }
        berechneGesamt();
      });
  });

  document.getElementById("gleichAdresse").addEventListener("change", function () {
    document.getElementById("rechnungsfelder").style.display = this.checked ? "none" : "block";
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Zahlung erfolgreich abgeschlossen! Danke für deine Bestellung.");
    localStorage.removeItem("cart");
    window.location.href = "index.html";
  });
});
