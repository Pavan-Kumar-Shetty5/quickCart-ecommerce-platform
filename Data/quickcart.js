const searchInput = document.querySelector('.search-product');
const productGrid = document.querySelector('.product-grid');

// 1. Make the function ASYNC so it can wait for the PHP response
async function renderProducts(searchTerm = '') {
    const urlParams = new URLSearchParams(window.location.search);
    const categoryFilter = urlParams.get('category'); 
    const urlSearch = urlParams.get('search') || '';
    const finalSearch = (searchTerm || urlSearch).toLowerCase();

    let html = '';

    try {
        // 1. UPDATED: Fetch BOTH products AND your personal cart data
        const [prodRes, cartRes] = await Promise.all([
            fetch('../backend/get-products.php'),
            fetch('../backend/cart-operation.php')
        ]);

        const products = await prodRes.json();
        const cartItems = await cartRes.json();

        products.forEach((product) => {
            if (categoryFilter && product.category_name !== categoryFilter) {
                return;
            }

            const matchesSearch = product.name.toLowerCase().includes(finalSearch);

            if (matchesSearch) {
                let path = `../${product.image_path || product.image}`;

                // 2. NEW LOGIC: Check if this specific product is already in the cartItems array
                const isInCart = Array.isArray(cartItems) && cartItems.some(item => item.productName === product.name);

                html += `
                    <div class="product-container">
                        <div class="product-image-container">
                            <img class="product-image" src="${path}" alt="${product.name}">
                        </div>
                        <div class="product-name"><p>${product.name}</p></div>
                        <div class="product-price">₹${Number(product.price).toLocaleString('en-IN')}</div>
                        
                        <div class="product-quantity-container">
                            <select class="js-quantity-select-${product.id}">
                                ${[1,2,3,4,5,6,7,8,9,10].map(n => `<option value="${n}">${n}</option>`).join('')}
                            </select>
                        </div>

                        <button class="add-to-cart-button"
                            data-name="${product.name}"
                            onclick="addToCart(${product.id}, '${product.name}', ${product.price}, '${path}', this)"
                            ${isInCart ? 'disabled style="background-color: #28a745; color:white"' :''}>
                            ${isInCart ? 'Added ' : 'Add to cart'}
                        </button>
                    </div>
                `;
            }
        });

        if (productGrid) {
            productGrid.innerHTML = html || `<div class="no-results" style="padding:20px;">No products found for "${finalSearch}"</div>`;
        }

        // Update Heading
        const heading = document.querySelector('.product-browsing .heading');
        if (heading) {
            if (finalSearch) heading.innerText = `Results for: "${finalSearch}"`;
            else if (categoryFilter) heading.innerText = `Top Picks in ${categoryFilter.toUpperCase()}`;
            else heading.innerText = "TOP PICKS FOR YOU";
        }

    } catch (error) {
        console.error("Error loading products from database:", error);
        if (productGrid) productGrid.innerHTML = "Error loading products.";
    }
}

// Event Listeners remain the same
if (searchInput) {
    searchInput.addEventListener('input', () => {
        renderProducts(searchInput.value);
    });
}

// 5. Initial Load
renderProducts();