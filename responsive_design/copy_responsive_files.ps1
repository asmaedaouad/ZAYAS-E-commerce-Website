# PowerShell script to copy responsive design files to the responsive_design folder

# CSS Files
$cssFiles = @(
    "public\css\header.css",
    "public\css\home.css",
    "public\css\shop.css",
    "public\css\product-card.css",
    "public\css\cart.css",
    "public\css\wishlist.css",
    "public\css\checkout.css",
    "public\css\account.css",
    "public\css\about.css",
    "public\css\contact.css",
    "public\css\order-details.css",
    "admin\assets\css\admin.css",
    "admin\assets\css\dashboard.css",
    "admin\assets\css\products.css",
    "admin\assets\css\customers.css",
    "admin\assets\css\orders.css",
    "admin\assets\css\delivery.css",
    "admin\assets\css\order-details.css",
    "admin\assets\css\profile.css",
    "delivery\public\css\style.css"
)

# JavaScript Files
$jsFiles = @(
    "public\js\header.js",
    "public\js\home.js",
    "public\js\shop.js",
    "public\js\cart.js",
    "public\js\about.js",
    "public\js\contact.js",
    "admin\assets\js\admin.js",
    "admin\assets\js\dashboard.js",
    "admin\assets\js\products.js",
    "admin\assets\js\delivery-personnel.js",
    "delivery\public\js\delivery.js"
)

# Copy CSS files
foreach ($file in $cssFiles) {
    if (Test-Path $file) {
        $destFile = "responsive_design\css\" + (Split-Path $file -Leaf)
        Copy-Item $file $destFile -Force
        Write-Host "Copied $file to $destFile"
    } else {
        Write-Host "Warning: $file not found"
    }
}

# Copy JavaScript files
foreach ($file in $jsFiles) {
    if (Test-Path $file) {
        $destFile = "responsive_design\js\" + (Split-Path $file -Leaf)
        Copy-Item $file $destFile -Force
        Write-Host "Copied $file to $destFile"
    } else {
        Write-Host "Warning: $file not found"
    }
}

Write-Host "All responsive design files have been copied to the responsive_design folder."
