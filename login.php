<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Database connection
require_once "db_connect.php";

$error = "";

// Process login when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        // Check if email exists
        $sql = "SELECT id, name, email, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a new session
                session_start();
                
                // Store data in session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Redirect to dashboard
                header("Location: index.html");
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "No account found with that email";
        }
        
        $stmt->close();
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Wanderlust</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="auth-styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <a href="index.html" class="logo"><i class="fas fa-paper-plane"></i>Wanderlust</a>
                <h1>Welcome Back</h1>
                <p>Sign in to continue your journey</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="auth-form">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
                </div>
                
                <button type="submit" class="auth-btn">Login</button>
                
                <div class="social-login">
                    <p>Or login with</p>
                    <div class="social-buttons">
                        <a href="#" class="social-btn google"><i class="fab fa-google"></i></a>
                        <a href="#" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            </div>
        </div>
        
        <div class="auth-image">
            <div class="overlay"></div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const icon = document.querySelector('.password-toggle i');
            
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
    </script>
</body>
</html>