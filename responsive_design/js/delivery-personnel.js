/**
 * Delivery Personnel JavaScript
 * Handles modal functionality and other interactive features
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the delete confirmation modal
    var deleteModal = document.getElementById('deleteConfirmModal');
    var deleteModalInstance = new bootstrap.Modal(deleteModal);

    // Get modal elements
    var deleteConfirmText = document.getElementById('deleteConfirmText');
    var warningContainer = document.getElementById('warningContainer');
    var warningText = document.getElementById('warningText');
    var deletePersonnelId = document.getElementById('deletePersonnelId');

    // Add event listeners to delete buttons
    var deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            // Get data from button attributes
            var personnelId = this.getAttribute('data-id');
            var personnelName = this.getAttribute('data-name');
            var activeOrders = parseInt(this.getAttribute('data-active-orders'));

            // Update modal content
            deleteConfirmText.textContent = 'Are you sure you want to delete ' + personnelName + '?';
            deletePersonnelId.value = personnelId;

            // Show warning if there are active orders
            if (activeOrders > 0) {
                warningText.textContent = 'This delivery personnel has ' + activeOrders + ' active orders. Deleting them will unassign these orders.';
                warningContainer.classList.remove('d-none');
            } else {
                warningContainer.classList.add('d-none');
            }

            // Show the modal
            deleteModalInstance.show();
        });
    });
});
