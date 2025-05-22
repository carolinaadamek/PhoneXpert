// Formular für neues Produkt absenden
$("#productForm").on("submit", function(e) {
    e.preventDefault();
    let formData = new FormData(this); // Formulardaten inkl. Datei

    $.ajax({
        url: "../../backend/auth/create_product.php",
        method: "POST",
        data: formData,
        contentType: false, // wichtig bei FormData
        processData: false,
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                $("#meldung").html(`<span class="text-success">${response.message}</span>`);
                $("#productForm")[0].reset(); // Formular zurücksetzen
            } else {
                $("#meldung").html(`<span class="text-danger">${response.message}</span>`);
            }
        },
        error: function(xhr, status, error) {
            console.error("Fehlerdetails:", xhr.responseText);
            $("#meldung").html(`<span class="text-danger">Serverfehler: ${xhr.status} - ${xhr.statusText}</span>`);
        }
    });
});
