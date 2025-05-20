/**
 * Customers JavaScript
 * Handles modal functionality and other interactive features
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the delete confirmation modal
    var deleteModal = document.getElementById('deleteConfirmModal');
    if (deleteModal) {
        var deleteModalInstance = new bootstrap.Modal(deleteModal);
        
        // Get modal elements
        var deleteConfirmText = document.getElementById('deleteConfirmText');
        
        // Add event listeners to delete buttons
        var deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var customerId = this.getAttribute('data-id');
                var customerName = this.getAttribute('data-name');
                
                // Set the customer ID in the form
                document.getElementById('deleteCustomerId').value = customerId;
                
                // Update the confirmation text
                deleteConfirmText.textContent = 'Are you sure you want to delete customer "' + customerName + '"?';
                
                // Show the modal
                deleteModalInstance.show();
            });
        });
    }

    // Handle alert messages
    if (document.querySelector('.alert-dismissible')) {
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    }
});
