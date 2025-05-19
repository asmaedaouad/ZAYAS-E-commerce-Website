/**
 * Password Toggle Visibility
 * Adds functionality to toggle password field visibility
 */
document.addEventListener('DOMContentLoaded', function() {
    // Find all password fields
    const passwordFields = document.querySelectorAll('input[type="password"]');

    // Add toggle button to each password field
    passwordFields.forEach(function(field) {
        // Create the toggle button
        const toggleButton = document.createElement('span');
        toggleButton.innerHTML = '<i class="fa-solid fa-eye"></i>';
        toggleButton.className = 'password-toggle-icon';
        toggleButton.style.display = 'none'; // Hide initially

        // Set inline styles for consistent positioning
        toggleButton.style.position = 'absolute';
        toggleButton.style.right = '10px';

        // Check if we're in the admin or delivery interface and adjust positioning
        const isAdmin = window.location.pathname.includes('/admin/');
        const isDelivery = window.location.pathname.includes('/delivery/');

        if (isAdmin) {
            toggleButton.style.top = '58%'; // Position lower for admin
            toggleButton.style.right = '15px'; // Adjust right position for admin
        } else if (isDelivery) {
            toggleButton.style.top = '58%'; // Position lower for delivery
            toggleButton.style.right = '15px'; // Adjust right position for delivery
        } else {
            toggleButton.style.top = '50%';
        }

        toggleButton.style.transform = 'translateY(-50%)';
        toggleButton.style.cursor = 'pointer';
        toggleButton.style.zIndex = '10';
        toggleButton.style.height = '20px';
        toggleButton.style.display = 'none';
        toggleButton.style.alignItems = 'center';

        // Make sure the parent container has position relative
        field.parentNode.style.position = 'relative';

        // Insert the toggle button directly after the input field
        field.insertAdjacentElement('afterend', toggleButton);

        // Adjust the position based on the input field
        const fieldRect = field.getBoundingClientRect();
        const fieldHeight = field.offsetHeight;

        // Show the toggle button when the field has content
        field.addEventListener('input', function() {
            toggleButton.style.display = field.value.length > 0 ? 'flex' : 'none';
        });

        // Check if field already has content (e.g., on page reload)
        if (field.value.length > 0) {
            toggleButton.style.display = 'flex';
        }

        // Add click event to toggle visibility
        toggleButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Toggle the password field type
            if (field.type === 'password') {
                field.type = 'text';
                toggleButton.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
            } else {
                field.type = 'password';
                toggleButton.innerHTML = '<i class="fa-solid fa-eye"></i>';
            }
        });
    });
});
