// product-details.js

document.addEventListener('DOMContentLoaded', function() {
    // Get product ID from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const productId = parseInt(urlParams.get('id'));
    
    if (productId) {
        loadProductDetails(productId);
    } else {
        // Handle case where no product ID is provided
        displayError("Product not found");
    }
});

function loadProductDetails(productId) {
    fetch('data/products.json')
        .then(response => response.json())
        .then(data => {
            const product = data.products.find(p => p.id === productId);
            
            if (product) {
                populateProductDetails(product);
                loadRelatedProducts(product);
            } else {
                displayError("Product not found");
            }
        })
        .catch(error => {
            console.error('Error loading product:', error);
            displayError("Failed to load product details");
        });
}

function populateProductDetails(product) {
    // Set breadcrumb
    document.querySelector('.product-category').textContent = product.category;
    document.querySelector('.product-name').textContent = product.name;
    
    // Set main product info
    document.querySelector('.product-title').textContent = product.name;
    document.querySelector('.current-price').textContent = `$${product.price.toFixed(2)}`;
    
    // Set old price if available
    const oldPriceElement = document.querySelector('.old-price');
    if (product.oldPrice) {
        oldPriceElement.textContent = `$${product.oldPrice.toFixed(2)}`;
        oldPriceElement.style.display = 'inline';
    } else {
        oldPriceElement.style.display = 'none';
    }
    
    // Set rating
    const ratingElement = document.querySelector('.product-rating');
    ratingElement.innerHTML = generateStarRating(product.rating);
    
    // Set reviews count
    document.querySelector('.product-reviews').textContent = `(${product.reviews} reviews)`;
    
    // Set description
    document.querySelector('.product-description').textContent = product.description;
    
    // Set detailed description in tab
    document.querySelector('.product-description-content p').textContent = product.description;
    
    // Set SKU
    document.getElementById('product-sku').textContent = `ZAY-${product.id.toString().padStart(3, '0')}`;
    
    // Set tags
    document.getElementById('product-tags').textContent = product.tags.join(', ');
    
    // Set stock info
    const stockInfo = document.querySelector('.stock-info');
    if (product.quantity > 0) {
        stockInfo.textContent = `${product.quantity} in stock`;
    } else {
        stockInfo.textContent = 'Out of stock';
        document.querySelector('.add-to-cart').disabled = true;
        document.querySelector('.add-to-cart').textContent = 'Out of Stock';
        document.getElementById('out-of-stock-overlay').style.display = 'flex';
    }
    
    // Set new tag if applicable
    if (product.isNew) {
        document.getElementById('new-tag').style.display = 'block';
    }
    
    // Set main product image
    const mainImage = document.getElementById('main-product-image');
    mainImage.src = product.image;
    mainImage.alt = product.name;
    
    // Create thumbnails (using same image for simplicity, could use different angles in real app)
    const thumbnailContainer = document.querySelector('.thumbnail-container');
    thumbnailContainer.innerHTML = '';
    
    // Add main image as first thumbnail
    addThumbnail(product.image, thumbnailContainer, true);
    
    // For demo purposes, add 2 more thumbnails (in real app these would be different angles)
    for (let i = 0; i < 2; i++) {
        addThumbnail(product.image, thumbnailContainer);
    }
    
    // Set up color options
    const colorOptionsContainer = document.querySelector('.color-options');
    colorOptionsContainer.innerHTML = '';
    
    product.colors.forEach(color => {
        const colorOption = document.createElement('div');
        colorOption.className = 'color-option';
        colorOption.style.backgroundColor = color;
        colorOption.setAttribute('data-color', color);
        colorOption.setAttribute('title', color.charAt(0).toUpperCase() + color.slice(1));
        
        colorOption.addEventListener('click', function() {
            document.querySelectorAll('.color-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
        });
        
        colorOptionsContainer.appendChild(colorOption);
    });
    
    // Select first color by default
    if (colorOptionsContainer.firstChild) {
        colorOptionsContainer.firstChild.classList.add('selected');
    }
    
    // Set up size options
    const sizeOptionsContainer = document.querySelector('.size-options');
    sizeOptionsContainer.innerHTML = '';
    
    product.sizes.forEach(size => {
        const sizeOption = document.createElement('div');
        sizeOption.className = 'size-option';
        sizeOption.textContent = size;
        sizeOption.setAttribute('data-size', size);
        
        sizeOption.addEventListener('click', function() {
            document.querySelectorAll('.size-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
        });
        
        sizeOptionsContainer.appendChild(sizeOption);
    });
    
    // Select first size by default
    if (sizeOptionsContainer.firstChild) {
        sizeOptionsContainer.firstChild.classList.add('selected');
    }
    
    // Set up quantity controls
    setupQuantityControls(product.quantity);
    
    // Set up wishlist button
    setupWishlistButton();
    
    // Set up image click functionality
    setupImageClick();
}

function addThumbnail(imageSrc, container, isActive = false) {
    const thumbnail = document.createElement('img');
    thumbnail.src = imageSrc;
    thumbnail.alt = 'Product thumbnail';
    thumbnail.className = 'thumbnail' + (isActive ? ' active' : '');
    
    thumbnail.addEventListener('click', function() {
        // Update main image
        document.getElementById('main-product-image').src = this.src;
        
        // Update active thumbnail
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        this.classList.add('active');
    });
    
    container.appendChild(thumbnail);
}

function setupQuantityControls(maxQuantity) {
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.querySelector('.quantity-btn[data-action="decrease"]');
    const increaseBtn = document.querySelector('.quantity-btn[data-action="increase"]');
    
    decreaseBtn.addEventListener('click', function() {
        let quantity = parseInt(quantityInput.value);
        if (quantity > 1) {
            quantityInput.value = quantity - 1;
        }
    });
    
    increaseBtn.addEventListener('click', function() {
        let quantity = parseInt(quantityInput.value);
        if (quantity < maxQuantity) {
            quantityInput.value = quantity + 1;
        }
    });
}

function setupWishlistButton() {
    const wishlistBtn = document.querySelector('.wishlist-btn');
    
    wishlistBtn.addEventListener('click', function() {
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
}

function setupImageClick() {
    const mainImage = document.getElementById('main-product-image');
    
    mainImage.addEventListener('click', function() {
        // Simple zoom effect - in a real app you might want a lightbox
        this.style.transform = this.style.transform === 'scale(1.5)' ? 'scale(1)' : 'scale(1.5)';
    });
}

function loadRelatedProducts(currentProduct) {
    fetch('data/products.json')
        .then(response => response.json())
        .then(data => {
            // Filter products from same category, excluding current product
            const relatedProducts = data.products.filter(p => 
                p.category === currentProduct.category && p.id !== currentProduct.id
            );
            
            // If not enough from same category, get random products
            if (relatedProducts.length < 4) {
                const otherProducts = data.products.filter(p => 
                    p.category !== currentProduct.category && p.id !== currentProduct.id
                );
                relatedProducts.push(...otherProducts.slice(0, 4 - relatedProducts.length));
            }
            
            populateRelatedProducts(relatedProducts.slice(0, 4));
        })
        .catch(error => {
            console.error('Error loading related products:', error);
        });
}

function populateRelatedProducts(products) {
    const container = document.querySelector('.related-products-container');
    container.innerHTML = '';
    
    products.forEach(product => {
        const isOutOfStock = product.quantity === 0;
        
        const productCard = document.createElement('div');
        productCard.className = 'col-md-6 col-lg-3';
        productCard.innerHTML = `
            <div class="card related-product-card h-100 border-0">
                <div class="position-relative">
                    <img src="${product.image}" class="card-img-top related-product-img" alt="${product.name}">
                    ${isOutOfStock ? '<div class="out-of-stock-badge">Sold Out</div>' : ''}
                </div>
                <div class="card-body">
                    <h5 class="card-title related-product-title">${product.name}</h5>
                    <div class="related-product-price">
                        $${product.price.toFixed(2)}
                        ${product.oldPrice ? `<span class="related-product-old-price">$${product.oldPrice.toFixed(2)}</span>` : ''}
                    </div>
                    <a href="product-details.html?id=${product.id}" class="stretched-link"></a>
                </div>
            </div>
        `;
        
        container.appendChild(productCard);
    });
}

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

function displayError(message) {
    const mainContent = document.querySelector('main');
    mainContent.innerHTML = `
        <div class="container py-5">
            <div class="alert alert-danger text-center">
                <h4>${message}</h4>
                <p>Please try again later or return to our <a href="shop.html">shop</a>.</p>
            </div>
        </div>
    `;
}