$(document).ready(function () {
    ladeProdukte();

    function ladeProdukte() {
        $.getJSON("../../backend/products/get_all_products.php", function (produkte) {
            if (produkte.length === 0) {
                $("#produktListe").html("<p class='text-muted'>Keine Produkte vorhanden.</p>");
                return;
            }

            let html = `<table class="table table-striped"><thead>
        <tr><th>Name</th><th>Preis</th><th>Bild</th><th></th></tr>
      </thead><tbody>`;

            produkte.forEach(p => {
                html += `<tr>
          <td>${p.name}</td>
          <td>${parseFloat(p.preis).toFixed(2)} €</td>
          <td><img src="../img/${p.image_path}" style="height: 50px"></td>
          <td><button class="btn btn-sm btn-danger delete-btn" data-id="${p.id}">Löschen</button></td>
        </tr>`;
            });

            html += `</tbody></table>`;
            $("#produktListe").html(html);
        });
    }

    // Produkt löschen
    $(document).on("click", ".delete-btn", function () {
        const id = $(this).data("id");
        if (!confirm("Produkt wirklich löschen?")) return;

        $.ajax({
            url: "../../backend/products/delete_product.php",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ id }),
            success: function (res) {
                if (res.status === "success") {
                    ladeProdukte();
                } else {
                    alert("Fehler beim Löschen: " + (res.message || ""));
                }
            },
            error: function () {
                alert("Serverfehler beim Löschen.");
            }
        });
    });
});
