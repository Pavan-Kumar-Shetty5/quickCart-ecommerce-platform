// --- ADD TO CART ---
window.addToCart = function(id, name, price, image, buttonElement) {
    if (buttonElement) {
        buttonElement.innerText = "Added";
        buttonElement.style.backgroundColor = "#28a745"; 
        buttonElement.style.color = "white";
        //buttonElement.disabled = true; 
    }

    const quantitySelector = document.querySelector(`.js-quantity-select-${id}`);
    const quantity = quantitySelector ? parseInt(quantitySelector.value) : 1;

    // Send only the necessary database ID and quantity
    const newItem = { 
        id: id, 
        quantity: quantity 
    };

    fetch('../backend/cart-operation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'add', item: newItem })
    }).then(() => {
        renderCart();
    });
};

// --- RENDER CART ---
async function renderCart() {
    const container = document.querySelector('.js-cart-items-container');
    if (!container) return; 

    try {
        const response = await fetch('../backend/cart-operation.php');
        const cart = await response.json();

        // --- NEW SECURITY CHECK ---
        if (cart.status === "error") {
            // Redirect to login if the session expired or user isn't logged in
            alert("Please login to view your cart!");
            window.location.href = "../frontend/home.html";
            return;
        }
        // --------------------------

        let cartHTML = '';
        let totalMRP = 0; 
        let totalItems = 0;

        // Check if the cart is empty
        if (!cart || cart.length === 0) {
            container.innerHTML = `<div class="empty-cart" style="color:grey; padding:20px;">Your cart is empty!</div>`;
            updateSummary(0, 0);
            return;
        }

        cart.forEach((item) => {
            const itemPrice = Number(item.price) || 0;
            const itemQty = Number(item.quantity) || 1;
            const cartItemId = item.cart_id; 

            totalMRP += (itemPrice * itemQty);
            totalItems += itemQty;

            cartHTML += `
                <div class="cart-item-card">
                    <img src="../${item.image}" class="cart-item-img" style="width:80px">
                    <div class="cart-item-info">
                        <p class="cart-item-name"><strong>${item.productName}</strong></p>
                        <p class="cart-item-price">₹${itemPrice.toLocaleString('en-IN')}</p>
                        <div class="cart-quantity-controls">
                            <button onclick="updateQuantity(${cartItemId}, -1)">-</button>
                            <span style="color:black; margin:0 10px;">${itemQty}</span>
                            <button onclick="updateQuantity(${cartItemId}, 1)">+</button>
                        </div>
                        <button class="delete-btn" onclick="removeFromCart(${cartItemId})">Remove</button>
                    </div>
                </div><hr style="border: 0.5px solid #444;">`;
        });

        container.innerHTML = cartHTML;
        updateSummary(totalMRP, totalItems);

    } catch (error) {
        console.error("Error fetching or rendering cart:", error);
        container.innerHTML = `<div style="color:red; padding:20px;">Error connecting to server.</div>`;
    }
}

// --- UTILITIES ---
// --- UTILITIES ---
window.updateQuantity = async function(cartId, change) {
    try {
        const response = await fetch('../backend/cart-operation.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                action: 'update', 
                cart_id: cartId, 
                change: change 
            })
        });

        const result = await response.json();
        // Wait for success before refreshing the UI
        if (result.status === 'success') {
            renderCart(); 
        }
    } catch (error) {
        console.error("Update failed:", error);
    }
};

window.removeFromCart = async function(cartId) {
    if (!confirm("Remove this item?")) return;
    
    try {
        const response = await fetch('../backend/cart-operation.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'remove', cart_id: cartId })
        });

        const result = await response.json();
        if (result.status === 'success') {
            renderCart();
        }
    } catch (error) {
        console.error("Error removing item:", error);
    }
};

function updateSummary(total, count) {
    const mrpElement = document.querySelector('.js-total-mrp');
    const itemsCountElement = document.querySelector('.js-items-count');
    const finalTotalElement = document.querySelector('.js-final-total'); 
    
    const formattedPrice = total.toLocaleString('en-IN');

    if (mrpElement) mrpElement.innerText = `₹${formattedPrice}`;
    if (itemsCountElement) itemsCountElement.innerText = `Price (${count} items)`;
    if (finalTotalElement) finalTotalElement.innerText = `₹${formattedPrice}`;
}

// Initial load
renderCart();

async function proceedToCheckout() {
    try {
        const res = await fetch('../backend/get-profile.php');
        const data = await res.json();

        if (data.status === "success") {
            // If the orders table is empty for this user, data.address will be ""
            if (!data.address || data.address.trim() === "") {
                alert("No previous shipping address found. Please update your profile first.");
                window.location.href = "profile.html";
            } else {
                // If address exists in the orders table, go to checkout!
                window.location.href = "checkout.html";
            }
        } else {
            alert("Please login first.");
            window.location.href = "login.html";
        }
    } catch (error) {
        console.error("Error:", error);
    }
}