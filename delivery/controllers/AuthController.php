<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new UserModel($db);
    }

    // Handle login
    public function login() {
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            // Validate input
            $errors = [];

            if (empty($email)) {
                $errors[] = 'Email is required';
            } elseif (!preg_match('/^[a-zA-Z][a-zA-Z0-9._%+-]*@[a-zA-Z]+\.[a-zA-Z]{2,}$/', $email)) {
                $errors[] = 'Invalid email format. Email must be like: user@example.com (username must start with a letter, domain must contain only letters)';
            }

            if (empty($password)) {
                $errors[] = 'Password is required';
            }

            // If no errors, attempt login
            if (empty($errors)) {
                $user = $this->userModel->login($email, $password);

                if ($user) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['is_delivery'] = $user['is_delivery'];
                    $_SESSION['logged_in'] = true;

                    // Redirect to delivery dashboard
                    redirect('/delivery/dashboard.php');
                } else {
                    $errors[] = 'Invalid email or password, or you are not registered as delivery personnel';
                }
            }

            // If we get here, there were errors
            return [
                'errors' => $errors,
                'email' => $email
            ];
        }

        // Display login form
        return [
            'errors' => [],
            'email' => ''
        ];
    }

    // Handle logout
    public function logout() {
        // Unset all session variables
        $_SESSION = [];

        // Destroy the session
        session_destroy();

        // Redirect to login page
        redirect('/delivery/login.php');
    }

    // Get user profile
    public function getProfile() {
        if (!isLoggedIn() || !isDelivery()) {
            redirect('/delivery/login.php');
        }

        return $this->userModel->getUserById($_SESSION['user_id']);
    }

    // Update profile
    public function updateProfile() {
        if (!isLoggedIn() || !isDelivery()) {
            redirect('/delivery/login.php');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $firstName = isset($_POST['first_name']) ? sanitize($_POST['first_name']) : '';
            $lastName = isset($_POST['last_name']) ? sanitize($_POST['last_name']) : '';
            $phone = isset($_POST['phone']) ? sanitize($_POST['phone']) : '';

            // Validate input
            $errors = [];

            if (empty($firstName)) {
                $errors[] = 'First name is required';
            } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\'\-]{2,50}$/', $firstName)) {
                $errors[] = 'First name must be 2-50 characters and contain only letters, spaces, hyphens, and apostrophes';
            }

            if (empty($lastName)) {
                $errors[] = 'Last name is required';
            } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\'\-]{2,50}$/', $lastName)) {
                $errors[] = 'Last name must be 2-50 characters and contain only letters, spaces, hyphens, and apostrophes';
            }

            if (empty($phone)) {
                $errors[] = 'Phone number is required';
            }

            // If no errors, update profile
            if (empty($errors)) {
                if ($this->userModel->updateProfile($_SESSION['user_id'], $firstName, $lastName, $phone)) {
                    // Update session variables
                    $_SESSION['first_name'] = $firstName;
                    $_SESSION['last_name'] = $lastName;

                    return [
                        'success' => 'Profile updated successfully',
                        'user' => $this->userModel->getUserById($_SESSION['user_id'])
                    ];
                } else {
                    $errors[] = 'Failed to update profile';
                }
            }

            // If we get here, there were errors
            return [
                'errors' => $errors,
                'user' => $this->userModel->getUserById($_SESSION['user_id'])
            ];
        }

        // Display profile form
        return [
            'errors' => [],
            'user' => $this->userModel->getUserById($_SESSION['user_id'])
        ];
    }

    // Update password
    public function updatePassword() {
        if (!isLoggedIn() || !isDelivery()) {
            redirect('/delivery/login.php');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
            $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

            // Validate input
            $errors = [];

            if (empty($currentPassword)) {
                $errors[] = 'Current password is required';
            }

            if (empty($newPassword)) {
                $errors[] = 'New password is required';
            }

            if (empty($confirmPassword)) {
                $errors[] = 'Confirm password is required';
            } elseif ($newPassword !== $confirmPassword) {
                $errors[] = 'Passwords do not match';
            }

            // If no errors, verify current password and update
            if (empty($errors)) {
                $user = $this->userModel->getUserById($_SESSION['user_id']);

                if ($user && password_verify($currentPassword, $user['password'])) {
                    if ($this->userModel->updatePassword($_SESSION['user_id'], $newPassword)) {
                        return [
                            'success' => 'Password updated successfully'
                        ];
                    } else {
                        $errors[] = 'Failed to update password';
                    }
                } else {
                    $errors[] = 'Current password is incorrect';
                }
            }

            // If we get here, there were errors
            return [
                'errors' => $errors
            ];
        }

        // Display password form
        return [
            'errors' => []
        ];
    }
}
?>
