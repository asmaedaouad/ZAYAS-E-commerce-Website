<?php
// Include configuration
require_once 'config/config.php';
require_once 'config/Database.php';

// Set page title
$pageTitle = 'Setup Password Reset';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// SQL to create password_reset_tokens table
$sql = "
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    used BOOLEAN DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add index for faster token lookups
CREATE INDEX idx_password_reset_token ON password_reset_tokens(token);
";

// Execute SQL
$success = false;
$error = '';

try {
    $success = $db->exec($sql) !== false;
} catch (PDOException $e) {
    $error = $e->getMessage();
}

// Include header
include_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Setup Password Reset</h2>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <h4 class="alert-heading">Success!</h4>
                            <p>The password reset table has been created successfully.</p>
                        </div>
                        <p>You can now use the password reset functionality.</p>
                        <a href="<?php echo url('/index.php'); ?>" class="btn btn-primary">Go to Homepage</a>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <h4 class="alert-heading">Error!</h4>
                            <p>Failed to create the password reset table.</p>
                            <p><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></p>
                        </div>
                        <p>Please check your database configuration and try again.</p>
                        <a href="<?php echo url('/setup_password_reset.php'); ?>" class="btn btn-primary">Try Again</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
