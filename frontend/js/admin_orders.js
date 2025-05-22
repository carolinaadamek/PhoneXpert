document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("bestellungen-container");

    // Alle Bestellungen laden
    fetch("../../backend/orders/get_all_orders.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Serverantwort war nicht OK");
            }
            return response.json();
        })
        .then(data => {
            // Keine Bestellungen vorhanden
            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = "<p class='text-muted'>Keine Bestellungen gefunden.</p>";
                return;
            }

            let html = "";
            // Jede Bestellung als Card anzeigen
            data.forEach(order => {
                const produkte = (order.items || []).map(p =>
                    `<li>${p.produktname} – ${p.menge} x ${parseFloat(p.preis).toFixed(2)} €</li>`
                ).join("");

                html += `
          <div class="card mb-4 shadow">
            <div class="card-body">
              <h5 class="card-title">Bestellung #${order.id}</h5>
              <p><strong>Benutzer:</strong> ${order.username ?? '–'} (ID: ${order.user_id ?? '-'})</p>
              <p><strong>Lieferadresse:</strong> ${order.lieferadresse || "-"}</p>
              <p><strong>Rechnungsadresse:</strong> ${order.rechnungsadresse || "-"}</p>
              <p><strong>Gutscheincode:</strong> ${order.gutscheincode || "-"}</p>
              <p><strong>Rabatt:</strong> ${order.rabatt ?? 0}%</p>
              <p><strong>Gesamtsumme:</strong> ${parseFloat(order.gesamt).toFixed(2)} €</p>
              <p><strong>Erstellt am:</strong> ${new Date(order.erstellt_am).toLocaleString()}</p>
              <p><strong>Produkte:</strong></p>
              <ul>${produkte}</ul>
              <button class="btn btn-danger btn-sm mt-2 stornieren-btn" data-id="${order.id}">
                Bestellung stornieren
              </button>
            </div>
          </div>
        `;
            });

            container.innerHTML = html;
        })
        .catch(error => {
            console.error("Fehler beim Laden der Bestellungen:", error);
            container.innerHTML = "<p class='text-danger'>Fehler beim Laden der Bestellungen.</p>";
        });

    // Stornieren-Button-Klick
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("stornieren-btn")) {
            const id = e.target.dataset.id;
            if (confirm("Diese Bestellung wirklich stornieren?")) {
                fetch("../../backend/orders/cancel_order.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert("Bestellung erfolgreich storniert.");
                            location.reload();
                        } else {
                            alert("Fehler beim Stornieren: " + (data.message || "Unbekannter Fehler"));
                        }
                    })
                    .catch(err => {
                        console.error("Storno-Fehler:", err);
                        alert("Verbindungsfehler beim Stornieren.");
                    });
            }
        }
    });
});
