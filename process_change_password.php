<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/password_policy.php';
require_once 'includes/logger.php';
require_once 'includes/email_service.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['password_message'] = "❌ Invalid request. Please try again.";
    header("Location: change_password.php");
    exit();
}

// Validate input presence
if (!isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
    $_SESSION['password_message'] = "❌ All fields are required.";
    header("Location: change_password.php");
    exit();
}

$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Verify passwords match
if ($new_password !== $confirm_password) {
    $_SESSION['password_message'] = "❌ New passwords do not match.";
    header("Location: change_password.php");
    exit();
}

// Validate new password strength
$password_errors = PasswordPolicy::validatePassword($new_password);
if (!empty($password_errors)) {
    $_SESSION['password_message'] = "❌ Password requirements not met: " . implode(", ", $password_errors);
    header("Location: change_password.php");
    exit();
}

try {
    // Get current user's data
    $stmt = $conn->prepare("SELECT password, email, username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current_password, $user['password'])) {
        $_SESSION['password_message'] = "❌ Current password is incorrect.";
        Logger::warning("Failed password change attempt for user ID: " . $_SESSION['user_id']);
        header("Location: change_password.php");
        exit();
    }

    // Hash new password
    $new_hash = PasswordPolicy::hashPassword($new_password);

    // Update password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$new_hash, $_SESSION['user_id']]);    // Send email notification
    EmailService::sendPasswordChangeNotification($user['email'], $user['username']);

    // Log successful password change
    Logger::info("Password changed successfully for user ID: " . $_SESSION['user_id']);

    $_SESSION['password_message'] = "✅ Password changed successfully! A confirmation email has been sent.";
    header("Location: change_password.php");
    exit();

} catch (PDOException $e) {
    Logger::error("Password change error: " . $e->getMessage());
    $_SESSION['password_message'] = "❌ An error occurred. Please try again later.";
    header("Location: change_password.php");
    exit();
}
