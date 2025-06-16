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

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['email_message'] = "❌ Invalid request. Please try again.";
    header("Location: change_email.php");
    exit();
}

// Validate input presence
if (!isset($_POST['current_password'], $_POST['new_email'], $_POST['confirm_email'])) {
    $_SESSION['email_message'] = "❌ All fields are required.";
    header("Location: change_email.php");
    exit();
}

$current_password = $_POST['current_password'];
$new_email = filter_var($_POST['new_email'], FILTER_VALIDATE_EMAIL);
$confirm_email = filter_var($_POST['confirm_email'], FILTER_VALIDATE_EMAIL);

// Validate email format
if (!$new_email || !$confirm_email) {
    $_SESSION['email_message'] = "❌ Invalid email format.";
    header("Location: change_email.php");
    exit();
}

// Verify emails match
if ($new_email !== $confirm_email) {
    $_SESSION['email_message'] = "❌ New emails do not match.";
    header("Location: change_email.php");
    exit();
}

try {
    // Get current user's data
    $stmt = $conn->prepare("SELECT password, email, username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current_password, $user['password'])) {
        $_SESSION['email_message'] = "❌ Current password is incorrect.";
        Logger::warning("Failed email change attempt for user ID: " . $_SESSION['user_id']);
        header("Location: change_email.php");
        exit();
    }

    // Check if new email is already in use
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$new_email, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $_SESSION['email_message'] = "❌ Email address is already in use.";
        header("Location: change_email.php");
        exit();
    }

    // Update email
    $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt->execute([$new_email, $_SESSION['user_id']]);

    // Send notification emails
    EmailService::sendEmailChangeNotification($user['email'], $new_email, $user['username']);

    // Update session
    $_SESSION['email'] = $new_email;

    // Log successful email change
    Logger::info("Email changed successfully for user ID: " . $_SESSION['user_id']);

    $_SESSION['email_message'] = "✅ Email changed successfully! Notifications have been sent to both addresses.";
    header("Location: change_email.php");
    exit();

} catch (PDOException $e) {
    Logger::error("Email change error: " . $e->getMessage());
    $_SESSION['email_message'] = "❌ An error occurred. Please try again later.";
    header("Location: change_email.php");
    exit();
}
