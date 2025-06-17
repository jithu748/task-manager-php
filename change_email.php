<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/logger.php';
require_once 'includes/email_service.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_email = filter_input(INPUT_POST, 'new_email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Verify password and update email
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($password, $user['password'])) {
            // Check if new email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $new_email, $_SESSION['user_id']);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                $error = "Email already in use";
            } else {
                // Update email
                $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->bind_param("si", $new_email, $_SESSION['user_id']);
                
                if ($stmt->execute()) {
                    $success = "Email updated successfully";
                    Logger::log("User {$_SESSION['username']} changed their email", "INFO");
                } else {
                    $error = "Error updating email";
                    Logger::log("Failed to update email for user {$_SESSION['username']}", "ERROR");
                }
            }
        } else {
            $error = "Invalid password";
            Logger::log("Failed email change attempt for user {$_SESSION['username']}", "WARNING");
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Change Email - Task Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📧 Change Email</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="new_email">New Email:</label>
                <input type="email" id="new_email" name="new_email" required>
            </div>
            <div class="form-group">
                <label for="password">Confirm Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Change Email</button>
            <a href="index.php" class="back-link">Back to Tasks</a>
        </form>
    </div>

    <!-- Dark mode toggle -->
    <div class="theme-toggle">
        <label class="theme-switch" for="themeSwitch">
            <input type="checkbox" id="themeSwitch">
            <span class="slider round"></span>
            <span class="theme-label">
                <i class="fas fa-sun light-icon"></i>
                <i class="fas fa-moon dark-icon"></i>
                <span class="mode-text">Dark Mode</span>
            </span>
        </label>
    </div>

    <script>
        // Initialize dark mode from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const darkMode = localStorage.getItem('darkMode') === 'true';
            document.body.classList.toggle('dark-mode', darkMode);
            document.getElementById('themeSwitch').checked = darkMode;
        });

        // Handle theme toggle
        document.getElementById('themeSwitch').addEventListener('change', (e) => {
            document.body.classList.toggle('dark-mode', e.target.checked);
            localStorage.setItem('darkMode', e.target.checked);
        });
    </script>
</body>
</html>
