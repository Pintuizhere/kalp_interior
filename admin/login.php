<?php
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

require_once 'config/db.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['username'] ?? ''); // Form uses 'username' name attribute for the email field
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        try {
            // Prepare statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT * FROM admin_users WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                // Check if user exists
                if ($result->num_rows == 1) {
                    if ($user = $result->fetch_assoc()) {
                        // Verify password
                        if (password_verify($password, $user['password'])) {
                            // Password is correct, start a new session
                            session_regenerate_id();
                            $_SESSION['admin_logged_in'] = true;
                            $_SESSION['admin_id'] = $user['id'];
                            $_SESSION['admin_email'] = $user['email'];
                            
                            // Redirect to admin dashboard
                            header('Location: index.php');
                            exit;
                        } else {
                            $error = "The password you entered was not valid.";
                        }
                    }
                } else {
                    $error = "No account found with that email address.";
                }
                $stmt->close();
            } else {
                $error = "Oops! Something went wrong. Please try again later.";
            }
        } catch (Exception $e) {
            $error = "Oops! Something went wrong. Please try again later.";
            // $error = "Database Error: " . $e->getMessage(); // Uncomment for debugging
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Kalp Interior Studio</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    
    <!-- Fonts & Icons from Frontend -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=League+Spartan:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            /* Frontend Variables */
            --primary-yellow: #EAB136; /* Mustard Yellow from frontend */
            --primary-yellow-hover: #D69E2B;
            --text-dark: #1E2723;
            --text-muted: #66756C;
            --input-bg: #F6F6F6;
            --input-border: #E5E7EB;
            --white: #FFFFFF;
            
            --font-primary: 'Inter', sans-serif;
            --font-headline: 'League Spartan', sans-serif;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-primary);
            height: 100vh;
            overflow: hidden; /* No scrolling */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            background-color: #111;
            /* Full background image */
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.75)), url('../assets/images/admin_bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .logo-container {
            margin-bottom: 40px;
            text-align: center;
            z-index: 10;
        }

        .logo-container img {
            height: 60px;
            width: auto;
            /* Force white logo */
            filter: brightness(0) invert(1);
        }
        
        .login-card-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 0 20px; /* Padding for small screens */
        }
        
        .shield-icon-container {
            position: absolute;
            top: -42px;
            left: 50%;
            transform: translateX(-50%);
            width: 84px;
            height: 84px;
            background: #111; /* Darker background to pop against white card */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid var(--primary-yellow);
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            z-index: 15;
        }
        
        .shield-icon-container i {
            color: var(--primary-yellow);
            font-size: 32px;
        }
        
        .login-card {
            background: var(--white);
            border-radius: 20px;
            padding: 60px 40px 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            position: relative;
            border-top: 3px solid var(--primary-yellow);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .login-header h1 {
            font-family: var(--font-headline);
            font-size: 36px;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .login-header h1 span {
            color: var(--primary-yellow);
        }
        
        .login-header p {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.5;
        }
        
        .separator {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px 0 30px;
        }
        
        .separator::before,
        .separator::after {
            content: "";
            height: 1px;
            width: 50px;
            background: var(--input-border);
        }
        
        .separator-dot {
            width: 6px;
            height: 6px;
            background: var(--primary-yellow);
            border-radius: 50%;
            margin: 0 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 15px;
        }
        
        .input-icon-right {
            left: auto;
            right: 16px;
            cursor: pointer;
            color: var(--text-muted);
            transition: color 0.3s ease;
        }
        
        .input-icon-right:hover {
            color: var(--primary-yellow);
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px 14px 44px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 8px;
            font-size: 14px;
            color: var(--text-dark);
            transition: all 0.3s ease;
            font-family: var(--font-primary);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-yellow);
            box-shadow: 0 0 0 3px rgba(234, 177, 54, 0.2);
            background: #fff;
        }
        
        .form-control::placeholder {
            color: #9ca3af;
        }
        
        .options-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 13px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-dark);
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }
        
        .remember-me input {
            accent-color: var(--primary-yellow);
            width: 16px;
            height: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary-yellow);
            color: var(--text-dark); /* Dark text on yellow for contrast */
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(234, 177, 54, 0.3);
            font-family: var(--font-primary);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            background: var(--primary-yellow-hover);
            box-shadow: 0 6px 15px rgba(234, 177, 54, 0.4);
        }
        
        
        .security-footer {
            margin-top: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            z-index: 10;
            text-align: left;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        
        .security-footer i {
            font-size: 28px;
            color: var(--primary-yellow);
        }
        
        .security-footer p span {
            color: var(--primary-yellow);
            display: block;
            font-weight: 600;
        }

        /* Error message styling */
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="logo-container">
        <!-- Logo -->
        <img src="../assets/images/logo.png" alt="Kalp Interior Studio">
    </div>

    <div class="login-card-wrapper">
        <div class="shield-icon-container">
            <i class="fa-solid fa-user-shield"></i>
        </div>
        
        <div class="login-card">
            <div class="login-header">
                <h1>Admin <span>Login</span></h1>
                <p>Welcome back! Please login to access<br>your admin dashboard.</p>
            </div>
            
            <div class="separator">
                <div class="separator-dot"></div>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="error-message">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="email" id="username" name="username" class="form-control" placeholder="Enter your email" required autocomplete="username">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
                        <i class="fa-regular fa-eye input-icon input-icon-right" onclick="togglePassword()"></i>
                    </div>
                </div>
                
                <div class="options-row">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" checked>
                        <span>Remember me</span>
                    </label>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    Login to Dashboard
                </button>

            </form>
        </div>
    </div>
    
    <div class="security-footer">
        <i class="fa-solid fa-shield-halved"></i>
        <p>Your data is protected with<br><span>enterprise-grade security.</span></p>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.input-icon-right');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
