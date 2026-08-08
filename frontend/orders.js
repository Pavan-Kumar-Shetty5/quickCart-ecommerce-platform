document.addEventListener("DOMContentLoaded", function() {
    fetchOrders();
});

async function fetchOrders() {
    try {
        const response = await fetch('../backend/get-orders.php');
        const result = await response.json();

        const container = document.getElementById('orders-list');
        container.innerHTML = ''; 

        if (result.status === "success" && result.orders.length > 0) {
            result.orders.forEach(order => {
                // Fixed the variable name from 'orders' to 'order'
                const orderCard = `
                    <div class="order-card">
                        <div class="order-header">
                           
                            <span class="order-date"><i class="fa fa-calendar"></i> ${order.order_date}</span>
                        </div>
                        <div class="order-details">
                            <div class="order-body" style="display: flex; gap: 15px; align-items: center;">
            <div class="order-img">
    <img src="../${order.image_path}" 
         alt="Product" 
         style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
</div>
                            <p><strong>Amount:</strong> ₹${order.total_amount}</p>
                            <p><strong>Status:</strong> <span class="status-tag">${order.status || 'Processing'}</span></p>
                        </div>
                        <div class="shipping-info">
                            <p><i class="fa fa-map-marker-alt"></i> ${order.address}, ${order.pincode}</p>
                        </div>
                    </div>
                `;
                container.innerHTML += orderCard;
            });
        } else {
            container.innerHTML = '<div class="no-orders">No orders found. Start shopping!</div>';
        }
    } catch (error) {
        console.error("Order Fetch Error:", error);
        document.getElementById('orders-list').innerHTML = '<p>Error loading orders.</p>';
    }
}