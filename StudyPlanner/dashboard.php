<?php
require_once 'functions.php'; require_login();
$user = current_user();
$uid = $user['id'];
$stats = [];
$stats['total'] = $pdo->prepare('SELECT COUNT(*) FROM tasks WHERE user_id=?'); $stats['total']->execute([$uid]); $total = (int)$stats['total']->fetchColumn();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id=? AND status='completed'"); $stmt->execute([$uid]); $done = (int)$stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id=? AND status!='completed'"); $stmt->execute([$uid]); $pending = (int)$stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT COALESCE(SUM(hours),0) FROM tasks WHERE user_id=? AND status='completed'"); $stmt->execute([$uid]); $hours = $stmt->fetchColumn();
$today = date('Y-m-d');
$stmt = $pdo->prepare('SELECT * FROM tasks WHERE user_id=? AND due_date=? ORDER BY FIELD(priority,"high","medium","low"), id DESC LIMIT 5'); $stmt->execute([$uid,$today]); $todayTasks = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT subject, COUNT(*) AS c FROM tasks WHERE user_id=? GROUP BY subject ORDER BY c DESC LIMIT 6'); $stmt->execute([$uid]); $subjects = $stmt->fetchAll();
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id=? AND status!='completed' AND due_date>=CURDATE() ORDER BY due_date ASC LIMIT 4"); $stmt->execute([$uid]); $upcoming = $stmt->fetchAll();
app_header('StudyFlow – Dashboard'); app_shell_start('dashboard');
?>
<section class="page active" id="dashboardPage">
  <?php auth_message(); ?>
  <div class="page-header"><div><h2 class="page-title">Good <?= date('H') < 12 ? 'morning' : (date('H') < 17 ? 'afternoon' : 'evening') ?>, <?= e(explode(' ', $user['full_name'])[0]) ?>!</h2><p class="page-subtitle"><?= date('l, j F Y') ?></p></div><div class="streak-badge">🔥 7 day streak</div></div>
  <div class="stats-grid">
    <div class="stat-card stat-blue"><div class="stat-icon">📋</div><div class="stat-info"><div class="stat-value"><?= $total ?></div><div class="stat-label">Total Tasks</div></div></div>
    <div class="stat-card stat-green"><div class="stat-icon">✅</div><div class="stat-info"><div class="stat-value"><?= $done ?></div><div class="stat-label">Completed</div></div></div>
    <div class="stat-card stat-orange"><div class="stat-icon">⏳</div><div class="stat-info"><div class="stat-value"><?= $pending ?></div><div class="stat-label">Pending</div></div></div>
    <div class="stat-card stat-purple"><div class="stat-icon">⏰</div><div class="stat-info"><div class="stat-value"><?= e($hours) ?>h</div><div class="stat-label">Study Hours</div></div></div>
  </div>
  <div class="dash-cols"><div class="dash-col"><div class="card"><div class="card-header"><h3 class="card-title">Today's Tasks</h3><a href="tasks.php" class="card-link">View all</a></div><div class="tasks-mini-list">
    <?php if (!$todayTasks): ?><div class="empty-state-sm">No tasks due today! 🎉</div><?php endif; ?>
    <?php foreach ($todayTasks as $t): ?><div class="task-mini"><span class="task-mini-check <?= $t['status']==='completed'?'done':'' ?>"></span><span class="task-mini-text <?= $t['status']==='completed'?'done':'' ?>"><?= e($t['title']) ?></span><span class="task-mini-badge badge-<?= e($t['priority']) ?>"><?= e($t['priority']) ?></span></div><?php endforeach; ?>
  </div></div><div class="card"><div class="card-header"><h3 class="card-title">Weekly Progress</h3></div><div class="weekly-bars">
  <?php foreach (['Mon'=>40,'Tue'=>75,'Wed'=>55,'Thu'=>90,'Fri'=>65,'Sat'=>30,'Sun'=>20] as $d=>$pct): ?><div class="week-bar-wrap"><div class="week-bar-track"><div class="week-bar-fill" style="height:<?= $pct ?>%"></div></div><span class="week-bar-label"><?= $d ?></span></div><?php endforeach; ?>
  </div></div></div><div class="dash-col"><div class="card"><div class="card-header"><h3 class="card-title">Subject Breakdown</h3></div><div class="subject-list">
  <?php if (!$subjects): ?><div class="empty-state-sm">No subjects yet</div><?php endif; ?>
  <?php foreach ($subjects as $s): ?><div class="subject-row"><div class="subject-dot"></div><span class="subject-name"><?= e($s['subject']) ?></span><span class="subject-count"><?= e($s['c']) ?> tasks</span></div><?php endforeach; ?>
  </div></div><div class="card"><div class="card-header"><h3 class="card-title">Upcoming Deadlines</h3></div><div class="deadline-list">
  <?php if (!$upcoming): ?><div class="empty-state-sm">No upcoming deadlines 🎉</div><?php endif; ?>
  <?php foreach ($upcoming as $t): $days=(new DateTime($today))->diff(new DateTime($t['due_date']))->days; ?><div class="deadline-item <?= $days<=1?'deadline-urgent':'' ?>"><div class="deadline-info"><div class="deadline-name"><?= e($t['title']) ?></div><div class="deadline-sub"><?= e($t['subject']) ?></div></div><div class="deadline-days"><?= $days===0?'Today!':($days===1?'Tomorrow':$days.' days') ?></div></div><?php endforeach; ?>
  </div></div></div></div>
</section>
<?php app_shell_end(); ?>
