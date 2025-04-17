<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

require_once "db_connect.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match";
    } else {
        // Check if email already exists
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $error = "Email already exists. Please use a different email or login";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $insertSql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("sss", $name, $email, $hashedPassword);
            
            if ($insertStmt->execute()) {
                $success = "Registration successful! You can now login.";
                
                // Optional: Auto-login after registration
                // $_SESSION['user_id'] = $conn->insert_id;
                // $_SESSION['user_name'] = $name;
                // $_SESSION['user_email'] = $email;
                // header("Location: test.html");
                // exit();
            } else {
                $error = "Error: " . $insertStmt->error;
            }
            
            $insertStmt->close();
        }
        
        $checkStmt->close();
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Wanderlust</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="auth-styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <a href="index.php" class="logo"><i class="fas fa-paper-plane"></i>Wanderlust</a>
                <h1>Create an Account</h1>
                <p>Join us and start planning your adventures</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <p>Redirecting to login page in <span id="countdown">5</span> seconds...</p>
                    <script>
                        // Countdown redirect
                        let seconds = 5;
                        const countdownElement = document.getElementById('countdown');
                        const countdownInterval = setInterval(() => {
                            seconds--;
                            countdownElement.textContent = seconds;
                            if (seconds <= 0) {
                                clearInterval(countdownInterval);
                                window.location.href = 'login.php';
                            }
                        }, 1000);
                    </script>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="auth-form" id="signupForm">
                <div class="form-group">
                    <label for="name"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </span>
                    <div class="password-strength-meter">
                        <div class="strength-meter-bar"></div>
                    </div>
                    <div class="password-tips">
                        <small id="passwordHelp">Password should be at least 6 characters</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    <span class="password-toggle" onclick="togglePassword('confirm_password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <div class="form-options">
                    <div class="agree-terms">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                    </div>
                </div>
                
                <button type="submit" class="auth-btn">Create Account</button>
                
                <div class="social-login">
                    <p>Or sign up with</p>
                    <div class="social-buttons">
                        <a href="#" class="social-btn google"><i class="fab fa-google"></i></a>
                        <a href="#" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Log In</a></p>
            </div>
        </div>
        
        <div class="auth-image signup-image">
            <div class="overlay"></div>
        </div>
    </div>
    
    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const icon = passwordField.nextElementSibling.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Password strength meter
        const passwordInput = document.getElementById('password');
        const strengthMeter = document.querySelector('.strength-meter-bar');
        const passwordHelp = document.getElementById('passwordHelp');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let tips = [];
            
            if (password.length >= 6) {
                strength += 20;
            } else {
                tips.push("at least 6 characters");
            }
            
            if (password.match(/[A-Z]/)) {
                strength += 20;
            } else {
                tips.push("at least one uppercase letter");
            }
            
            if (password.match(/[a-z]/)) {
                strength += 20;
            } else {
                tips.push("at least one lowercase letter");
            }
            
            if (password.match(/[0-9]/)) {
                strength += 20;
            } else {
                tips.push("at least one number");
            }
            
            if (password.match(/[^A-Za-z0-9]/)) {
                strength += 20;
            } else {
                tips.push("at least one special character");
            }
            
            strengthMeter.style.width = strength + '%';
            
            if (strength <= 20) {
                strengthMeter.style.backgroundColor = '#ff4d4d';
            } else if (strength <= 40) {
                strengthMeter.style.backgroundColor = '#ffa64d';
            } else if (strength <= 60) {
                strengthMeter.style.backgroundColor = '#ffff4d';
            } else if (strength <= 80) {
                strengthMeter.style.backgroundColor = '#4dff4d';
            } else {
                strengthMeter.style.backgroundColor = '#4d4dff';
            }
            
            if (tips.length > 0) {
                passwordHelp.textContent = 'Password should include ' + tips.join(", ");
            } else {
                passwordHelp.textContent = 'Strong password!';
            }
        });
        
        // Confirm password validation
        const confirmPassword = document.getElementById('confirm_password');
        
        confirmPassword.addEventListener('input', function() {
            if (this.value !== passwordInput.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Form validation
        const signupForm = document.getElementById('signupForm');
        
        signupForm.addEventListener('submit', function(event) {
            if (!document.getElementById('terms').checked) {
                event.preventDefault();
                alert('Please agree to the Terms of Service and Privacy Policy');
            }
        });
    </script>
</body>
</html>