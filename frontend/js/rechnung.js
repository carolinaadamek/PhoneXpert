document.getElementById("downloadBtn").addEventListener("click", async function () {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  const logo = await getBase64FromUrl("../img/logo.png");

  // Logo + Titel
  doc.addImage(logo, "PNG", 10, 10, 30, 15);
  doc.setFontSize(18);
  doc.text("Rechnung", 105, 20, { align: "center" });

  const today = new Date().toLocaleDateString();
  doc.setFontSize(12);
  doc.text(`Datum: ${today}`, 150, 30);
  doc.text("PhoneXpert GmbH\nHochstädterplatz 6\n1200 Wien", 10, 40);

  // 📥 Daten abrufen
  $.getJSON("../../backend/orders/get_invoice.php", function (res) {
    if (!res.success) {
      alert("Fehler beim Laden der Rechnung");
      return;
    }

    const rows = res.items.map(item => [
      item.name,
      item.preis,
      item.menge,
      item.gesamt
    ]);

    doc.autoTable({
      startY: 60,
      head: [["Produkt", "Einzelpreis", "Menge", "Gesamt"]],
      body: rows,
      theme: "grid",
      styles: { halign: "center" },
      headStyles: { fillColor: [0, 171, 200] }
    });

    doc.setFontSize(14);
    doc.text(`Gesamtsumme: ${res.total}`, 200, doc.lastAutoTable.finalY + 10, { align: "right" });

    doc.save("rechnung.pdf");
  });
});

async function getBase64FromUrl(url) {
  const res = await fetch(url);
  const blob = await res.blob();
  return new Promise((resolve) => {
    const reader = new FileReader();
    reader.onloadend = () => resolve(reader.result);
    reader.readAsDataURL(blob);
  });
}
