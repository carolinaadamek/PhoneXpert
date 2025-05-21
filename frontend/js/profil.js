$(document).ready(function () {
    // Profildaten laden
    $.getJSON("../../backend/auth/get_user_data.php", function (response) {
        if (response.status === "success") {
            const d = response.data;
            $("#vorname").val(d.vorname);
            $("#nachname").val(d.nachname);
            $("#username").val(d.username);
            $("#email").val(d.email);
        } else {
            $("#meldung").html(`<span class="text-danger">${response.message}</span>`);
        }
    });

    // Profil speichern
    $("#profilForm").on("submit", function (e) {
        e.preventDefault();
        const data = {
            vorname: $("#vorname").val(),
            nachname: $("#nachname").val(),
            username: $("#username").val(),
            email: $("#email").val()
        };

        $.ajax({
            url: "../../backend/auth/update_user_data.php",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(data),
            dataType: "json",
            success: function (response) {
                $("#meldung").html(`<span class="${response.status === 'success' ? 'text-success' : 'text-danger'}">${response.message}</span>`);
            },
            error: function () {
                $("#meldung").html(`<span class="text-danger">Fehler beim Speichern.</span>`);
            }
        });
    });

    // Passwort ändern
    $("#pwForm").on("submit", function (e) {
        e.preventDefault();

        const oldPw = $("#old_pw").val();
        const newPw = $("#new_pw").val();
        const repeatPw = $("#repeat_pw").val();

        if (newPw !== repeatPw) {
            $("#pw_meldung").html(`<span class="text-danger">Die neuen Passwörter stimmen nicht überein.</span>`);
            return;
        }

        $.ajax({
            url: "../../backend/auth/change_password.php",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                old_password: oldPw,
                new_password: newPw
            }),
            dataType: "json",
            success: function (response) {
                $("#pw_meldung").html(`<span class="${response.status === 'success' ? 'text-success' : 'text-danger'}">${response.message}</span>`);
                if (response.status === "success") {
                    $("#pwForm")[0].reset();
                }
            },
            error: function () {
                $("#pw_meldung").html(`<span class="text-danger">Fehler beim Ändern des Passworts.</span>`);
            }
        });
    });
});
