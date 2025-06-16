# Task Management System Documentation

## Overview
The task management system handles all task-related operations including creation, reading, updating, deletion (CRUD), filtering, and statistics.

## Files Structure
```
task-management/
├── index.php           # Main task dashboard
├── add_task.php       # Task creation
├── edit_task.php      # Task modification
├── delete_task.php    # Task deletion
└── toggle_task.php    # Task completion toggle
```

## 1. Main Dashboard (index.php)
📁 Location: [`index.php`](../../index.php)

### Features
- Task listing
- Search functionality
- Category filtering
- Progress tracking
- Statistics display
- Dark mode toggle

### Implementation

#### Task Listing
```php
// Fetch tasks with filters
$sql = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$_SESSION['user_id']];

if (!empty($search)) {
    $sql .= " AND task LIKE ?";
    $params[] = "%$search%";
}

if ($category !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll();
```

#### Statistics Calculation
```php
// Get task statistics
$totalStmt = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ?");
$totalStmt->execute([$_SESSION['user_id']]);
$totalCount = $totalStmt->fetchColumn();

$doneStmt = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND is_done = 1");
$doneStmt->execute([$_SESSION['user_id']]);
$doneCount = $doneStmt->fetchColumn();

$pendingCount = $totalCount - $doneCount;
```

## 2. Task Creation (add_task.php)
📁 Location: [`add_task.php`](../../add_task.php)

### Features
- Task input validation
- Category selection
- Due date setting
- CSRF protection

### Implementation
```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed');
    }
    
    $task = trim($_POST['task']);
    $category = $_POST['category'];
    $due_date = $_POST['due_date'];
    
    // Validate input
    if (empty($task)) {
        $error = "Task cannot be empty";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO tasks (user_id, task, category, due_date) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$_SESSION['user_id'], $task, $category, $due_date]);
    }
}
```

## 3. Task Editing (edit_task.php)
📁 Location: [`edit_task.php`](../../edit_task.php)

### Features
- Load existing task
- Modify task details
- Update categories
- Change due dates
- Ownership verification

### Implementation
```php
// Load task
$stmt = $conn->prepare("
    SELECT * FROM tasks 
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$task_id, $_SESSION['user_id']]);
$task = $stmt->fetch();

// Update task
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate ownership and CSRF
    if (!$task || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Access denied');
    }
    
    $stmt = $conn->prepare("
        UPDATE tasks 
        SET task = ?, category = ?, due_date = ? 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([
        $_POST['task'],
        $_POST['category'],
        $_POST['due_date'],
        $task_id,
        $_SESSION['user_id']
    ]);
}
```

## 4. Task Deletion (delete_task.php)
📁 Location: [`delete_task.php`](../../delete_task.php)

### Features
- Task removal
- Ownership verification
- CSRF protection
- Confirmation dialog

### Implementation
```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed');
    }
    
    // Verify ownership and delete
    $stmt = $conn->prepare("
        DELETE FROM tasks 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
}
```

## 5. Task Completion Toggle (toggle_task.php)
📁 Location: [`toggle_task.php`](../../toggle_task.php)

### Features
- Toggle completion status
- Update progress
- AJAX support
- Instant feedback

### Implementation
```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed');
    }
    
    // Toggle task status
    $stmt = $conn->prepare("
        UPDATE tasks 
        SET is_done = NOT is_done 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    
    // Return new status
    $stmt = $conn->prepare("
        SELECT is_done FROM tasks 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    echo json_encode(['status' => $stmt->fetchColumn()]);
}
```

## Task Categories
```php
$VALID_CATEGORIES = [
    'General' => '📁',
    'Work' => '💼',
    'Personal' => '🏠',
    'Study' => '📚',
    'Health' => '💪'
];
```

## Progress Tracking
```php
// Calculate progress percentage
$progress = $totalCount > 0 
    ? round(($doneCount / $totalCount) * 100) 
    : 0;

// Progress bar HTML
echo "<div class='progress-bar'>
    <div class='progress' style='width: {$progress}%'></div>
</div>";
```

## Error Handling

### Task Validation
```php
function validateTask($task, $category, $due_date) {
    $errors = [];
    
    if (empty($task)) {
        $errors[] = "Task cannot be empty";
    }
    
    if (!array_key_exists($category, $VALID_CATEGORIES)) {
        $errors[] = "Invalid category";
    }
    
    if (!empty($due_date) && !strtotime($due_date)) {
        $errors[] = "Invalid due date";
    }
    
    return $errors;
}
```

### Error Display
```php
function displayErrors($errors) {
    if (!empty($errors)) {
        echo "<div class='alert alert-error'>";
        foreach ($errors as $error) {
            echo "<p>" . htmlspecialchars($error) . "</p>";
        }
        echo "</div>";
    }
}
```
