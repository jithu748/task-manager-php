<?php
session_start();
require_once 'includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
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
        
        <?php if (isset($_SESSION['email_message'])): ?>
            <div class="alert <?php echo strpos($_SESSION['email_message'], '✅') !== false ? 'success' : 'error'; ?>">
                <?php 
                    echo $_SESSION['email_message'];
                    unset($_SESSION['email_message']);
                ?>
            </div>
        <?php endif; ?>

        <form action="process_change_email.php" method="POST" class="email-form">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>

            <div class="form-group">
                <label for="new_email">New Email:</label>
                <input type="email" id="new_email" name="new_email" required>
            </div>

            <div class="form-group">
                <label for="confirm_email">Confirm New Email:</label>
                <input type="email" id="confirm_email" name="confirm_email" required>
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
