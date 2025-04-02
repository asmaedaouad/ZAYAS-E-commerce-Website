// Initialize variables
let allProducts = [];
let filteredProducts = [];
let activeFilters = {
    type: '',
    categories: [],
    colors: [],
    sizes: [],
    priceMax: 200,
    inStock: true,
    newArrivals: false,
    onSale: false
};

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Load products from JSON
    fetch('data/products.json')
        .then(response => response.json())
        .then(data => {
            allProducts = data.products;
            initializeFilters();
            setupEventListeners();
            
            // Check URL parameters for pre-filtered type
            const urlParams = new URLSearchParams(window.location.search);
            const typeParam = urlParams.get('type');
            
            if (typeParam && ['abaya', 'dress', 'hijab'].includes(typeParam)) {
                activeFilters.type = typeParam;
                updateCategoryBubbles(typeParam);
                updateCollectionTitle(typeParam);
                
                // Smooth scroll to products section
                setTimeout(() => {
                    document.querySelector('.products-section').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 300);
            }
            
            // Apply filters and render products
            applyFilters();
        })
        .catch(error => {
            console.error('Error loading products:', error);
            document.getElementById('products-container').innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                    <h4>Failed to load products</h4>
                    <p class="text-muted">Please try refreshing the page</p>
                </div>
            `;
        });
});

// Initialize filters based on available products
function initializeFilters() {
    generateCategoryFilters();
    generateColorFilters();
    generateSizeFilters();
    
    // Set up price range
    const priceRange = document.getElementById('price-range');
    const priceValue = document.getElementById('price-value');
    
    priceRange.addEventListener('input', function() {
        const value = this.value;
        priceValue.textContent = `$${value}`;
        activeFilters.priceMax = parseInt(value);
        applyFilters();
    });
}

// Set up all event listeners
function setupEventListeners() {
    // Category bubble clicks
    document.querySelectorAll('.category-bubble').forEach(bubble => {
        bubble.addEventListener('click', function(e) {
            e.preventDefault();
            const type = this.getAttribute('data-type');
            activeFilters.type = type;
            updateCategoryBubbles(type);
            updateCollectionTitle(type);
            applyFilters();
            
            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set('type', type);
            window.history.pushState({}, '', url);
        });
    });
    
    // Sort by change
    document.getElementById('sort-by').addEventListener('change', function() {
        applyFilters();
    });
    
    // Mobile filter button
    const mobileFilterBtn = document.getElementById('mobile-filter-btn');
    const filterSidebar = document.querySelector('.filter-sidebar');
    const filterOverlay = document.createElement('div');
    filterOverlay.className = 'filter-overlay';
    document.body.appendChild(filterOverlay);
    
    // Add close button to filter sidebar
    const closeBtn = document.createElement('button');
    closeBtn.className = 'btn-close filter-close-btn d-lg-none';
    closeBtn.setAttribute('aria-label', 'Close');
    filterSidebar.insertBefore(closeBtn, filterSidebar.firstChild);
    
    // Toggle filter sidebar
    mobileFilterBtn.addEventListener('click', () => {
        filterSidebar.classList.add('active');
        filterOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
    
    // Close filter sidebar
    closeBtn.addEventListener('click', closeFilter);
    filterOverlay.addEventListener('click', closeFilter);
    
    function closeFilter() {
        filterSidebar.classList.remove('active');
        filterOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // Clear all filters
    document.getElementById('clear-filters').addEventListener('click', function() {
        // Reset filter values
        activeFilters = {
            type: activeFilters.type, // Maintain the current type
            categories: [],
            colors: [],
            sizes: [],
            priceMax: 200,
            inStock: true, 
            newArrivals: false,
            onSale: false
        };
        
        // Reset UI elements
        document.getElementById('price-range').value = 200;
        document.getElementById('price-value').textContent = '$200';
        document.getElementById('in-stock').checked = true;
        document.getElementById('new-arrivals').checked = false;
        document.getElementById('on-sale').checked = false;
        
        // Reset checkbox selections
        document.querySelectorAll('#category-filters input').forEach(input => {
            input.checked = false;
        });
        
        // Reset color selections
        document.querySelectorAll('.color-option').forEach(color => {
            color.classList.remove('selected');
        });
        
        // Reset size selections
        document.querySelectorAll('.size-option').forEach(size => {
            size.classList.remove('selected');
        });
        
        applyFilters();
    });
    
    // Availability filters
    document.getElementById('in-stock').addEventListener('change', function() {
        activeFilters.inStock = this.checked;
        applyFilters();
    });
    
    document.getElementById('new-arrivals').addEventListener('change', function() {
        activeFilters.newArrivals = this.checked;
        applyFilters();
    });
    
    document.getElementById('on-sale').addEventListener('change', function() {
        activeFilters.onSale = this.checked;
        applyFilters();
    });
}

// Generate category filter options
function generateCategoryFilters() {
    const categoryFiltersContainer = document.getElementById('category-filters');
    const categories = {};
    
    // Count products in each category
    allProducts.forEach(product => {
        if (!categories[product.category]) {
            categories[product.category] = 0;
        }
        categories[product.category]++;
    });
    
    // Generate HTML
    let categoryFiltersHTML = '';
    Object.keys(categories).sort().forEach(category => {
        categoryFiltersHTML += `
            <div class="form-check">
                <input class="form-check-input category-filter" type="checkbox" id="category-${category.toLowerCase()}" 
                    data-category="${category}">
                <label class="form-check-label" for="category-${category.toLowerCase()}">
                    ${category} (${categories[category]})
                </label>
            </div>
        `;
    });
    
    categoryFiltersContainer.innerHTML = categoryFiltersHTML;
    
    // Add event listeners
    document.querySelectorAll('.category-filter').forEach(filter => {
        filter.addEventListener('change', function() {
            const category = this.getAttribute('data-category');
            
            if (this.checked) {
                if (!activeFilters.categories.includes(category)) {
                    activeFilters.categories.push(category);
                }
            } else {
                activeFilters.categories = activeFilters.categories.filter(c => c !== category);
            }
            
            applyFilters();
        });
    });
}

// Generate color filter options
function generateColorFilters() {
    const colorFiltersContainer = document.getElementById('color-filters');
    const colors = new Set();
    
    // Collect unique colors
    allProducts.forEach(product => {
        product.colors.forEach(color => {
            colors.add(color);
        });
    });
    
    // Generate HTML
    let colorFiltersHTML = '';
    Array.from(colors).sort().forEach(color => {
        colorFiltersHTML += `
            <div class="color-option" data-color="${color}" 
                 style="background-color: ${color};" 
                 title="${color.charAt(0).toUpperCase() + color.slice(1)}">
            </div>
        `;
    });
    
    colorFiltersContainer.innerHTML = colorFiltersHTML;
    
    // Add event listeners
    document.querySelectorAll('.color-option').forEach(colorOption => {
        colorOption.addEventListener('click', function() {
            const color = this.getAttribute('data-color');
            this.classList.toggle('selected');
            
            if (this.classList.contains('selected')) {
                if (!activeFilters.colors.includes(color)) {
                    activeFilters.colors.push(color);
                }
            } else {
                activeFilters.colors = activeFilters.colors.filter(c => c !== color);
            }
            
            applyFilters();
        });
    });
}

// Generate size filter options
function generateSizeFilters() {
    const sizeFiltersContainer = document.getElementById('size-filters');
    const sizes = new Set();
    
    // Collect unique sizes
    allProducts.forEach(product => {
        product.sizes.forEach(size => {
            sizes.add(size);
        });
    });
    
    // Generate HTML
    let sizeFiltersHTML = '';
    Array.from(sizes).sort().forEach(size => {
        sizeFiltersHTML += `
            <div class="size-option" data-size="${size}">${size}</div>
        `;
    });
    
    sizeFiltersContainer.innerHTML = sizeFiltersHTML;
    
    // Add event listeners
    document.querySelectorAll('.size-option').forEach(sizeOption => {
        sizeOption.addEventListener('click', function() {
            const size = this.getAttribute('data-size');
            this.classList.toggle('selected');
            
            if (this.classList.contains('selected')) {
                if (!activeFilters.sizes.includes(size)) {
                    activeFilters.sizes.push(size);
                }
            } else {
                activeFilters.sizes = activeFilters.sizes.filter(s => s !== size);
            }
            
            applyFilters();
        });
    });
}

// Apply all active filters and render products
function applyFilters() {
    // Start with all products
    filteredProducts = [...allProducts];
    
    // Filter by type if selected
    if (activeFilters.type) {
        filteredProducts = filteredProducts.filter(product => 
            product.type === activeFilters.type
        );
    }
    
    // Filter by categories if any selected
    if (activeFilters.categories.length > 0) {
        filteredProducts = filteredProducts.filter(product => 
            activeFilters.categories.includes(product.category)
        );
    }
    
    // Filter by colors if any selected
    if (activeFilters.colors.length > 0) {
        filteredProducts = filteredProducts.filter(product => 
            product.colors.some(color => activeFilters.colors.includes(color))
        );
    }
    
    // Filter by sizes if any selected
    if (activeFilters.sizes.length > 0) {
        filteredProducts = filteredProducts.filter(product => 
            product.sizes.some(size => activeFilters.sizes.includes(size))
        );
    }
    
    // Filter by price
    filteredProducts = filteredProducts.filter(product => 
        product.price <= activeFilters.priceMax
    );
    
    // Filter in-stock products
    if (activeFilters.inStock) {
        filteredProducts = filteredProducts.filter(product => 
            product.quantity > 0
        );
    }
    
    // Filter new arrivals
    if (activeFilters.newArrivals) {
        filteredProducts = filteredProducts.filter(product => 
            product.isNew
        );
    }
    
    // Filter on-sale products
    if (activeFilters.onSale) {
        filteredProducts = filteredProducts.filter(product => 
            product.oldPrice !== undefined
        );
    }
    
    // Sort products
    sortProducts();
    
    // Render the filtered products
    renderProducts();
}

// Sort products based on the selected sort option
function sortProducts() {
    const sortBy = document.getElementById('sort-by').value;
    
    switch (sortBy) {
        case 'price-low-high':
            filteredProducts.sort((a, b) => a.price - b.price);
            break;
        case 'price-high-low':
            filteredProducts.sort((a, b) => b.price - a.price);
            break;
        case 'newest':
            filteredProducts.sort((a, b) => {
                if (a.isNew && !b.isNew) return -1;
                if (!a.isNew && b.isNew) return 1;
                return 0;
            });
            break;
        case 'rating':
            filteredProducts.sort((a, b) => b.rating - a.rating);
            break;
        case 'featured':
        default:
            // Default sort (id order for now)
            filteredProducts.sort((a, b) => a.id - b.id);
    }
}

// Render product cards to the container
function renderProducts() {
    const productsContainer = document.getElementById('products-container');
    const emptyState = document.getElementById('empty-state');
    const productsCount = document.getElementById('products-count');
    
    // Update products count
    productsCount.textContent = filteredProducts.length;
    
    // Show empty state if no products
    if (filteredProducts.length === 0) {
        productsContainer.innerHTML = '';
        emptyState.classList.remove('d-none');
        return;
    }
    
    // Hide empty state and render products
    emptyState.classList.add('d-none');
    
    // Generate HTML for all products
    let productsHTML = '';
    filteredProducts.forEach(product => {
        productsHTML += createProductCard(product);
    });
    
    productsContainer.innerHTML = productsHTML;
    
    // Set up product card event listeners
    setupProductCards();
}

// Create a single product card HTML
function createProductCard(product) {
    const starRating = generateStarRating(product.rating);
    const isOutOfStock = product.quantity === 0;
    
    return `
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="product-card" data-product-id="${product.id}">
                <div class="product-image">
                    <img src="${product.image}" alt="${product.name}" loading="lazy">
                    
                    <div class="product-badges">
                        ${product.isNew ? '<span class="badge badge-new">New</span>' : ''}
                        ${product.oldPrice ? '<span class="badge badge-sale">Sale</span>' : ''}
                        ${isOutOfStock ? '<span class="badge badge-sold-out">Sold Out</span>' : ''}
                    </div>
                    
                    <button class="wishlist-btn" type="button">
                        <i class="far fa-heart"></i>
                    </button>
                    
                    <button class="quick-view-btn" type="button" data-bs-toggle="modal" data-bs-target="#quickViewModal" 
                            data-product-id="${product.id}">
                        Quick View
                    </button>
                </div>
                
                <div class="product-info">
                    <div class="product-category">${product.category}</div>
                    <h3 class="product-title">${product.name}</h3>
                    
                    <div class="product-rating">
                        <span class="rating-stars">${starRating}</span>
                        <span class="rating-text">(${product.reviews})</span>
                    </div>
                    
                    <div class="product-price">
                        <span class="price">$${product.price.toFixed(2)}</span>
                        ${product.oldPrice ? `<span class="old-price">$${product.oldPrice.toFixed(2)}</span>` : ''}
                    </div>
                    
                    <button class="btn btn-dark w-100 ${isOutOfStock ? 'disabled' : ''}" type="button">
                        ${isOutOfStock ? 'Out of Stock' : 'Add to Cart'}
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Generate star rating HTML
function generateStarRating(rating) {
    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
    
    let stars = '';
    
    // Add full stars
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star"></i>';
    }
    
    // Add half star if needed
    if (halfStar) {
        stars += '<i class="fas fa-star-half-alt"></i>';
    }
    
    // Add empty stars
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="far fa-star"></i>';
    }
    
    return stars;
}

// Set up event listeners for product cards
function setupProductCards() {
    // Quick view buttons
    document.querySelectorAll('.quick-view-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = parseInt(this.getAttribute('data-product-id'));
            const product = allProducts.find(p => p.id === productId);
            setupQuickViewModal(product);
        });
    });
    
    // Wishlist buttons
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            if (this.classList.contains('active')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        });
    });
}

// Update category bubbles to highlight active category
function updateCategoryBubbles(activeType) {
    document.querySelectorAll('.category-bubble').forEach(bubble => {
        const type = bubble.getAttribute('data-type');
        if (type === activeType) {
            bubble.classList.add('active');
        } else {
            bubble.classList.remove('active');
        }
    });
}

// Update the collection title and description based on the selected type
function updateCollectionTitle(type) {
    const title = document.getElementById('collection-title');
    const description = document.getElementById('collection-description');
    
    if (!type) {
        title.textContent = 'Shop';
        description.textContent = 'Discover our complete collection of premium Islamic fashion';
        return;
    }
    
    // Capitalize the first letter of the type
    const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
    
    // Set the title and description based on the type
    title.textContent = capitalizedType;
    
    switch (type) {
        case 'abaya':
            description.textContent = 'Elegant abayas for every occasion';
            break;
        case 'dress':
            description.textContent = 'Beautiful dresses for your special moments';
            break;
        case 'hijab':
            description.textContent = 'Premium quality hijabs in various styles';
            break;
        default:
            description.textContent = 'Discover our curated selection';
    }
}

// Quick View Modal Setup (basic implementation)
function setupQuickViewModal(product) {
    if (!product) return;
    
    // Update modal content
    document.getElementById('modal-product-image').src = product.image;
    document.getElementById('modal-product-name').textContent = product.name;
    document.getElementById('modal-product-rating').innerHTML = generateStarRating(product.rating);
    document.getElementById('modal-product-reviews').textContent = `(${product.reviews} reviews)`;
    document.getElementById('modal-product-price').textContent = `$${product.price.toFixed(2)}`;
    
    if (product.oldPrice) {
        document.getElementById('modal-product-old-price').textContent = `$${product.oldPrice.toFixed(2)}`;
        document.getElementById('modal-product-old-price').style.display = 'inline';
    } else {
        document.getElementById('modal-product-old-price').style.display = 'none';
    }
    
    document.getElementById('modal-product-description').textContent = product.description || 'No description available';
    
    // Update colors
    const colorsContainer = document.getElementById('modal-product-colors');
    colorsContainer.innerHTML = '';
    product.colors.forEach(color => {
        const colorElement = document.createElement('div');
        colorElement.className = 'color-circle';
        colorElement.style.backgroundColor = color;
        colorElement.title = color.charAt(0).toUpperCase() + color.slice(1);
        colorsContainer.appendChild(colorElement);
    });
    
    // Update sizes
    const sizesContainer = document.getElementById('modal-product-sizes');
    sizesContainer.innerHTML = '';
    product.sizes.forEach(size => {
        const sizeElement = document.createElement('div');
        sizeElement.className = 'size-option';
        sizeElement.textContent = size;
        sizesContainer.appendChild(sizeElement);
    });
    
    // Set up quantity buttons
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const quantityInput = document.getElementById('quantity');
            let quantity = parseInt(quantityInput.value);
            
            if (action === 'increase') {
                quantity++;
            } else if (action === 'decrease' && quantity > 1) {
                quantity--;
            }
            
            quantityInput.value = quantity;
        });
    });
    
    // Set up "See All Details" button
    document.getElementById('see-all-details-btn').href = `product-details.html?id=${product.id}`;
}