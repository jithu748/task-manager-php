<?php
session_start();
include('includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['id']) && isset($_POST['task'])) {
    $id = $_POST['id'];
    $task = trim($_POST['task']);

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

    if ($task !== '') {
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        
        // Validate due date format if provided
        if ($due_date !== null && !strtotime($due_date)) {
            die("Invalid date format.");
        }

        $stmt = $conn->prepare("UPDATE tasks SET task = :task, due_date = :due_date WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(':task', $task);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
    }
}

header("Location: index.php");
exit();
