$(document).ready(function () {
    ladeBenutzer();

    function ladeBenutzer() {
        $.getJSON("../../backend/auth/get_all_users.php", function (users) {
            let html = `<table class="table table-striped"><thead>
        <tr><th>Name</th><th>Username</th><th>E-Mail</th><th>Rolle</th><th>Status</th><th></th></tr>
      </thead><tbody>`;
            users.forEach(user => {
                html += `<tr>
          <td>${user.vorname} ${user.nachname}</td>
          <td>${user.username}</td>
          <td>${user.email}</td>
          <td>${user.typ}</td>
          <td>${user.status}</td>
          <td><button class="btn btn-sm btn-primary edit-btn" data-user='${JSON.stringify(user)}'>Bearbeiten</button></td>
        </tr>`;
            });
            html += `</tbody></table>`;
            $("#userTabelle").html(html);
        });
    }

    $(document).on("click", ".edit-btn", function () {
        const user = $(this).data("user");
        $("#edit_id").val(user.id);
        $("#edit_vorname").val(user.vorname);
        $("#edit_nachname").val(user.nachname);
        $("#edit_username").val(user.username);
        $("#edit_email").val(user.email);
        $("#edit_typ").val(user.typ);
        $("#edit_status").val(user.status);
        $("#edit_meldung").html("");
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    });

    $("#editForm").on("submit", function (e) {
        e.preventDefault();
        const daten = {
            id: $("#edit_id").val(),
            vorname: $("#edit_vorname").val(),
            nachname: $("#edit_nachname").val(),
            username: $("#edit_username").val(),
            email: $("#edit_email").val(),
            typ: $("#edit_typ").val(),
            status: $("#edit_status").val()
        };

        $.ajax({
            url: "../../backend/auth/update_user_by_admin.php",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(daten),
            dataType: "json",
            success: function (res) {
                if (res.status === "success") {
                    $("#edit_meldung").html(`<span class="text-success">${res.message}</span>`);
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                        ladeBenutzer();
                    }, 1500);
                } else {
                    $("#edit_meldung").html(`<span class="text-danger">${res.message}</span>`);
                }
            },
            error: function () {
                $("#edit_meldung").html(`<span class="text-danger">Fehler beim Speichern</span>`);
            }
        });
    });
});
