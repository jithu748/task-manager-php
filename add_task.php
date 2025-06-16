<?php
session_start();
include('includes/db.php');

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task'])) {
    $task = trim($_POST['task']);
    $category = $_POST['category'] ?? 'General';
    $due_date = $_POST['due_date'] ?? null;
    $user_id = $_SESSION['user_id'];    // Validate task
    if (empty($task)) {
        echo "❌ Task cannot be empty.";
        exit();
    }

    // Validate category
    $valid_categories = ['General', 'Work', 'Personal', 'Study', 'Health'];
    if (!in_array($category, $valid_categories)) {
        echo "❌ Invalid category.";
        exit();
    }    // Validate due date
    if (!empty($due_date)) {
        $timestamp = strtotime($due_date);
        if ($timestamp === false) {
            echo "❌ Invalid date format.";
            exit();
        }
        // Convert the date to MySQL datetime format
        $due_date = date('Y-m-d H:i:s', $timestamp);
    }

    // Insert task
    $stmt = $conn->prepare("INSERT INTO tasks (task, category, user_id, due_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([htmlspecialchars($task), $category, $user_id, $due_date]);

    $_SESSION['toast_message'] = '✅ Task added successfully!';
    header("Location: index.php");
    exit();
}
