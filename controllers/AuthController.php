<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
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
                    $_SESSION['is_admin'] = $user['is_admin'];
                    $_SESSION['is_delivery'] = $user['is_delivery'];
                    $_SESSION['logged_in'] = true;

                    // Transfer cart items from session to database
                    require_once __DIR__ . '/CartController.php';
                    $cartController = new CartController($this->db);
                    $cartController->transferCartOnLogin($user['id']);

                    // Redirect based on user role
                    if ($user['is_admin']) {
                        redirect('/admin/dashboard.php');
                    } elseif ($user['is_delivery']) {
                        redirect('/delivery/dashboard.php');
                    } else {
                        // Redirect regular users to their account page
                        redirect('/views/user/account.php');
                    }
                } else {
                    $errors[] = 'Invalid email or password';
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

    // Handle registration
    public function register() {
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $firstName = isset($_POST['first_name']) ? sanitize($_POST['first_name']) : '';
            $lastName = isset($_POST['last_name']) ? sanitize($_POST['last_name']) : '';
            $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

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

            if (empty($email)) {
                $errors[] = 'Email is required';
            } elseif (!preg_match('/^[a-zA-Z][a-zA-Z0-9._%+-]*@[a-zA-Z]+\.[a-zA-Z]{2,}$/', $email)) {
                $errors[] = 'Invalid email format. Email must be like: user@example.com (username must start with a letter, domain must contain only letters)';
            } elseif ($this->userModel->emailExists($email)) {
                $errors[] = 'Email already exists';
            }

            if (empty($password)) {
                $errors[] = 'Password is required';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match';
            }

            // If no errors, register user
            if (empty($errors)) {
                if ($this->userModel->register($firstName, $lastName, $email, $password)) {
                    // Redirect to unified login page
                    redirect('/views/auth/unified_login.php?registered=1');
                } else {
                    $errors[] = 'Registration failed';
                }
            }

            // If we get here, there were errors
            return [
                'errors' => $errors,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email
            ];
        }

        // Display registration form
        return [
            'errors' => [],
            'first_name' => '',
            'last_name' => '',
            'email' => ''
        ];
    }

    // Handle logout
    public function logout() {
        // Unset all session variables
        $_SESSION = [];

        // Destroy the session
        session_destroy();

        // Redirect to home page
        redirect('/index.php');
    }
}
?>
