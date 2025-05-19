document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("bestellungen-container");

    fetch("../../backend/orders/get_user_orders.php")
        .then(response => {
            if (!response.ok) throw new Error("Serverantwort war nicht OK");
            return response.json();
        })
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = "<p class='text-muted'>Keine Bestellungen gefunden.</p>";
                return;
            }

            let html = "";

            data.forEach((order, index) => {
                const produkte = (order.items || []).map(p =>
                    `<li>${p.produktname} – ${p.menge} x ${parseFloat(p.preis).toFixed(2)} €</li>`
                ).join("");

                html += `
                <div class="card mb-4 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Bestellung vom ${new Date(order.erstellt_am).toLocaleDateString('de-DE')}</h5>
                        <p><strong>Lieferadresse:</strong> ${order.lieferadresse || "-"}</p>
                        <p><strong>Rechnungsadresse:</strong> ${order.rechnungsadresse || "-"}</p>
                        <p><strong>Gutscheincode:</strong> ${order.gutscheincode || "-"}</p>
                        <p><strong>Rabatt:</strong> ${order.rabatt ?? 0}%</p>
                        <p><strong>Gesamtsumme:</strong> ${parseFloat(order.gesamt).toFixed(2)} €</p>
                        <p><strong>Produkte:</strong></p>
                        <ul>${produkte}</ul>
                        <button class="btn btn-primary mt-3" onclick="generateInvoice(${index})">Rechnung herunterladen</button>
                    </div>
                </div>
                `;
            });

            container.innerHTML = html;
            window.allOrders = data; // für Zugriff beim PDF-Export
        })
        .catch(error => {
            console.error("Fehler beim Laden der Bestellungen:", error);
            container.innerHTML = "<p class='text-danger'>Fehler beim Laden der Bestellungen.</p>";
        });
});

function generateInvoice(index) {
    const order = window.allOrders[index];
    const doc = new jspdf.jsPDF();

    let y = 20;
    doc.setFontSize(18);
    doc.text("Rechnung", 105, y, null, null, "center");

    y += 10;
    doc.setFontSize(12);
    doc.text(`Bestelldatum: ${new Date(order.erstellt_am).toLocaleDateString('de-DE')}`, 20, y);
    y += 8;
    doc.text(`Rechnungsadresse: ${order.rechnungsadresse || '-'}`, 20, y);
    y += 8;
    doc.text(`Gutscheincode: ${order.gutscheincode || '-'}`, 20, y);
    y += 8;
    doc.text(`Rabatt: ${order.rabatt ?? 0}%`, 20, y);
    y += 10;

    doc.text("Produkte:", 20, y);
    y += 8;
    order.items.forEach(p => {
        doc.text(`${p.produktname} - ${p.menge} x ${parseFloat(p.preis).toFixed(2)} €`, 25, y);
        y += 8;
    });

    y += 5;
    doc.setFontSize(14);
    doc.text(`Gesamt: ${parseFloat(order.gesamt).toFixed(2)} €`, 20, y);

    doc.save(`Rechnung_${order.id}.pdf`);
}

