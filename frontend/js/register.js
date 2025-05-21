$(document).ready(function () {
  function checkPasswordsMatch() {
    const pass1 = $('#password').val();
    const pass2 = $('#password_repeat').val();
    if (pass1 !== pass2) {
      $('#passCheck').removeClass('d-none');
      return false;
    } else {
      $('#passCheck').addClass('d-none');
      return true;
    }
  }

  $("#password_repeat").on("keyup", checkPasswordsMatch);

  $("#registerForm").on("submit", function (e) {
    e.preventDefault();
    if (!checkPasswordsMatch()) return;

    $.ajax({
      url: "../../backend/auth/register.php",
      method: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        if (response.status === "error" && response.message.includes("Username")) {
          $("#userCheck").removeClass("d-none");
        } else {
          $("#userCheck").addClass("d-none");
        }

        $("#meldung").html(`<span class="${response.status === "success" ? 'text-success' : 'text-danger'}">${response.message}</span>`);

        if (response.status === "success") {
          setTimeout(() => window.location.href = "login.html", 1500);
        }
      },
      error: function () {
        $("#meldung").html(`<span class="text-danger">Ein Fehler ist aufgetreten.</span>`);
      }
    });
  });
});
