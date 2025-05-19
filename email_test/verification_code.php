<?php
// Include configuration
require_once '../config/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code Test</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f5f0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #8B4513;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #8B4513;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #8B4513;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin: 0 auto;
        }
        button:hover {
            background-color: #A0522D;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .verification-form {
            display: <?php echo isset($_SESSION['verification_email']) ? 'block' : 'none'; ?>;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .code-input {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .code-input input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #8B4513;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verification Code Test</h1>
        
        <?php if (isset($_SESSION['email_success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['email_success']; 
                unset($_SESSION['email_success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['email_error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['email_error']; 
                unset($_SESSION['email_error']);
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Email Form -->
        <form action="send_verification.php" method="POST" id="emailForm">
            <div class="form-group">
                <label for="email">Enter Email Address:</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo isset($_SESSION['verification_email']) ? $_SESSION['verification_email'] : ''; ?>">
            </div>
            
            <button type="submit">Send Verification Code</button>
        </form>
        
        <!-- Verification Code Form -->
        <div class="verification-form" id="verificationForm">
            <h2 style="text-align: center; color: #8B4513;">Enter Verification Code</h2>
            <p style="text-align: center;">A 6-digit verification code has been sent to your email.</p>
            
            <form action="verify_code.php" method="POST">
                <div class="code-input">
                    <input type="text" name="code[]" maxlength="1" pattern="[0-9]" inputmode="numeric" required autofocus>
                    <input type="text" name="code[]" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" name="code[]" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" name="code[]" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" name="code[]" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" name="code[]" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                </div>
                
                <button type="submit">Verify Code</button>
            </form>
            
            <a href="verification_code.php" class="back-link">Cancel</a>
        </div>
    </div>
    
    <script>
        // Auto-focus next input when a digit is entered
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.code-input input');
            
            inputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });
                
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });
            
            // Show verification form if email has been sent
            <?php if (isset($_SESSION['verification_email'])): ?>
            document.getElementById('verificationForm').style.display = 'block';
            <?php endif; ?>
        });
    </script>
</body>
</html>
