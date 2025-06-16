# Security Implementation

This document details the security features implemented in the Task Manager application.

## 1. Password Security

### Password Hashing (includes/password_policy.php)
```php
<?php
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function validatePassword($password) {
    // At least 8 characters
    if (strlen($password) < 8) return false;
    
    // Check for uppercase
    if (!preg_match('/[A-Z]/', $password)) return false;
    
    // Check for lowercase
    if (!preg_match('/[a-z]/', $password)) return false;
    
    // Check for numbers
    if (!preg_match('/[0-9]/', $password)) return false;
    
    // Check for special characters
    if (!preg_match('/[^A-Za-z0-9]/', $password)) return false;
    
    return true;
}
?>
```

## 2. CSRF Protection

### Token Generation (includes/session.php)
```php
<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validateCSRF($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die('CSRF token validation failed');
    }
    return true;
}
?>
```

### Form Implementation
```html
<form method="POST" action="process.php">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <!-- form fields -->
</form>
```

## 3. SQL Injection Prevention

### Using Prepared Statements
```php
// GOOD: Using prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);

// BAD: Direct variable insertion (NEVER DO THIS)
$query = "SELECT * FROM users WHERE username = '$username'";
```

## 4. XSS Prevention

### Output Escaping
```php
// Always escape output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// For JSON data
header('Content-Type: application/json');
echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
```

## 5. Session Security

### Session Configuration
```php
// Set secure session cookies
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);

// Regenerate session ID periodically
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 3600) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}
```

## 6. Directory Protection

### Apache Configuration (.htaccess)
```apache
# Prevent directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Protect config files
<FilesMatch "^(config\.php|password_policy\.php|db\.php)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

## 7. Input Validation

### Validation Functions
```php
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validateUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

function sanitizeInput($input) {
    return trim(strip_tags($input));
}
```

## 8. Error Handling

### Custom Error Handler
```php
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error = date('Y-m-d H:i:s') . " - Error: [$errno] $errstr - $errfile:$errline\n";
    error_log($error, 3, 'logs/error.log');
    
    if (ini_get('display_errors')) {
        printf("<div class='error'>An error occurred. Please try again later.</div>");
    }
    return true;
}
set_error_handler('customErrorHandler');
```

## Implementation Files

- [Password Policy](../includes/password_policy.php)
- [Session Handler](../includes/session.php)
- [Main .htaccess](../.htaccess)
- [Error Logger](../includes/logger.php)

## Testing Security

1. Test password validation
2. Verify CSRF protection
3. Test SQL injection prevention
4. Check XSS protection
5. Verify session security
6. Test directory protection
7. Validate input handling
8. Check error logging

## Next Steps

Move on to [Input Validation & Sanitization](10-validation.md) for more details on input handling.
