<?php
session_start();
include('includes/db.php');
include('includes/logger.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['id']) && isset($_POST['task'])) {
    $id = $_POST['id'];
    $task = trim($_POST['task']);
    $category = isset($_POST['category']) ? $_POST['category'] : 'General';
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid request.");
    }

    // Validate input
    if (empty($task)) {
        die("Task cannot be empty.");
    }

    // Authorization check
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    if ($stmt->rowCount() === 0) {
        die("Unauthorized action.");
    }    try {
        // Validate due date format if provided
        if ($due_date !== null && !strtotime($due_date)) {
            die("Invalid date format.");
        }

        $stmt = $conn->prepare("UPDATE tasks SET task = :task, category = :category, due_date = :due_date WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(':task', $task);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);

        if ($stmt->execute()) {
            Logger::log("Task updated successfully: ID {$id}", "INFO");
            header("Location: index.php");
            exit();
        } else {
            Logger::log("Failed to update task: ID {$id}", "ERROR");
            die("Error updating task.");
        }
    } catch (PDOException $e) {
        Logger::log("Database error while updating task: " . $e->getMessage(), "ERROR");
        die("Error updating task.");
    }
}

// If no POST data, redirect back to index
header("Location: index.php");
exit();

header("Location: index.php");
exit();
