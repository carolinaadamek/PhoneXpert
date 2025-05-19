$(document).ready(function () {

    // Gutscheincode generieren
    $("#voucherForm").on("submit", function (e) {
        e.preventDefault();

        const percent = $("#percent").val();
        const expires = $("#expires").val();

        $.ajax({
            url: "../../backend/auth/create_voucher.php",
            method: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({ percent, expires }),
            success: function (res) {
                if (res.success) {
                    $("#result").html(`<div class="alert alert-success">Code erstellt: <strong>${res.code}</strong></div>`);
                    $("#voucherForm")[0].reset();
                    ladeVouchers(); // Liste neu laden
                } else {
                    $("#result").html(`<div class="alert alert-danger">${res.message}</div>`);
                }
            },
            error: function () {
                $("#result").html(`<div class="alert alert-danger">Serverfehler beim Erstellen.</div>`);
            }
        });
    });

    // Alle Gutscheine laden und anzeigen
    function ladeVouchers() {
        $.getJSON("../../backend/auth/get_vouchers.php", function (res) {
            if (res.success) {
                const list = res.vouchers;
                let html = `<table class="table table-striped table-bordered">
          <thead>
            <tr><th>Code</th><th>Rabatt</th><th>Gültig bis</th><th>Status</th><th>Erstellt am</th></tr>
          </thead><tbody>`;

                list.forEach(v => {
                    html += `<tr>
            <td>${v.code}</td>
            <td>${v.prozent}%</td>
            <td>${v.gueltig_bis || '-'}</td>
            <td>${v.aktiv ? "🟢 aktiv" : "🔴 inaktiv"}</td>
            <td>${v.erstellt_am}</td>
          </tr>`;
                });

                html += `</tbody></table>`;
                $("#voucherTable").html(html);
            } else {
                $("#voucherTable").html(`<div class="text-danger">Konnte Gutscheine nicht laden.</div>`);
            }
        }).fail(() => {
            $("#voucherTable").html(`<div class="text-danger">Serverfehler beim Laden der Gutscheine.</div>`);
        });
    }

    // Initial laden
    ladeVouchers();
});
