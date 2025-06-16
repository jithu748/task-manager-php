<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/password_policy.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Change Password - Task Manager</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="navbar">
        <div class="nav-left">
            <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Tasks</a>
        </div>
        <div class="nav-center">
            <span>Welcome <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
        </div>
        <div class="nav-right">
            <div class="user-menu">
                <button class="user-menu-btn">
                    <i class="fas fa-user-circle"></i>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="user-menu-dropdown">
                    <a href="change_password.php" class="active"><i class="fas fa-key"></i> Change Password</a>
                    <a href="change_email.php"><i class="fas fa-envelope"></i> Change Email</a>
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
                    <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container password-change-page">
        <div class="password-header">
            <h1><i class="fas fa-lock"></i> Change Password</h1>
            <p class="subtitle">Update your password to keep your account secure</p>
        </div>
        
        <?php if (isset($_SESSION['password_message'])): ?>
            <div class="alert <?php echo strpos($_SESSION['password_message'], '✅') !== false ? 'success' : 'error'; ?>">
                <?php 
                    echo $_SESSION['password_message'];
                    unset($_SESSION['password_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="password-change-layout">
            <div class="password-form-section">
                <form action="process_change_password.php" method="POST" class="password-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="current_password">
                                <i class="fas fa-key"></i> Current Password
                            </label>
                            <div class="password-input-group">
                                <input type="password" id="current_password" name="current_password" required>
                                <i class="fas fa-eye toggle-password" data-target="current_password"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">
                                <i class="fas fa-lock"></i> New Password
                            </label>
                            <div class="password-input-group">
                                <input type="password" id="new_password" name="new_password" required>
                                <i class="fas fa-eye toggle-password" data-target="new_password"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="confirm_password">
                                <i class="fas fa-check-circle"></i> Confirm New Password
                            </label>
                            <div class="password-input-group">
                                <input type="password" id="confirm_password" name="confirm_password" required>
                                <i class="fas fa-eye toggle-password" data-target="confirm_password"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Password
                    </button>
                </form>
            </div>

            <div class="password-requirements-section">
                <div class="password-requirements">
                    <h3><i class="fas fa-shield-alt"></i> Password Requirements</h3>
                    <ul>
                        <li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li>
                        <li id="req-uppercase"><i class="fas fa-circle"></i> One uppercase letter</li>
                        <li id="req-lowercase"><i class="fas fa-circle"></i> One lowercase letter</li>
                        <li id="req-number"><i class="fas fa-circle"></i> One number</li>
                        <li id="req-special"><i class="fas fa-circle"></i> One special character</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dark mode toggle
        const themeSwitch = document.getElementById('themeSwitch');
        const body = document.body;
        
        // Check for saved theme preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            body.classList.add('dark-mode');
            themeSwitch.checked = true;
        }

        themeSwitch.addEventListener('change', () => {
            if (themeSwitch.checked) {
                body.classList.add('dark-mode');
                localStorage.setItem('theme', 'dark');
            } else {
                body.classList.remove('dark-mode');
                localStorage.setItem('theme', 'light');
            }
        });

        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', () => {
                const targetId = icon.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html>
