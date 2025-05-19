$(document).ready(function() {

  // Produkte laden (optional mit Suchbegriff)
  function ladeProdukte(suchbegriff = "") {
    let url = "../../backend/products/get_products.php";
    if (suchbegriff !== "") {
      url = "../../backend/products/search_products.php?query=" + encodeURIComponent(suchbegriff);
    }

    $.ajax({
      url: url,
      method: "GET",
      dataType: "json",
      success: function(produkte) {
        if (produkte.length === 0) {
          $("#produktContainer").html(`<p class="text-muted">Keine Produkte gefunden.</p>`);
          return;
        }

        let html = "";
        produkte.forEach((p, index) => {
          const collapseId = `collapse_${index}`;
          html += `
            <div class="col-md-4">
              <div class="card shadow h-100 square-card">
                <div class="card-body text-center">
                  <img src="../img/${p.image_path}" class="card-img-top small-img mb-2" alt="${p.name}">
                  <h6 class="card-title mb-1">${p.name}</h6>
                  <p class="fw-bold text-primary mb-2">${parseFloat(p.preis).toFixed(2)} €</p>
                  <button class="btn btn-outline-primary btn-sm add-to-cart-btn mb-2"
                          data-id="${p.id}"
                          data-name="${p.name}"
                          data-price="${p.preis}">
                    In den Warenkorb
                  </button>
                  <div class="collapse mt-2" id="${collapseId}">
                    <img src="../img/${p.image_path}" class="img-fluid mb-2 big-img" alt="${p.name}">
                    <p class="text-muted">${p.beschreibung}</p>
                  </div>
                </div>
                <div class="card-footer text-center p-2">
                  <a href="#" class="text-decoration-none" data-bs-toggle="collapse" data-bs-target="#${collapseId}">
                    Details anzeigen
                  </a>
                </div>
              </div>
            </div>
          `;
        });

        $("#produktContainer").html(html);
      },
      error: function(xhr, status, error) {
        console.error("Fehler beim Laden der Produkte:", error);
        $("#produktContainer").html(`<p class="text-danger">Produkte konnten nicht geladen werden.</p>`);
      }
    });
  }

  // Initial laden
  ladeProdukte();

  // Live-Suche
  $("#produktSuche").on("input", function() {
    const suchbegriff = $(this).val();
    ladeProdukte(suchbegriff);
  });

  // Collapse: Bild bei Beschreibung ein-/ausblenden
  $(document).on("show.bs.collapse", function(e) {
    $(e.target).closest(".card").find(".small-img").hide();
  });

  $(document).on("hide.bs.collapse", function(e) {
    $(e.target).closest(".card").find(".small-img").show();
  });

  // In den Warenkorb legen
  $(document).on("click", ".add-to-cart-btn", function() {
    const id = $(this).data("id");
    const name = $(this).data("name");
    const price = $(this).data("price");

    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    const existing = cart.find(item => item.id === id);
    if (existing) {
      existing.quantity++;
    } else {
      cart.push({ id, name, price, quantity: 1 });
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    alert(`„${name}“ wurde dem Warenkorb hinzugefügt.`);
  });

});
