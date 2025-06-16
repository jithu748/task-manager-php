<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('includes/db.php');

// Check if 'id' is passed in URL and CSRF token is valid
if (isset($_GET['id']) && isset($_GET['csrf_token'])) {
    $id = $_GET['id'];

    // Verify CSRF token
    if ($_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid request.");
    }

    // Authorization check
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    if ($stmt->rowCount() === 0) {
        die("Unauthorized action.");
    }

    // Delete the task only if it belongs to the current user
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
}

// Redirect back to main page
header("Location: index.php");
exit();
