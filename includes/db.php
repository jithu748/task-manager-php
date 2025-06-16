<?php
$host = 'localhost';
$dbname = 'task_manager';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Enable error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Database connected!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
