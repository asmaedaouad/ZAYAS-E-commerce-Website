<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Set page title
$pageTitle = 'Contact Settings';
$customCss = 'settings.css';

// Path to contact settings file
$contactSettingsFile = __DIR__ . '/../config/contact_settings.php';

// Initialize variables
$successMessage = '';
$errorMessage = '';
$contactSettings = include $contactSettingsFile;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_contact_settings'])) {
    // Validate and sanitize inputs
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mapEmbedUrl = isset($_POST['map_embed_url']) ? trim($_POST['map_embed_url']) : '';
    
    // Opening hours
    $openingHours = [
        'Monday - Friday' => isset($_POST['weekday_hours']) ? trim($_POST['weekday_hours']) : '',
        'Saturday' => isset($_POST['saturday_hours']) ? trim($_POST['saturday_hours']) : '',
        'Sunday' => isset($_POST['sunday_hours']) ? trim($_POST['sunday_hours']) : ''
    ];
    
    // Social media
    $socialMedia = [
        'facebook' => isset($_POST['facebook']) ? trim($_POST['facebook']) : '#',
        'instagram' => isset($_POST['instagram']) ? trim($_POST['instagram']) : '#',
        'pinterest' => isset($_POST['pinterest']) ? trim($_POST['pinterest']) : '#',
        'twitter' => isset($_POST['twitter']) ? trim($_POST['twitter']) : '#'
    ];
    
    // Basic validation
    $isValid = true;
    
    if (empty($address)) {
        $errorMessage = 'Address is required';
        $isValid = false;
    } elseif (empty($phone)) {
        $errorMessage = 'Phone number is required';
        $isValid = false;
    } elseif (empty($email)) {
        $errorMessage = 'Email is required';
        $isValid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Invalid email format';
        $isValid = false;
    }
    
    // If valid, update settings
    if ($isValid) {
        // Create new settings array
        $newSettings = [
            'address' => $address,
            'phone' => $phone,
            'email' => $email,
            'opening_hours' => $openingHours,
            'map_embed_url' => $mapEmbedUrl,
            'social_media' => $socialMedia
        ];
        
        // Convert to PHP code
        $settingsCode = "<?php\n/**\n * Contact Settings Configuration\n * This file stores the contact information for the website\n */\n\n// Return contact settings as an array\nreturn " . var_export($newSettings, true) . ";";
        
        // Write to file
        if (file_put_contents($contactSettingsFile, $settingsCode)) {
            $successMessage = 'Contact settings updated successfully';
            $contactSettings = $newSettings; // Update current settings
        } else {
            $errorMessage = 'Failed to update settings. Please check file permissions.';
        }
    }
}

// Include header
include_once './includes/header.php';
?>

<!-- Settings Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-cog me-2"></i> Contact Settings</span>
            </div>
            <div class="card-body">
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success">
                        <?php echo $successMessage; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="row">
                        <!-- Contact Information -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-info-circle me-2"></i> Contact Information
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($contactSettings['address']); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($contactSettings['phone']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($contactSettings['email']); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Opening Hours -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-clock me-2"></i> Opening Hours
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="weekday_hours" class="form-label">Monday - Friday</label>
                                        <input type="text" class="form-control" id="weekday_hours" name="weekday_hours" value="<?php echo htmlspecialchars($contactSettings['opening_hours']['Monday - Friday']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="saturday_hours" class="form-label">Saturday</label>
                                        <input type="text" class="form-control" id="saturday_hours" name="saturday_hours" value="<?php echo htmlspecialchars($contactSettings['opening_hours']['Saturday']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="sunday_hours" class="form-label">Sunday</label>
                                        <input type="text" class="form-control" id="sunday_hours" name="sunday_hours" value="<?php echo htmlspecialchars($contactSettings['opening_hours']['Sunday']); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Map Settings -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-map-marker-alt me-2"></i> Map Settings
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="map_embed_url" class="form-label">Google Maps Embed URL</label>
                                        <textarea class="form-control" id="map_embed_url" name="map_embed_url" rows="4"><?php echo htmlspecialchars($contactSettings['map_embed_url']); ?></textarea>
                                        <small class="form-text text-muted">Enter the full Google Maps embed URL</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Map Preview</label>
                                        <div class="map-preview">
                                            <iframe src="<?php echo htmlspecialchars($contactSettings['map_embed_url']); ?>" width="100%" height="150" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Social Media -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-share-alt me-2"></i> Social Media Links
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="facebook" class="form-label">Facebook</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                                            <input type="text" class="form-control" id="facebook" name="facebook" value="<?php echo htmlspecialchars($contactSettings['social_media']['facebook']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="instagram" class="form-label">Instagram</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                            <input type="text" class="form-control" id="instagram" name="instagram" value="<?php echo htmlspecialchars($contactSettings['social_media']['instagram']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="pinterest" class="form-label">Pinterest</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-pinterest-p"></i></span>
                                            <input type="text" class="form-control" id="pinterest" name="pinterest" value="<?php echo htmlspecialchars($contactSettings['social_media']['pinterest']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="twitter" class="form-label">Twitter</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                            <input type="text" class="form-control" id="twitter" name="twitter" value="<?php echo htmlspecialchars($contactSettings['social_media']['twitter']); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" name="update_contact_settings" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once './includes/footer.php';
?>

