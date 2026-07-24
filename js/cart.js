// js/cart.js
// AJAX cart interactions for cart.php.
// Talks to server/cart_process.php, which returns JSON.
// Only runs on pages that actually have a #cartTable (i.e. cart.php).

document.addEventListener("DOMContentLoaded", function () {
  const cartTable = document.querySelector("#cartTable");
  if (!cartTable) return; // Not on the cart page, nothing to do

  const cartTotalEl = document.querySelector("#cartTotal");
  const cartCountEl = document.querySelector("#cartCount"); // navbar badge, if present on this page

  // ---------- Quantity change ----------
  cartTable.addEventListener("change", function (e) {
    if (!e.target.classList.contains("qty-input")) return;

    const row = e.target.closest("tr");
    const cartId = row.dataset.cartId;
    let quantity = parseInt(e.target.value, 10);

    if (isNaN(quantity) || quantity < 1) {
      quantity = 1;
      e.target.value = 1;
    }

    updateCartItem(cartId, quantity, row);
  });

  // ---------- Remove item ----------
  cartTable.addEventListener("click", function (e) {
    if (!e.target.classList.contains("remove-btn")) return;

    const row = e.target.closest("tr");
    const cartId = row.dataset.cartId;

    if (!confirm("Remove this item from your cart?")) return;

    removeCartItem(cartId, row);
  });

  // ---------- Server calls ----------
  function updateCartItem(cartId, quantity, row) {
    fetch("server/cart_process.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `action=update&cart_id=${encodeURIComponent(cartId)}&quantity=${encodeURIComponent(quantity)}`,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          const subtotalEl = row.querySelector(".row-subtotal");
          if (subtotalEl) subtotalEl.textContent = data.rowSubtotal;
          refreshTotals(data);
        } else {
          alert(data.message || "Could not update cart. Please try again.");
        }
      })
      .catch(() => {
        alert("Network error. Please try again.");
      });
  }

  function removeCartItem(cartId, row) {
    fetch("server/cart_process.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `action=remove&cart_id=${encodeURIComponent(cartId)}`,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          row.remove();
          refreshTotals(data);

          if (data.itemCount === 0) {
            cartTable.closest(".cart-wrapper").innerHTML =
              "<p>Your cart is empty. <a href='products.php'>Browse products</a>.</p>";
          }
        } else {
          alert(data.message || "Could not remove item. Please try again.");
        }
      })
      .catch(() => {
        alert("Network error. Please try again.");
      });
  }

  function refreshTotals(data) {
    if (cartTotalEl && typeof data.cartTotal !== "undefined") {
      cartTotalEl.textContent = data.cartTotal;
    }
    if (cartCountEl && typeof data.itemCount !== "undefined") {
      cartCountEl.textContent = data.itemCount;
    }
  }
});