<?php
require_once 'config.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header('Location: ' . $path);
    exit;
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        redirect('index.php');
    }
}

function current_user() {
    global $pdo;
    if (empty($_SESSION['user_id'])) return null;
    $stmt = $pdo->prepare('SELECT id, full_name, email FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function app_header($title = 'StudyFlow') {
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>' . e($title) . '</title><link rel="stylesheet" href="style.css"><link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet"></head>';
}

function auth_message() {
    $success = flash('success');
    $error = flash('error');
    if ($success) echo '<div class="card" style="border-color:rgba(16,185,129,.4);margin-bottom:16px;color:#10b981">' . e($success) . '</div>';
    if ($error) echo '<div class="card" style="border-color:rgba(239,68,68,.4);margin-bottom:16px;color:#ef4444">' . e($error) . '</div>';
}

function sidebar($active = 'dashboard') {
    $user = current_user();
    $name = $user ? $user['full_name'] : 'Student';
    $initial = strtoupper(substr($name, 0, 1));
    $items = [
        'dashboard' => ['Dashboard', 'dashboard.php', '🏠'],
        'schedule' => ['Schedule', 'schedule.php', '📅'],
        'tasks' => ['Tasks', 'tasks.php', '✅'],
        'progress' => ['Progress', 'progress.php', '📊'],
    ];
    echo '<aside class="sidebar"><div class="sidebar-brand"><div class="brand-icon-sm">📚</div><span>StudyFlow</span></div><nav class="sidebar-nav">';
    foreach ($items as $key => $item) {
        $class = $active === $key ? 'nav-item active' : 'nav-item';
        echo '<a href="' . e($item[1]) . '" class="' . $class . '"><span class="nav-icon">' . $item[2] . '</span><span>' . e($item[0]) . '</span></a>';
    }
    echo '</nav><div class="sidebar-footer"><div class="user-avatar-sm">' . e($initial) . '</div><div class="user-info-sm"><div class="user-name-sm">' . e($name) . '</div><div class="user-role">Learner</div></div><a class="logout-btn" href="logout.php" title="Logout">⏻</a></div></aside>';
}

function topbar() {
    echo '<header class="topbar"><div class="topbar-search"><span class="search-icon">🔍</span><form action="tasks.php" method="get" style="width:100%"><input type="text" name="q" placeholder="Search tasks, subjects..." class="search-input"></form></div><div class="topbar-actions"><a class="icon-btn" href="task_form.php">＋ Add Task</a><a class="notif-btn" href="tasks.php">🔔<span class="notif-badge">3</span></a></div></header>';
}

function app_shell_start($active = 'dashboard') {
    echo '<body><div id="app">';
    sidebar($active);
    echo '<main class="main-content">';
    topbar();
}

function app_shell_end() {
    echo '</main></div></body></html>';
}
?>
