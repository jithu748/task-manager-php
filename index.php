<?php
session_start();

// Define valid categories with icons
$VALID_CATEGORIES = [
    'General' => '📁',
    'Work' => '💼',
    'Personal' => '🏠',
    'Study' => '📚',
    'Health' => '💪'
];

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('includes/db.php');
include('auth.php');

// 📊 Task Summary Counts
$totalStmt = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ?");
$totalStmt->execute([$_SESSION['user_id']]);
$totalCount = $totalStmt->fetchColumn();

$doneStmt = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND is_done = 1");
$doneStmt->execute([$_SESSION['user_id']]);
$doneCount = $doneStmt->fetchColumn();

$pendingCount = $totalCount - $doneCount;

// 🧠 Task Filter Logic
$filter = in_array($_GET['filter'] ?? '', ['pending', 'done']) ? $_GET['filter'] : 'all';
$category = isset($_GET['category']) && array_key_exists($_GET['category'], $VALID_CATEGORIES) ? $_GET['category'] : 'all';
$search = trim($_GET['search'] ?? '');

$sql = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$_SESSION['user_id']];

// Add search filter
if (!empty($search)) {
    $sql .= " AND task LIKE ?";
    $params[] = "%" . $search . "%";
}

// Add status filter
if ($filter === 'done') {
    $sql .= " AND is_done = 1";
} elseif ($filter === 'pending') {
    $sql .= " AND is_done = 0";
}

// Add category filter
if ($category !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY id DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $tasks = $stmt->fetchAll();

    // Calculate progress
    $taskCount = count($tasks);
    $doneCount = 0;
    foreach ($tasks as $t) {
        if ($t['is_done']) $doneCount++;
    }
    $percent = $taskCount ? round(($doneCount / $taskCount) * 100) : 0;
    
    $color = '#f44336'; // Red
    if ($percent >= 50) $color = '#ff9800'; // Orange
    if ($percent >= 80) $color = '#4caf50'; // Green
} catch (PDOException $e) {
    // Log the error (in a production environment)
    error_log("Database error: " . $e->getMessage());
    // Show user-friendly message
    echo '<div class="error-message">An error occurred while loading tasks. Please try again later.</div>';
    $tasks = [];
    $total = $done = $percent = 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Manager</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="navbar">
        <div class="nav-left">
            <h1><i class="fas fa-tasks"></i> Task Manager</h1>
        </div>
        <div class="nav-center">
            <span>Welcome <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
        </div>
        <div class="nav-right">
            <div class="user-menu">
                <button class="user-menu-btn">
                    <i class="fas fa-user-circle"></i>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="user-menu-dropdown">
                    <a href="change_password.php"><i class="fas fa-key"></i> Change Password</a>
                    <a href="change_email.php"><i class="fas fa-envelope"></i> Change Email</a>
                    <div class="theme-toggle">
                        <label class="theme-switch" for="themeSwitch">
                            <input type="checkbox" id="themeSwitch">
                            <span class="slider round"></span>
                            <span class="theme-label">
                                <i class="fas fa-sun light-icon"></i>
                                <i class="fas fa-moon dark-icon"></i>
                                <span class="mode-text">Dark Mode</span>
                            </span>
                        </label>
                    </div>
                    <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 📊 Task Statistics -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-value"><?php echo $totalCount; ?></div>
            <div class="stat-label">Total Tasks</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $pendingCount; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $doneCount; ?></div>
            <div class="stat-label">Completed</div>
        </div>
    </div>

    <!-- Container -->
    <div class="container">
        <h1>📝 Task Manager</h1>    <!-- 🔍 Search Tasks -->
        <form method="GET" class="search-form">
            <div class="search-container">
                <input type="text" name="search" placeholder="Search tasks..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">🔍</button>
            </div>
        </form>

        <!-- ➕ Add New Task -->
        <form action="add_task.php" method="POST" class="add-task-form">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-row">
                <input type="text" name="task" placeholder="Enter a new task" required>
                <select name="category" required>
                    <?php foreach ($VALID_CATEGORIES as $cat => $icon): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo "$icon $cat"; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="datetime-local" name="due_date" class="due-date-input">
            </div>
            <button type="submit">Add Task</button>
        </form>    <!-- 🧮 Filter Controls -->
        <div class="filter-controls">
            <!-- Status Filter -->
            <span>Status:</span>
            <a href="index.php<?php echo $search ? '?search=' . urlencode($search) : ''; ?>" class="<?php echo $filter === 'all' && $category === 'all' ? 'active' : ''; ?>">All</a>
            <a href="index.php?filter=pending<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="<?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending</a>
            <a href="index.php?filter=done<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="<?php echo $filter === 'done' ? 'active' : ''; ?>" style="margin-right: 20px;">Completed</a>

            <!-- Category Filter -->
            <span>Category:</span>
            <a href="index.php<?php echo $filter !== 'all' ? '?filter=' . $filter : ''; ?><?php echo $search ? ($filter !== 'all' ? '&' : '?') . 'search=' . urlencode($search) : ''; ?>" 
               class="<?php echo $category === 'all' ? 'active' : ''; ?>">All</a>
            <?php foreach ($VALID_CATEGORIES as $cat => $icon): ?>
                <a href="index.php?category=<?php echo urlencode($cat); ?><?php echo $filter !== 'all' ? '&filter=' . $filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                   class="<?php echo $category === $cat ? 'active' : ''; ?>"><?php echo "$icon $cat"; ?></a>
            <?php endforeach; ?>
        </div><!-- 📊 Task Summary -->
        <div class="task-summary">
            📋 <strong>Total:</strong> <?php echo $totalCount; ?> |
            ✅ <strong>Completed:</strong> <?php echo $doneCount; ?> |
            ⏳ <strong>Pending:</strong> <?php echo $pendingCount; ?>
        </div>    <?php
        $total = count($tasks);
        $done = 0;
        foreach ($tasks as $t) {
            if ($t['is_done']) $done++;
        }    $percent = $total ? round(($done / $total) * 100) : 0;
        $color = '#f44336'; // Red
        if ($percent >= 50) $color = '#ff9800'; // Orange
        if ($percent >= 80) $color = '#4caf50'; // Green
        ?>    <!-- 📊 Task Progress -->
        <div class="progress-container">
            <p class="progress-text"><strong>Progress:</strong> <?php echo $done; ?> / <?php echo $total; ?> tasks completed (<?php echo $percent; ?>%)</p>        <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $percent; ?>%; background: <?php echo $color; ?>;"></div>
            </div>
        </div>

        <!-- ✅ Task List -->
        <ul>
            <?php if (count($tasks) === 0): ?>
                <li style="color: gray;">No tasks yet. Add your first task!</li>
            <?php endif; ?>

            <?php foreach ($tasks as $task): ?>            <li class="<?php echo $task['is_done'] ? 'done' : ''; ?> <?php echo (!empty($task['due_date']) && strtotime($task['due_date']) < time()) ? 'overdue' : ''; ?>">
                    <div class="task-content">
                        <!-- Done checkbox -->
                        <form action="toggle_task.php" method="POST" class="toggle-form">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                            <input type="checkbox" class="task-checkbox" name="is_done" onchange="this.form.submit()" <?php echo $task['is_done'] ? 'checked' : ''; ?>>
                        </form>

                        <div class="task-details">
                            <!-- Task text -->
                            <span class="task-text">
                                <?php echo htmlspecialchars($task['task']); ?>
                            </span>

                            <div class="task-meta">
                                <!-- Category badge -->
                                <span class="category-tag">
                                    <?php echo $VALID_CATEGORIES[$task['category']] ?? $VALID_CATEGORIES['General']; ?> 
                                    <?php echo htmlspecialchars($task['category']); ?>
                                </span>

                                <!-- Due date -->
                                <?php if (!empty($task['due_date'])): 
                                    $due = strtotime($task['due_date']);
                                    $now = time();
                                    $is_overdue = $due < $now;
                                    $is_soon = ($due - $now) < 24 * 3600; // 24 hours
                                ?>
                                    <span class="due-label <?php echo $is_overdue ? 'overdue' : ($is_soon ? 'due-soon' : ''); ?>">
                                        <?php if ($is_overdue): ?>
                                            <i class="fas fa-exclamation-triangle"></i> Overdue:
                                        <?php elseif ($is_soon): ?>
                                            <i class="fas fa-clock"></i> Due soon:
                                        <?php else: ?>
                                            <i class="fas fa-calendar"></i> Due:
                                        <?php endif; ?>
                                        <?php echo date('M j, Y g:i A', $due); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="task-actions">
                            <button onclick="showEditForm(<?php echo $task['id']; ?>)" class="action-btn edit-btn" title="Edit task">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="delete_task.php" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                <button type="submit" class="action-btn delete-btn" title="Delete task">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Toast container -->
    <div id="toast" class="toast"></div>

    <script>
        // Dark mode management
        const themeSwitch = document.getElementById('themeSwitch');
        const body = document.body;
        const modeText = document.querySelector('.mode-text');
        
        // Initialize dark mode from localStorage
        function initializeDarkMode() {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const storedDarkMode = localStorage.getItem('darkMode');
            const shouldBeDark = storedDarkMode === null ? prefersDark : storedDarkMode === 'true';
            
            body.classList.toggle('dark-mode', shouldBeDark);
            themeSwitch.checked = shouldBeDark;
            updateModeText(shouldBeDark);

            // Add transition after initial load
            setTimeout(() => {
                body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
            }, 100);
        }

        // Function to update mode text
        function updateModeText(isDark) {
            modeText.textContent = isDark ? 'Light Mode' : 'Dark Mode';
        }

        // Handle theme toggle
        themeSwitch.addEventListener('change', () => {
            const isDark = themeSwitch.checked;
            body.classList.toggle('dark-mode', isDark);
            localStorage.setItem('darkMode', isDark);
            updateModeText(isDark);
            showToast(isDark ? '🌙 Dark mode enabled' : '☀️ Light mode enabled');
        });

        // Initialize dark mode
        initializeDarkMode();

        // Show welcome toast if just logged in
        <?php if (isset($_SESSION['just_logged_in'])): ?>
            showToast('👋 Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!');
            <?php unset($_SESSION['just_logged_in']); ?>
        <?php endif; ?>

        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type;
            if (body.classList.contains('dark-mode')) {
                toast.classList.add('dark');
            }
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        // Show toast message if exists
        <?php if (isset($_SESSION['toast_message'])): ?>
            showToast('<?php echo addslashes($_SESSION['toast_message']); ?>');
            <?php unset($_SESSION['toast_message']); ?>
        <?php endif; ?>
    </script>
</body>
</html>
