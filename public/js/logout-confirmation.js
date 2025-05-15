/**
 * Logout confirmation functionality
 * This script provides a consistent logout confirmation across all interfaces
 */

// Function to confirm logout
function confirmLogout(logoutUrl) {
    if (confirm('Are you sure you want to log out?')) {
        window.location.href = logoutUrl;
    }
}
