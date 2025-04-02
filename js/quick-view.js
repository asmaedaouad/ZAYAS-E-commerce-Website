// Set up quick view modal with product data
function setupQuickViewModal(product) {
    // Set basic product info
    document.getElementById('modal-product-name').textContent = product.name;
    document.getElementById('modal-product-image').src = product.image;
    document.getElementById('modal-product-image').alt = product.name;
    document.getElementById('modal-product-price').textContent = `$${product.price.toFixed(2)}`;
    document.getElementById('modal-product-description').textContent = product.description;
    
    // Set rating
    const modalRating = document.getElementById('modal-product-rating');
    modalRating.innerHTML = generateStarRating(product.rating);
    
    // Set reviews
    document.getElementById('modal-product-reviews').textContent = `(${product.reviews} reviews)`;
    
    // Set old price if it exists
    const oldPriceElement = document.getElementById('modal-product-old-price');
    if (product.oldPrice) {
        oldPriceElement.textContent = `$${product.oldPrice.toFixed(2)}`;
        oldPriceElement.classList.remove('d-none');
    } else {
        oldPriceElement.textContent = '';
        oldPriceElement.classList.add('d-none');
    }
    
    // Generate color options
    setupColorOptions(product.colors);
    
    // Generate size options
    setupSizeOptions(product.sizes);
    
    // Set up quantity buttons
    setupQuantityControls(product.quantity);
    
    // Set up "See All Details" button
    setupDetailsButton(product.id);
}

function setupColorOptions(colors) {
    const colorsContainer = document.getElementById('modal-product-colors');
    colorsContainer.innerHTML = '';
    
    colors.forEach(color => {
        const colorElement = document.createElement('div');
        colorElement.className = 'modal-color-option';
        colorElement.style.backgroundColor = color;
        colorElement.setAttribute('data-color', color);
        colorElement.setAttribute('title', color.charAt(0).toUpperCase() + color.slice(1));
        
        colorElement.addEventListener('click', function() {
            document.querySelectorAll('.modal-color-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
        });
        
        colorsContainer.appendChild(colorElement);
    });
    
    // Select first color by default
    if (colorsContainer.firstChild) {
        colorsContainer.firstChild.classList.add('selected');
    }
}

function setupSizeOptions(sizes) {
    const sizesContainer = document.getElementById('modal-product-sizes');
    sizesContainer.innerHTML = '';
    
    sizes.forEach(size => {
        const sizeElement = document.createElement('div');
        sizeElement.className = 'modal-size-option';
        sizeElement.textContent = size;
        sizeElement.setAttribute('data-size', size);
        
        sizeElement.addEventListener('click', function() {
            document.querySelectorAll('.modal-size-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
        });
        
        sizesContainer.appendChild(sizeElement);
    });
    
    // Select first size by default
    if (sizesContainer.firstChild) {
        sizesContainer.firstChild.classList.add('selected');
    }
}

function setupQuantityControls(maxQuantity) {
    const quantityInput = document.getElementById('quantity');
    quantityInput.value = 1;
    
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            let quantity = parseInt(quantityInput.value);
            
            if (action === 'increase') {
                if (quantity < maxQuantity) {
                    quantityInput.value = quantity + 1;
                }
            } else if (action === 'decrease') {
                if (quantity > 1) {
                    quantityInput.value = quantity - 1;
                }
            }
        });
    });
}

function setupDetailsButton(productId) {
    const detailsBtn = document.getElementById('see-all-details-btn');
    if (detailsBtn) {
        // This will link to the product details page we'll create later
        detailsBtn.href = `product-details.html?id=${productId}`;
    }
}

// Generate star rating HTML (can be moved to a utilities file if used elsewhere)
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