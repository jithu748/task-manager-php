# Core System Documentation

## Database Connection (db.php)
📁 Location: [`includes/db.php`](../../includes/db.php)

### Purpose
Manages database connections using PDO for secure and efficient database operations.

### Key Features
- Secure PDO connection
- Error handling
- Connection pooling
- Prepared statements support

### Implementation Details
```php
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Connection failed. Please try again later.");
}
```

### Usage Examples
```php
// Select query
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();

// Insert query
$stmt = $conn->prepare("INSERT INTO tasks (user_id, task) VALUES (?, ?)");
$stmt->execute([$user_id, $task]);
```

## Configuration System (config.php)
📁 Location: [`includes/config.php`](../../includes/config.php)

### Purpose
Central configuration management for the entire application.

### Settings Categories
1. Database Configuration
   - Host settings
   - Credentials
   - Database name

2. Email Settings
   - SMTP configuration
   - Email templates
   - Sender information

3. Security Settings
   - Session configuration
   - Password policies
   - Token expiration

4. Application Settings
   - Site URL
   - File upload limits
   - Debug mode

### Sample Configuration
```php
// Database settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'task_manager');
define('DB_USER', 'username');
define('DB_PASS', 'password');

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-specific-password');

// Security settings
define('SESSION_LIFETIME', 3600);
define('MAX_LOGIN_ATTEMPTS', 5);
```

## Logger System (logger.php)
📁 Location: [`includes/logger.php`](../../includes/logger.php)

### Purpose
Handles all application logging including errors, activities, and security events.

### Features
- Error logging
- Activity tracking
- Security event monitoring
- Debug information

### Implementation
```php
class Logger {
    public static function log($message, $level = 'INFO') {
        $date = date('Y-m-d H:i:s');
        $log_message = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents(self::$log_file, $log_message, FILE_APPEND);
    }
}
```

### Usage Examples
```php
// Log error
Logger::log("Database connection failed", "ERROR");

// Log user activity
Logger::log("User {$username} logged in", "INFO");

// Log security event
Logger::log("Failed login attempt for user {$username}", "WARNING");
```

## Email Service (email_service.php)
📁 Location: [`includes/email_service.php`](../../includes/email_service.php)

### Purpose
Handles all email communications in the application.

### Features
- Password reset emails
- Account notifications
- Security alerts
- Email verification

### Implementation
```php
function sendEmail($to, $subject, $body, $isHTML = true) {
    $headers = [
        'From' => SMTP_FROM,
        'Reply-To' => SMTP_FROM,
        'X-Mailer' => 'PHP/' . phpversion()
    ];
    
    if ($isHTML) {
        $headers['MIME-Version'] = '1.0';
        $headers['Content-Type'] = 'text/html; charset=UTF-8';
    }
    
    return mail($to, $subject, $body, $headers);
}
```

### Email Templates
1. Password Reset
```html
<h2>Password Reset Request</h2>
<p>Click the link below to reset your password:</p>
<a href="{reset_link}">Reset Password</a>
```

2. Account Notification
```html
<h2>Account Update</h2>
<p>Your account information has been updated.</p>
<p>If you didn't make this change, please contact support.</p>
```

## Maintenance Tools

### Backup System (backup.php)
📁 Location: [`backup/backup.php`](../../backup/backup.php)

#### Features
- Automated database backups
- SQL dump creation
- Backup file management
- Compression support

#### Implementation
```php
function createBackup() {
    $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $command = sprintf(
        'mysqldump --host=%s --user=%s --password=%s %s > %s',
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME,
        $filename
    );
    exec($command);
    return $filename;
}
```

### Directory Protection (.htaccess)
📁 Location: [`includes/.htaccess`](../../includes/.htaccess)

#### Purpose
Protects sensitive directories and files from direct access.

#### Configuration
```apache
# Prevent directory listing
Options -Indexes

# Deny direct file access
<FilesMatch "\.(php|config)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# Protect sensitive files
<FilesMatch "^(config\.php|password_policy\.php|db\.php)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

## Error Handling

### Global Error Handler
```php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    Logger::log("Error [$errno]: $errstr in $errfile on line $errline", "ERROR");
    return true;
});
```

### Exception Handler
```php
set_exception_handler(function($exception) {
    Logger::log("Uncaught Exception: " . $exception->getMessage(), "ERROR");
    die("An error occurred. Please try again later.");
});
