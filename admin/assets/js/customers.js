

document.addEventListener('DOMContentLoaded', function() {
    // wa lmodel(video ni gi youtub)
    var deleteModal = document.getElementById('deleteConfirmModal');
    if (deleteModal) {
        var deleteModalInstance = new bootstrap.Modal(deleteModal);
        
        
        var deleteConfirmText = document.getElementById('deleteConfirmText');
        
        //  delete buttons
        var deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var customerId = this.getAttribute('data-id');
                var customerName = this.getAttribute('data-name');
                
                
                document.getElementById('deleteCustomerId').value = customerId;
                
                
                deleteConfirmText.textContent = 'Are you sure you want to delete customer "' + customerName + '"?';
                
               
                deleteModalInstance.show();
            });
        });
    }

    //  alert messages
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
