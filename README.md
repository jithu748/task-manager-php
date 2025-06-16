# PHP Task Manager

A comprehensive task management system with user authentication, task categorization, dark mode, and security features.

## 📁 Project Structure

### Core Files
- [`index.php`](index.php) - Main dashboard with task listing, filtering, and statistics
  - Task display with categories
  - Search functionality
  - Progress tracking
  - Statistics cards
  - Dark mode toggle

- [`style.css`](style.css) - Global styles
  - Light/Dark theme support
  - Responsive design
  - Modern UI components

### Task Management
- [`add_task.php`](add_task.php) - Task creation
  - Category selection
  - Due date picker
  - Form validation

- [`edit_task.php`](edit_task.php) - Task modification
  - Update task details
  - Change category/due date
  - Security checks

- [`delete_task.php`](delete_task.php) - Task deletion
  - Ownership verification
  - CSRF protection

- [`toggle_task.php`](toggle_task.php) - Task completion
  - Mark tasks done/undone
  - Update progress

### User Authentication
- [`register.php`](register.php) - User registration
  - Email validation
  - Password strength check
  - Duplicate prevention

- [`login.php`](login.php) - User login
  - Secure authentication
  - Session management
  - Remember me option

- [`logout.php`](logout.php) - User logout
  - Session cleanup
  - Security measures

- [`auth.php`](auth.php) - Authentication helper
  - Session verification
  - Access control

### Account Management
- [`change_password.php`](change_password.php) - Password updates
  - Current password verification
  - Password policy check
  - Email notification

- [`change_email.php`](change_email.php) - Email updates
  - Email verification
  - Security checks
  - Update confirmation

- [`process_change_password.php`](process_change_password.php) - Password change handler
  - Password hashing
  - Validation logic
  - Error handling

- [`process_change_email.php`](process_change_email.php) - Email change handler
  - Email validation
  - Update processing
  - Notification sending

### Core Includes [`includes/`](includes)
- [`db.php`](includes/db.php) - Database connection
  - PDO configuration
  - Error handling
  - Connection pooling

- [`config.php`](includes/config.php) - Application settings
  - Database credentials
  - Email settings
  - Site configuration

- [`session.php`](includes/session.php) - Session management
  - Session security
  - CSRF protection
  - Token management

- [`password_policy.php`](includes/password_policy.php) - Password rules
  - Strength requirements
  - Validation logic
  - Hashing functions

- [`email_service.php`](includes/email_service.php) - Email functionality
  - SMTP configuration
  - Email templates
  - Sending logic

- [`logger.php`](includes/logger.php) - Error logging
  - Activity tracking
  - Error handling
  - Debug information

### Backup System [`backup/`](backup)
- [`backup.php`](backup/backup.php) - Database backup
  - Automated backups
  - SQL dump creation
  - File management

### Security
- [`.htaccess`](.htaccess) - Server security
  - Directory protection
  - Access control
  - Error handling

## 🔍 Features Explained

### 1. Task Management
- Create, Read, Update, Delete (CRUD) operations
- Category organization
- Due date tracking
- Progress monitoring
- Search and filtering

### 2. User System
- Secure registration and login
- Password management
- Email updates
- Session handling

### 3. Security Features
- Password hashing (bcrypt)
- CSRF protection
- SQL injection prevention
- XSS protection
- Input validation
- Session security

### 4. UI/UX Features
- Responsive design
- Dark/Light modes
- Interactive elements
- Progress visualization
- Status indicators

## 🛠️ Code Implementation Details

### Database Structure
```sql
-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tasks table
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

### Security Implementation
```php
// Password Hashing
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// CSRF Protection
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF token validation failed');
}

// SQL Injection Prevention
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? AND category = ?");
$stmt->execute([$user_id, $category]);
```

### Task Operations
```php
// Adding Tasks
$stmt = $conn->prepare("INSERT INTO tasks (user_id, task, category, due_date) VALUES (?, ?, ?, ?)");
$stmt->execute([$user_id, $task, $category, $due_date]);

// Updating Tasks
$stmt = $conn->prepare("UPDATE tasks SET task = ?, category = ?, due_date = ? WHERE id = ? AND user_id = ?");
$stmt->execute([$task, $category, $due_date, $task_id, $user_id]);
```

## 📊 Progress Tracking
- Task completion statistics
- Category-wise progress
- Due date monitoring
- Activity logging

## 🔒 Security Measures
1. Password Requirements:
   - Minimum 8 characters
   - Mixed case letters
   - Numbers
   - Special characters

2. CSRF Protection:
   - Token generation
   - Token validation
   - Session security

3. Database Security:
   - Prepared statements
   - Input validation
   - Error handling

## 🎨 UI Components
1. Task Interface:
   - Clean layout
   - Category colors
   - Status indicators
   - Progress bars

2. Forms:
   - Validation feedback
   - Error messages
   - Success notifications

3. Dark Mode:
   - Theme switcher
   - Persistent preference
   - Smooth transition

## 📱 Responsive Design
- Mobile-first approach
- Flexible layouts
- Touch-friendly controls
- Adaptive components

## 🔄 Future Updates
1. Planned Features:
   - Task priorities
   - Shared tasks
   - File attachments
   - Calendar view

2. Improvements:
   - Performance optimization
   - Enhanced security
   - UI refinements
   - Additional categories
