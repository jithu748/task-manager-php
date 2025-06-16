# Implementation Details

This document provides detailed implementation notes for each major feature of the Task Manager application.

## 1. Database Connection
File: [`includes/db.php`](../includes/db.php)
```php
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
```

## 2. User Authentication
File: [`auth.php`](../auth.php)
```php
// Session check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// CSRF token generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
```

## 3. Task Management

### Add Task
File: [`add_task.php`](../add_task.php)
```php
$stmt = $conn->prepare("
    INSERT INTO tasks (user_id, task, category, due_date) 
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$user_id, $task, $category, $due_date]);
```

### Edit Task
File: [`edit_task.php`](../edit_task.php)
```php
$stmt = $conn->prepare("
    UPDATE tasks 
    SET task = ?, category = ?, due_date = ? 
    WHERE id = ? AND user_id = ?
");
```

### Delete Task
File: [`delete_task.php`](../delete_task.php)
```php
$stmt = $conn->prepare("
    DELETE FROM tasks 
    WHERE id = ? AND user_id = ?
");
```

## 4. Security Features

### Password Hashing
File: [`includes/password_policy.php`](../includes/password_policy.php)
```php
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
```

### Session Management
File: [`includes/session.php`](../includes/session.php)
```php
session_start();
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_regenerate_id(true);
```

## 5. Email Notifications
File: [`includes/email_service.php`](../includes/email_service.php)
```php
function sendEmail($to, $subject, $body) {
    $headers = 'From: ' . SMTP_FROM . "\r\n" .
        'Reply-To: ' . SMTP_FROM . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    
    return mail($to, $subject, $body, $headers);
}
```

## 6. Progress Tracking
File: [`index.php`](../index.php)
```php
$totalStmt = $conn->prepare("
    SELECT COUNT(*) FROM tasks WHERE user_id = ?
");
$doneStmt = $conn->prepare("
    SELECT COUNT(*) FROM tasks WHERE user_id = ? AND is_done = 1
");
```

## 7. Dark Mode Implementation
File: [`style.css`](../style.css)
```css
body.dark-mode {
    background: #1a1a1a;
    color: #e0e0e0;
}

body.dark-mode .container {
    background: #2d2d2d;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}
```

## 8. Backup System
File: [`backup/backup.php`](../backup/backup.php)
```php
$backup_file = $backup_dir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
$command = sprintf(
    'mysqldump --host=%s --user=%s --password=%s %s > %s',
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME,
    $backup_file
);
```

## Feature Enhancement Guide

### Adding a New Category
1. Update the `$VALID_CATEGORIES` array in `index.php`
2. Add category icon in CSS
3. Update task form to include new category

### Adding a New Feature
1. Create necessary database tables/columns
2. Implement backend logic
3. Create/update frontend interface
4. Add security measures
5. Update documentation

## Testing Guidelines

### Security Testing
1. Test CSRF protection
2. Verify password hashing
3. Check SQL injection prevention
4. Validate XSS protection

### Functionality Testing
1. Test all CRUD operations
2. Verify email notifications
3. Check dark mode toggle
4. Test responsive design

## Common Issues and Solutions

### Database Connection Issues
```php
// Check connection parameters
if (!$conn) {
    error_log("Connection failed: " . $e->getMessage());
    // Display user-friendly message
}
```

### Session Problems
```php
// Verify session status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

### File Permissions
- Set proper permissions for logs directory
- Ensure backup directory is writable
- Protect sensitive files with .htaccess
