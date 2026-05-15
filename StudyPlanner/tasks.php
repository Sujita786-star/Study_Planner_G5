<?php
require_once 'functions.php'; require_login();
$user = current_user(); $uid = $user['id'];
if (isset($_GET['delete'])) { $stmt=$pdo->prepare('DELETE FROM tasks WHERE id=? AND user_id=?'); $stmt->execute([(int)$_GET['delete'],$uid]); flash('success','Task deleted.'); redirect('tasks.php'); }
$q = trim($_GET['q'] ?? ''); $status = $_GET['status'] ?? 'all'; $priority = $_GET['priority'] ?? 'all';
$sql='SELECT * FROM tasks WHERE user_id=?'; $params=[$uid];
if ($q !== '') { $sql.=' AND (title LIKE ? OR subject LIKE ? OR notes LIKE ?)'; $like='%'.$q.'%'; array_push($params,$like,$like,$like); }
if (in_array($status,['pending','in-progress','completed'],true)) { $sql.=' AND status=?'; $params[]=$status; }
if (in_array($priority,['low','medium','high'],true)) { $sql.=' AND priority=?'; $params[]=$priority; }
$sql.=' ORDER BY FIELD(status,"pending","in-progress","completed"), FIELD(priority,"high","medium","low"), due_date ASC';
$stmt=$pdo->prepare($sql); $stmt->execute($params); $tasks=$stmt->fetchAll();
app_header('StudyFlow – Tasks'); app_shell_start('tasks');
?>
<section class="page active" id="tasksPage"><?php auth_message(); ?><div class="page-header"><div><h2 class="page-title">Tasks</h2><p class="page-subtitle">Manage your study tasks</p></div><a class="btn-primary" href="task_form.php">＋ Add Task</a></div>
<form class="filter-bar" method="get" action="tasks.php"><input class="filter-select" type="text" name="q" value="<?= e($q) ?>" placeholder="Search"><select class="filter-select" name="status"><option value="all">All Status</option><option value="pending" <?= $status==='pending'?'selected':'' ?>>Pending</option><option value="in-progress" <?= $status==='in-progress'?'selected':'' ?>>In Progress</option><option value="completed" <?= $status==='completed'?'selected':'' ?>>Completed</option></select><select class="filter-select" name="priority"><option value="all">All Priorities</option><option value="high" <?= $priority==='high'?'selected':'' ?>>High</option><option value="medium" <?= $priority==='medium'?'selected':'' ?>>Medium</option><option value="low" <?= $priority==='low'?'selected':'' ?>>Low</option></select><button class="btn-secondary" type="submit">Filter</button><a class="btn-secondary" href="tasks.php">Clear</a></form>
<div class="tasks-grid"><?php if (!$tasks): ?><div class="no-tasks"><div class="no-tasks-icon">📭</div><div class="no-tasks-title">No tasks found</div></div><?php endif; ?>
<?php foreach ($tasks as $t): $overdue=$t['due_date'] < date('Y-m-d') && $t['status']!=='completed'; ?><div class="task-card <?= e($t['priority']) ?>"><div class="task-card-header"><div class="task-card-title <?= $t['status']==='completed'?'done':'' ?>"><?= e($t['title']) ?></div><div class="task-card-actions"><a class="task-action-btn" href="task_form.php?id=<?= $t['id'] ?>">✏️</a><a class="task-action-btn delete" href="tasks.php?delete=<?= $t['id'] ?>">🗑</a></div></div><div class="task-card-meta"><span class="task-subject-tag"><?= e($t['subject']) ?></span><span class="task-due <?= $overdue?'overdue':'' ?>">📅 <?= e(date('j M Y', strtotime($t['due_date']))) ?></span><span class="task-mini-badge badge-<?= e($t['priority']) ?>"><?= e($t['priority']) ?></span></div><p style="color:var(--text2);font-size:.875rem;margin-top:10px"><?= e($t['notes']) ?></p><div class="task-card-footer"><span><?= e($t['hours']) ?> hours</span><span><?= e($t['status']) ?></span></div></div><?php endforeach; ?>
</div></section><?php app_shell_end(); ?>
