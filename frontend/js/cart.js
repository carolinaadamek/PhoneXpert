document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("warenkorb-inhalt");

  // Warenkorb anzeigen
  function renderCart() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    if (cart.length === 0) {
      container.innerHTML = "<p>Dein Warenkorb ist leer.</p>";
      return;
    }

    let total = 0;
    let html = `
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Produkt</th>
            <th>Preis</th>
            <th>Menge</th>
            <th>Gesamt</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
    `;

    // Produkte im Warenkorb durchgehen
    cart.forEach((item, index) => {
      const itemTotal = parseFloat(item.price) * item.quantity;
      total += itemTotal;

      html += `
        <tr>
          <td>${item.name}</td>
          <td>${parseFloat(item.price).toFixed(2)} €</td>
          <td>
            <div class="d-flex align-items-center">
              <button class="btn btn-sm btn-outline-secondary btn-decrease me-2" data-index="${index}">–</button>
              <span>${item.quantity}</span>
              <button class="btn btn-sm btn-outline-secondary btn-increase ms-2" data-index="${index}">+</button>
            </div>
          </td>
          <td>${itemTotal.toFixed(2)} €</td>
          <td><button class="btn btn-danger btn-sm btn-remove" data-index="${index}">Entfernen</button></td>
        </tr>
      `;
    });

    html += `
        </tbody>
      </table>
      <div class="text-end fw-bold">Gesamtsumme: ${total.toFixed(2)} €</div>
      <div class="text-center mt-3">
        <button class="btn btn-secondary me-2" id="clear-cart">Warenkorb leeren</button>
        <a href="checkout.html" class="btn btn-primary">Jetzt bestellen</a>
      </div>
    `;

    container.innerHTML = html;
    addEventListeners();
  }

  // Buttons aktivieren: Menge ändern, löschen, leeren
  function addEventListeners() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    document.querySelectorAll(".btn-increase").forEach(btn => {
      btn.addEventListener("click", function () {
        const index = parseInt(this.dataset.index);
        cart[index].quantity++;
        localStorage.setItem("cart", JSON.stringify(cart));
        renderCart();
      });
    });

    document.querySelectorAll(".btn-decrease").forEach(btn => {
      btn.addEventListener("click", function () {
        const index = parseInt(this.dataset.index);
        if (cart[index].quantity > 1) {
          cart[index].quantity--;
          localStorage.setItem("cart", JSON.stringify(cart));
          renderCart();
        }
      });
    });

    document.querySelectorAll(".btn-remove").forEach(btn => {
      btn.addEventListener("click", function () {
        const index = parseInt(this.dataset.index);
        cart.splice(index, 1);
        localStorage.setItem("cart", JSON.stringify(cart));
        renderCart();
      });
    });

    // Kompletten Warenkorb leeren
    document.getElementById("clear-cart").addEventListener("click", function () {
      localStorage.removeItem("cart");
      renderCart();
    });
  }

  renderCart();
});
