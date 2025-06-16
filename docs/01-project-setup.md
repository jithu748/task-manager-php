# Project Setup and Database Configuration

This document explains the initial setup and database configuration for the Task Manager application.

## 1. Directory Structure Setup

```bash
mkdir task-manager
cd task-manager
mkdir includes css js docs screenshots
```

## 2. Database Configuration

### Database Schema
```sql
CREATE DATABASE task_manager;
USE task_manager;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    task VARCHAR(255) NOT NULL,
    category VARCHAR(50) DEFAULT 'General',
    due_date DATETIME,
    is_done BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Database Connection (includes/db.php)
```php
<?php
$host = 'localhost';
$dbname = 'task_manager';
$username = 'your_username';
$password = 'your_password';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

## 3. Core Files Setup

### Configuration (includes/config.php)
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'task_manager');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

define('SITE_NAME', 'Task Manager');
define('SITE_URL', 'http://localhost/task-manager');

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-specific-password');
?>
```

### Directory Protection (.htaccess)
```apache
# Prevent directory listing
Options -Indexes

# Prevent direct access to .php files in includes directory
<Files "*.php">
    Order Allow,Deny
    Deny from all
</Files>
```

## 4. Initial Files Creation

1. Create main application files:
   - index.php (main task view)
   - login.php
   - register.php
   - add_task.php
   - edit_task.php
   - delete_task.php

2. Create includes:
   - session.php (session handling)
   - password_policy.php
   - logger.php
   - email_service.php

3. Create CSS and JavaScript files:
   - css/style.css
   - js/script.js

## 5. File Permissions

Set appropriate file permissions:
```bash
chmod 755 task-manager
chmod 644 *.php
chmod 644 css/*
chmod 644 js/*
chmod 644 includes/*.php
chmod 644 .htaccess
```

## 6. Testing Setup

1. Test database connection
2. Verify directory protection
3. Check file permissions
4. Test basic routing

## Links to Implementation Files

- [Database Connection Code](../includes/db.php)
- [Configuration File](../includes/config.php)
- [Main .htaccess](../.htaccess)
- [Includes Protection](../includes/.htaccess)

## Next Steps

Move on to [User Authentication Implementation](02-authentication.md) to set up the user registration and login system.
