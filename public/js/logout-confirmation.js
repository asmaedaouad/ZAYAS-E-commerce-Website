

// Function to confirm logout
function confirmLogout(logoutUrl) {
    if (confirm('Are you sure you want to log out?')) {
        window.location.href = logoutUrl;
    }
}
