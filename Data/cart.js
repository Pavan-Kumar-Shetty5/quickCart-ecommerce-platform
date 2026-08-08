
window.addToCart = function(name, price, image) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
  
    const safeID = name.replace(/\s+/g, '-');
    const quantitySelector = document.querySelector(`.js-quantity-select-${safeID}`);
    
    
    const quantity = quantitySelector ? parseInt(quantitySelector.value) : 1;

    let matchingItem = cart.find(item => item.productName === name);

    if (matchingItem) {
        matchingItem.quantity += quantity;
    } else {
        cart.push({
            productName: name,
            price: Number(price),
            image: image,
            quantity: quantity
        });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    alert("Added to cart!");
     
};


function renderCart() {
    const container = document.querySelector('.js-cart-items-container');
    if (!container) return; 

    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    let cartHTML = '';
    let totalMRP = 0;
    let totalItems = 0;

    if (cart.length === 0) {
        container.innerHTML = `<div class="empty-cart">Your cart is empty! <a href="user.html">Shop Now</a></div>`;
        updateSummary(0, 0);
        return;
    }

    cart.forEach((item, index) => {
        const itemPrice = Number(item.price) || 0;
        totalMRP += (itemPrice * item.quantity);
        totalItems += item.quantity;

        cartHTML += `
            <div class="cart-item-card">
                <img src="${item.image}" class="cart-item-img">
                <div class="cart-item-info">
                    <p class="cart-item-name">${item.productName}</p>
                    <p class="cart-item-price">₹${itemPrice.toLocaleString()}</p>
                    <div class="cart-quantity-controls">
                        <button onclick="updateQuantity(${index}, -1)">-</button>
                        <span>${item.quantity}</span>
                        <button onclick="updateQuantity(${index}, 1)">+</button>
                    </div>
                    <button class="delete-btn" onclick="removeFromCart(${index})">Remove</button>
                </div>
            </div>`;
    });

    container.innerHTML = cartHTML;
    updateSummary(totalMRP, totalItems);
}

// --- PART C: UTILITIES ---
window.updateQuantity = function(index, change) {
    let cart = JSON.parse(localStorage.getItem('cart'));
    cart[index].quantity += change;
    if (cart[index].quantity < 1) removeFromCart(index);
    else {
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
    }
};

window.removeFromCart = function(index) {
    let cart = JSON.parse(localStorage.getItem('cart'));
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
};

function updateSummary(total, count) {
    const mrp = document.querySelector('.js-total-mrp');
    const final = document.querySelector('.js-final-total');
    const items = document.querySelector('.js-items-count');

    if (mrp) mrp.innerText = `₹${total.toLocaleString()}`;
    if (final) final.innerText = `₹${total.toLocaleString()}`;
    if (items) items.innerText = `Price (${count} items)`;
}


renderCart();