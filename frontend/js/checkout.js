document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("checkout-form");
  const totalPriceSpan = document.getElementById("total-price");
  const voucherInput = document.getElementById("voucher");
  const feedback = document.getElementById("voucher-feedback");

  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  let rabattProzent = 0;

  function berechneGesamt() {
    let gesamt = 0;
    cart.forEach(p => gesamt += parseFloat(p.price) * p.quantity);
    if (rabattProzent > 0) {
      const rabatt = gesamt * (rabattProzent / 100);
      feedback.textContent = `Rabatt angewendet: -${rabatt.toFixed(2)} € (${rabattProzent}%)`;
      gesamt -= rabatt;
    } else {
      feedback.textContent = "";
    }
    totalPriceSpan.textContent = gesamt.toFixed(2);
  }

  berechneGesamt();

  // Gutscheincode prüfen
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
      })
      .catch(() => {
        rabattProzent = 0;
        feedback.textContent = "Fehler beim Prüfen des Codes.";
      });
  });

  // Rechnungsadresse ein-/ausblenden
  document.getElementById("gleichAdresse").addEventListener("change", function () {
    document.getElementById("rechnungsfelder").style.display = this.checked ? "none" : "block";
  });

  // Formular absenden
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    if (cart.length === 0) {
      alert("Der Warenkorb ist leer.");
      return;
    }

    // Lieferadresse
    const lieferadresse = [
      form.vorname.value,
      form.nachname.value,
      form.strasse.value,
      form.plz.value,
      form.ort.value,
      form.land.value
    ].map(s => s.trim()).join(", ");

    // Rechnungsadresse
    let rechnungsadresse = lieferadresse;
    if (!form.gleichAdresse.checked) {
      rechnungsadresse = [
        form.r_vorname.value,
        form.r_nachname.value,
        form.r_strasse.value,
        form.r_plz.value,
        form.r_ort.value,
        form.r_land.value
      ].map(s => s.trim()).join(", ");
    }

    const gutscheincode = voucherInput.value.trim();

    const payload = {
      cart,
      lieferadresse,
      rechnungsadresse,
      gutscheincode,
      rabatt: rabattProzent
    };

    fetch("../../backend/orders/save_order.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert("Zahlung erfolgreich abgeschlossen! Danke für deine Bestellung.");
        localStorage.removeItem("cart");
        window.location.href = "index.html";
      } else {
        alert("Fehler beim Speichern der Bestellung:\n" + (data.message || "Unbekannter Fehler"));
      }
    })
    .catch(err => {
      console.error("Netzwerkfehler:", err);
      alert("Verbindung zum Server fehlgeschlagen.");
    });
  });
});
