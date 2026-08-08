document.addEventListener("DOMContentLoaded", function() {
    loadCheckoutData();
});

// 1. READ address/phone from the database
async function loadCheckoutData() {
    const res = await fetch('../backend/get-profile.php');
    const data = await res.json();

    const addressDiv = document.getElementById('address-info');
    const paymentSection = document.getElementById('payment-section'); // Get the hidden section
    
    if (!data.address || data.address === "") {
        alert("Please update your shipping details in your profile first!");
        window.location.href = "profile.html";
        return;
    }

    // 1. Display the info to the user
    addressDiv.innerHTML = `
        <p><strong>Deliver to:</strong> ${data.username}</p>
        <p><strong>Phone:</strong> ${data.phone}</p>
        <p><strong>Address:</strong> ${data.address}, ${data.pincode}</p>
    `;

    // 2. IMPORTANT: Show the payment section and confirm button
    if (paymentSection) {
        paymentSection.style.display = "block";
    }
}

// 2. READ Product ID & Qty and STORE in DB
async function placeOrder() {
    try {
        // 1. Fetch the actual cart from the database first
        const cartRes = await fetch('../backend/cart-operation.php');
        const cartItems = await cartRes.json();

        // 2. Check if the cart is empty
        if (!cartItems || cartItems.length === 0 || cartItems.status === "error") {
            alert("Your cart is empty! Add items before confirming.");
            return;
        }

        // 3. Get payment method
        const paymentElement = document.querySelector('input[name="payment"]:checked');
        if (!paymentElement) {
            alert("Please select a payment method.");
            return;
        }

        // 4. Send the order data to the backend
        const orderData = {
            payment_method: paymentElement.value,
            cart_items: cartItems // Now sending the database results
        };

        const response = await fetch('../backend/confirm-order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderData)
        });

        const result = await response.json();

        if (result.status === "success") {
            alert("Order Placed Successfully!");
            // Optional: You might need a 'clear-cart' action in cart-operation.php here
            window.location.href = "user.html";
        } else {
            alert("Error: " + result.message);
        }

    } catch (error) {
        console.error("Order Error:", error);
        alert("Failed to place order. Check console for details.");
    }
}