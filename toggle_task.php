<?php
session_start();
include('includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid request.");
    }

    // Authorization check
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    if ($stmt->rowCount() === 0) {
        die("Unauthorized action.");
    }

    $is_done = isset($_POST['is_done']) ? 1 : 0;

    // Update only if the task belongs to the current user
    $stmt = $conn->prepare("UPDATE tasks SET is_done = :is_done WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':is_done', $is_done);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
}

header("Location: index.php");
exit();
