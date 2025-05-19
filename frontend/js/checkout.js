document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("checkout-form");
  const totalPriceSpan = document.getElementById("total-price");
  const voucherInput = document.getElementById("voucher");
  const voucherBtn = document.getElementById("voucherBtn");
  const feedback = document.getElementById("voucher-feedback");
  const zahlungsart = document.getElementById("zahlungsart");
  const kreditkartenfelder = document.getElementById("kreditkartenfelder");

  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  let rabattProzent = 0;

  // Kreditkartenformatierung
  const cardNumberInput = form.cardNumber;
  const expiryInput = form.expiry;
  const cvcInput = form.cvc;

  cardNumberInput.addEventListener("input", () => {
    let value = cardNumberInput.value.replace(/\D/g, "").substring(0, 16);
    cardNumberInput.value = value.replace(/(.{4})/g, "$1 ").trim();
  });

  expiryInput.addEventListener("input", () => {
    let value = expiryInput.value.replace(/\D/g, "").substring(0, 4);
    if (value.length >= 3) {
      expiryInput.value = value.substring(0, 2) + "/" + value.substring(2);
    } else {
      expiryInput.value = value;
    }
  });

  cvcInput.addEventListener("input", () => {
    cvcInput.value = cvcInput.value.replace(/\D/g, "").substring(0, 3);
  });

  // Preisberechnung
  function berechneGesamt() {
    let gesamt = 0;
    cart.forEach(p => gesamt += parseFloat(p.price) * p.quantity);

    if (rabattProzent > 0) {
      const rabatt = gesamt * (rabattProzent / 100);
      feedback.textContent = `Rabatt: -${rabatt.toFixed(2)} € (${rabattProzent}%)`;
      gesamt -= rabatt;
    } else {
      feedback.textContent = voucherInput.value.trim() ? "❌ Code ungültig oder nicht aktiv." : "";
    }

    totalPriceSpan.textContent = gesamt.toFixed(2);
  }

  berechneGesamt();

  // Gutscheincode prüfen
  voucherBtn.addEventListener("click", () => {
    const code = voucherInput.value.trim();
    if (!code) {
      feedback.textContent = "⚠️ Bitte einen Gutscheincode eingeben.";
      rabattProzent = 0;
      berechneGesamt();
      return;
    }

    fetch("../../backend/cart/check_voucher.php?code=" + encodeURIComponent(code))
        .then(res => res.json())
        .then(data => {
          rabattProzent = data.valid ? parseFloat(data.percent) : 0;
          berechneGesamt();
        })
        .catch(() => {
          rabattProzent = 0;
          feedback.textContent = "❌ Serverfehler beim Prüfen des Codes.";
          berechneGesamt();
        });
  });

  // Rechnungsadresse anzeigen
  document.getElementById("gleichAdresse").addEventListener("change", function () {
    document.getElementById("rechnungsfelder").style.display = this.checked ? "none" : "block";
  });

  // Zahlungsart-Felder umschalten
  zahlungsart.addEventListener("change", function () {
    const isCard = this.value === "kreditkarte";
    kreditkartenfelder.style.display = isCard ? "block" : "none";
    kreditkartenfelder.querySelectorAll("input").forEach(input => input.required = isCard);
  });
  zahlungsart.dispatchEvent(new Event("change"));

  // Validierung
  function validateForm() {
    let isValid = true;
    const fields = form.querySelectorAll("input[required], select[required]");

    fields.forEach(field => {
      const errorMsg = field.nextElementSibling;
      if (errorMsg && errorMsg.classList.contains("invalid-feedback")) {
        errorMsg.remove();
      }
      field.classList.remove("is-invalid");

      if (!field.checkValidity()) {
        const feedback = document.createElement("div");
        feedback.className = "invalid-feedback";
        feedback.textContent = "Bitte gültige Eingabe.";
        field.classList.add("is-invalid");
        field.parentNode.appendChild(feedback);
        isValid = false;
      }
    });

    if (zahlungsart.value === "kreditkarte") {
      const cardValue = cardNumberInput.value.replace(/\s/g, '');
      const expiryValue = expiryInput.value;
      const cvcValue = cvcInput.value;

      const cardValid = /^\d{16}$/.test(cardValue);
      const expiryValid = /^(0[1-9]|1[0-2])\/\d{2}$/.test(expiryValue);
      const cvcValid = /^\d{3}$/.test(cvcValue);

      if (!cardValid) {
        showInvalid(cardNumberInput, "Kartennummer muss 16 Ziffern haben.");
        isValid = false;
      }
      if (!expiryValid) {
        showInvalid(expiryInput, "Ablaufdatum im Format MM/YY angeben.");
        isValid = false;
      }
      if (!cvcValid) {
        showInvalid(cvcInput, "CVC muss 3 Ziffern sein.");
        isValid = false;
      }
    }

    return isValid;
  }

  function showInvalid(input, message) {
    const error = document.createElement("div");
    error.className = "invalid-feedback";
    error.textContent = message;
    input.classList.add("is-invalid");
    input.parentNode.appendChild(error);
  }

  // Formular absenden
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    if (!validateForm()) return;

    if (cart.length === 0) {
      alert("Der Warenkorb ist leer.");
      return;
    }

    const lieferadresse = [
      form.vorname.value,
      form.nachname.value,
      form.strasse.value,
      form.nummer.value,
      form.plz.value,
      form.ort.value,
      form.land.value
    ].map(s => s.trim()).join(", ");

    let rechnungsadresse = lieferadresse;
    if (!form.gleichAdresse.checked) {
      rechnungsadresse = [
        form.r_vorname.value,
        form.r_nachname.value,
        form.r_strasse.value,
        form.r_nummer.value,
        form.r_plz.value,
        form.r_ort.value,
        form.r_land.value
      ].map(s => s.trim()).join(", ");
    }

    const gutscheincode = voucherInput.value.trim();
    const zahlungstyp = zahlungsart.value;

    const payload = {
      cart,
      lieferadresse,
      rechnungsadresse,
      gutscheincode,
      rabatt: rabattProzent,
      zahlung: zahlungstyp,
      ...(zahlungstyp === "kreditkarte" && {
        cardNumber: form.cardNumber.value.trim(),
        expiry: form.expiry.value.trim(),
        cvc: form.cvc.value.trim()
      })
    };

    fetch("../../backend/orders/save_order.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
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
        .catch(() => alert("Verbindung zum Server fehlgeschlagen."));
  });
});
