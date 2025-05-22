$(document).ready(function () {
  console.log("Login-Skript aktiv");
 // Login-Formular absenden
  $("#loginForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
      url: "../../backend/auth/login.php",
      method: "POST",
      data: $(this).serialize(), // Formulardaten senden
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          $("#meldung").html(`<span class="text-success">${response.message}</span>`);
          setTimeout(() => window.location.href = "index.html", 1500);
        } else {
          $("#meldung").html(`<span class="text-danger">${response.message}</span>`);
        }
      },
      error: function () {
        $("#meldung").html(`<span class="text-danger">Ein Fehler ist aufgetreten.</span>`);
      }
    });
  });
});
