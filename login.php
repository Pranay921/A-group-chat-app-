<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    }
    $error = "Invalid credentials";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="login.css">
    <title>Chat App Login</title>
    <style>
        /* Dark mode improvements */
        body.dark-mode .form-container {
            background-color: #2d2d2d;
        }
        
        body.dark-mode .form-container h1 {
            color: #e0e0e0;
        }
        
        body.dark-mode .form-container span,
        body.dark-mode .form-container a {
            color: #b8b8b8;
        }
        
        body.dark-mode .form-container input {
            background-color: #3a3a3a;
            border: 1px solid #444;
            color: #e0e0e0;
        }
        
        body.dark-mode .form-container input::placeholder {
            color: #888;
        }
        
        body.dark-mode .form-container button {
            background-color: #1976d2;
            color: #ffffff;
        }
        
        body.dark-mode .form-container button:hover {
            background-color: #1565c0;
        }
        
        body.dark-mode .toggle-container {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        /* Improve toggle panel button hover states */
        body.dark-mode .toggle-panel button {
            color: #e0e0e0;
            border: 1px solid #e0e0e0;
        }
        
        body.dark-mode .toggle-panel button:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: #ffffff;
        }
        
        /* Light mode button hover improvements */
        .toggle-panel button:hover {
            background-color: rgba(0, 0, 0, 0.05);
            color: #333;
            border-color: #333;
        }
        
        /* Fix dark mode toggle icon visibility */
        body.dark-mode .theme-toggle .switch-label {
            background-color: #1e1e1e;
            border-color: #444;
        }
        
        body.dark-mode .theme-toggle .fa-sun {
            color: #aaa;
        }
        
        body.dark-mode .theme-toggle .fa-moon {
            color: #4fc3f7;
        }
        
        body.dark-mode .theme-toggle .switch-handle {
            background-color: #4fc3f7;
        }
        
        /* Add hover effects for the theme toggle */
        .theme-toggle .switch-label:hover::before {
            content: "";
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border-radius: 50px;
            background-color: rgba(30, 144, 255, 0.2);
            z-index: -1;
            transition: all 0.3s ease;
        }
        
        body.dark-mode .theme-toggle .switch-label:hover::before {
            background-color: rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="signup.php" method="POST">
                <h1>Create Account</h1>
                <span>use your details for registration</span>
                <input type="text" placeholder="Username" name="name" required>
                <input type="email" placeholder="Email" name="email" required>
                <input type="tel" placeholder="Phone Number" name="phone_number" 
                       pattern="[0-9]{10}" title="Please enter valid 10-digit phone number" required>
                <input type="password" placeholder="Password" name="password" required>
                <button type="submit">Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form action="signin.php" method="POST">
                <h1>Sign In</h1>
                <span>use your account</span>
                <input type="text" placeholder="Username" name="username" required>
                <input type="password" placeholder="Password" name="password" required>
                <!-- <a href="#">Forgot Your Password?</a> -->
                <button type="submit">Sign In</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Hello, Friend!</h1>
                    <p>Register with your details to use the chat application</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Welcome Back!</h1>
                    <p>Enter your details to use the chat application</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <div class="theme-toggle">
        <input type="checkbox" id="theme-switch" class="theme-switch">
        <label for="theme-switch" class="switch-label">
            <i class="fas fa-sun"></i>
            <i class="fas fa-moon"></i>
            <span class="switch-handle"></span>
        </label>
    </div>
    
    <div class="forgot-password-modal" id="forgotPasswordModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Reset Password</h2>
            <div class="verification-options">
                <button class="option-btn active" data-method="phone">Via Phone</button>
                <button class="option-btn" data-method="email">Via Email</button>
            </div>
            <form id="forgotPasswordForm">
                <div class="step" id="step1">
                    <div class="phone-input" id="phoneVerification">
                        <input type="tel" name="phone" placeholder="Enter your phone number" 
                               pattern="[0-9]{10}" required>
                    </div>
                    <div class="email-input" id="emailVerification" style="display:none;">
                        <input type="email" name="email" placeholder="Enter your email">
                    </div>
                    <button type="button" onclick="sendOTP()">Send OTP</button>
                </div>
                <div class="step" id="step2" style="display:none;">
                    <input type="text" name="otp" placeholder="Enter OTP" maxlength="6" required>
                    <input type="password" name="new_password" placeholder="New Password" required>
                    <button type="button" onclick="verifyOTP()">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="login.js"></script>
</body>
</html>