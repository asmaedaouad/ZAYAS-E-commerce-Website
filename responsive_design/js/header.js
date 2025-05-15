// Function to confirm logout
function confirmLogout(logoutUrl) {
    if (confirm('Are you sure you want to log out?')) {
        window.location.href = logoutUrl;
    }
}

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    const menuBackdrop = document.getElementById('menuBackdrop');
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

    // Search elements - get all search toggles (mobile and desktop)
    const searchToggles = document.querySelectorAll('.search-toggle');
    const searchInputContainers = document.querySelectorAll('.search-input-container');
    const searchInputs = document.querySelectorAll('.search-input-container input');
    const closeSearchButtons = document.querySelectorAll('.close-search');

    // Toggle menu when navbar toggler is clicked
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            // Close search if open
            searchInputContainers.forEach(container => {
                container.classList.remove('active');
            });

            navbarCollapse.classList.toggle('show');
            menuBackdrop.classList.toggle('show');
            document.body.classList.toggle('menu-open');
        });
    }

    // Close menu when backdrop is clicked
    if (menuBackdrop) {
        menuBackdrop.addEventListener('click', function() {
            navbarCollapse.classList.remove('show');
            menuBackdrop.classList.remove('show');
            document.body.classList.remove('menu-open');
        });
    }

    // Close menu when close button is clicked
    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', function() {
            navbarCollapse.classList.remove('show');
            menuBackdrop.classList.remove('show');
            document.body.classList.remove('menu-open');
        });
    }

    // Handle dropdown menus on mobile
    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            // Only prevent default for mobile view
            if (window.innerWidth < 992) {
                e.preventDefault();
                const dropdownMenu = this.nextElementSibling;
                const expanded = this.getAttribute('aria-expanded') === 'true';

                // Close all other dropdowns
                dropdownToggles.forEach(function(otherToggle) {
                    if (otherToggle !== toggle) {
                        otherToggle.setAttribute('aria-expanded', 'false');
                        otherToggle.nextElementSibling.classList.remove('show');
                    }
                });

                // Toggle current dropdown
                this.setAttribute('aria-expanded', !expanded);
                dropdownMenu.classList.toggle('show');
            }
        });
    });

    // Close menu when clicking a dropdown item (for mobile)
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    dropdownItems.forEach(function(item) {
        item.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                navbarCollapse.classList.remove('show');
                menuBackdrop.classList.remove('show');
                document.body.classList.remove('menu-open');
            }
        });
    });

    // Close menu when clicking a nav link (for mobile)
    const navLinks = document.querySelectorAll('.nav-link:not(.dropdown-toggle)');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                navbarCollapse.classList.remove('show');
                menuBackdrop.classList.remove('show');
                document.body.classList.remove('menu-open');
            }
        });
    });

    // Toggle search input when search icon is clicked
    if (searchToggles.length > 0) {
        searchToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();

                // Get corresponding input container
                const inputContainer = toggle.closest('.search-box').querySelector('.search-input-container');
                const input = inputContainer.querySelector('input');

                inputContainer.classList.add('active');
                input.focus();

                // Close mobile menu if open
                if (window.innerWidth < 992) {
                    navbarCollapse.classList.remove('show');
                    menuBackdrop.classList.remove('show');
                }
            });
        });
    }

    // Close search when close button is clicked
    if (closeSearchButtons.length > 0) {
        closeSearchButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const container = button.closest('.search-input-container');
                container.classList.remove('active');
            });
        });
    }

    // Close search when clicking outside of it
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-box')) {
            searchInputContainers.forEach(container => {
                container.classList.remove('active');
            });
        }
    });

    // Close search when ESC key is pressed
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchInputContainers.forEach(container => {
                container.classList.remove('active');
            });
        }
    });

    // Handle search submission
    if (searchInputs.length > 0) {
        searchInputs.forEach(function(input) {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    // Submit the parent form
                    const form = this.closest('form');
                    if (form) {
                        form.submit();
                    }
                }
            });
        });
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            // Reset mobile menu state when resizing to desktop
            navbarCollapse.classList.remove('show');
            menuBackdrop.classList.remove('show');
            document.body.classList.remove('menu-open');

            // Reset dropdowns
            dropdownToggles.forEach(function(toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            });
        }
    });
});