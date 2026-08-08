
const searchInput = document.querySelector('.search-product');
const productGrid = document.querySelector('.product-grid');


function renderProducts(searchTerm = '') {
    const urlParams = new URLSearchParams(window.location.search);
    const categoryFilter = urlParams.get('category'); 
    const urlSearch = urlParams.get('search') || '';

  
    const finalSearch = (searchTerm || urlSearch).toLowerCase();

    let html = '';

  
    Object.entries(products).forEach(([categoryName, items]) => {
        
        
        if (!categoryFilter || categoryFilter === categoryName) {
            
            items.forEach((product) => {
               
                const matchesSearch = product.name.toLowerCase().includes(finalSearch);

                if (matchesSearch) {
                    let path = product.image;
                  
                    const safeID = product.name.replace(/\s+/g, '-');

                    html += `
                        <div class="product-container">
                            <div class="product-image-container">
                                <img class="product-image" src="${path}" alt="${product.name}">
                            </div>
                            <div class="product-name"><p>${product.name}</p></div>
                            <div class="product-price">₹${product.price}</div>
                            
                            <div class="product-quantity-container">
                                <select class="js-quantity-select-${safeID}">
                                    ${[1,2,3,4,5,6,7,8,9,10].map(n => `<option value="${n}">${n}</option>`).join('')}
                                </select>
                            </div>

                            <button class="add-to-cart-button" 
                                onclick="addToCart('${product.name}', ${product.price}, '${path}')">
                                Add to cart
                            </button>
                        </div>
                    `;
                }
            });
        }
    });

    
    if (productGrid) {
        productGrid.innerHTML = html || `<div class="no-results" style="padding:20px;">No products found for "${finalSearch}"</div>`;
    }

   
    const heading = document.querySelector('.product-browsing .heading');
    if (heading) {
        if (finalSearch) heading.innerText = `Results for: "${finalSearch}"`;
        else if (categoryFilter) heading.innerText = `Top Picks in ${categoryFilter.toUpperCase()}`;
        else heading.innerText = "TOP PICKS FOR YOU";
    }
}


if (searchInput) {
    searchInput.addEventListener('input', () => {
        renderProducts(searchInput.value);
    });

    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            const query = searchInput.value.trim();
            
        }
    });
}

// 3. Run on Page Load
renderProducts();