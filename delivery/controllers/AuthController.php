<?php
require_once __DIR__ . '/../../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new UserModel($db);
    }

    // Get user profile
    public function getProfile() {
        if (!isLoggedIn() || !isDelivery()) {
            redirect('/views/auth/login.php');
        }

        return $this->userModel->getUserById($_SESSION['user_id']);
    }

    // Update user profile
    public function updateProfile() {
        if (!isLoggedIn() || !isDelivery()) {
            redirect('/views/auth/login.php');
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
            } elseif (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\'\-\s]{2,50}$/', $firstName)) {
                $errors[] = 'First name must be 2-50 characters and contain only letters, spaces, apostrophes, and hyphens';
            }

            if (empty($lastName)) {
                $errors[] = 'Last name is required';
            } elseif (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\'\-\s]{2,50}$/', $lastName)) {
                $errors[] = 'Last name must be 2-50 characters and contain only letters, spaces, apostrophes, and hyphens';
            }

            // Validate phone (if provided)
            if (!empty($phone) && !preg_match('/^[0-9\+\-\(\)\s]{5,20}$/', $phone)) {
                $errors[] = 'Phone number must be 5-20 characters and contain only numbers, +, -, (, ), and spaces';
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
                'user' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone
                ]
            ];
        }

        // Display profile form
        return [
            'user' => $this->userModel->getUserById($_SESSION['user_id'])
        ];
    }

    // Update password
    public function updatePassword() {
        if (!isLoggedIn() || !isDelivery()) {
            redirect('/views/auth/login.php');
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
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'New password must be at least 6 characters';
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Passwords do not match';
            }

            // If no errors, verify current password and update
            if (empty($errors)) {
                // Get user data
                $user = $this->userModel->getUserById($_SESSION['user_id']);

                // Verify current password
                if (password_verify($currentPassword, $user['password'])) {
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

