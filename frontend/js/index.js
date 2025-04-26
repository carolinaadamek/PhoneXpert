$(document).ready(function() {

    // Funktion zum Produkte holen (Optionaler Suchbegriff)
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
                    const collapseId = "collapse_" + index;
                    html += `
                        <div class="col-md-4">
                            <div class="card shadow p-2 square-card d-flex flex-column justify-content-between">
                                <div class="preview-wrapper text-center" data-bs-toggle="collapse" data-bs-target="#${collapseId}" style="cursor: pointer;">
                                    <img src="../img/${p.image_path}" class="small-preview mb-2" alt="${p.name}">
                                    <h6 class="card-title small mb-1">${p.name}</h6>
                                    <p class="fw-bold text-primary small mb-0">${parseFloat(p.preis).toFixed(2)} €</p>
                                </div>
                                <div class="collapse" id="${collapseId}">
                                    <img src="../img/${p.image_path}" class="img-fluid border-top full-image" alt="${p.name}">
                                    <div class="card-body pt-2 pb-2 border-top">
                                        <p class="text-muted small">${p.beschreibung}</p>
                                    </div>
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

    // Seite lädt Produkte beim Start
    ladeProdukte();

    // Suchfeld Event
    $("#produktSuche").on("input", function() {
        const suchbegriff = $(this).val();
        ladeProdukte(suchbegriff);
    });

    // Collapse Handling
    $(document).on("show.bs.collapse", function(e) {
        $(e.target).closest(".card").find(".small-preview").hide();
    });

    $(document).on("hide.bs.collapse", function(e) {
        $(e.target).closest(".card").find(".small-preview").show();
    });

});

