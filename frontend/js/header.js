$(document).ready(function () {
  $("#header").load("../components/header.html", function () {
    $.get("../../backend/auth/check_login.php", function (response) {
      let html = "";
// Wenn eingeloggt: Buttons je nach Rolle anzeigen
      if (response.loggedIn) {
        html += `<a href="cart.html" class="btn btn-outline-primary">Warenkorb</a>`;
        html += `<a href="profil.html" class="btn btn-outline-primary">Profil</a>`;

        if (response.typ === "kunde") {
          html += `<a href="kunden_orders.html" class="btn btn-primary">Bestellungen</a>`;
        } else if (response.typ === "admin") {
          html += `<a href="admin_dashboard.html" class="btn btn-primary">Admin</a>`;
        }
 // Wenn nicht eingeloggt: Login/Register anzeigen
        html += `
          <span class="ms-3 me-2 fw-semibold text-dark">Hallo, ${response.vorname}</span>
          <a href="../../backend/auth/logout.php" class="btn btn-outline-secondary">Logout</a>
        `;
      } else {
        html += `
          <a href="login.html" class="btn btn-outline-primary">Login</a>
          <a href="register.html" class="btn btn-primary">Registrieren</a>
        `;
      }
// Inhalte in die Navbar einfügen
      $("#nav-area").html(html);
    }, "json");
  });
});
