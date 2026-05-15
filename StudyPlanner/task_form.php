<?php
require_once 'functions.php'; require_login();
$user=current_user(); $uid=$user['id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$task = ['title'=>'','subject'=>'','priority'=>'medium','due_date'=>date('Y-m-d'),'status'=>'pending','notes'=>'','hours'=>'1.0'];
if ($id) { $stmt=$pdo->prepare('SELECT * FROM tasks WHERE id=? AND user_id=?'); $stmt->execute([$id,$uid]); $task=$stmt->fetch(); if (!$task) redirect('tasks.php'); }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $title=trim($_POST['title']??''); $subject=trim($_POST['subject']??''); $priority=$_POST['priority']??'medium'; $due=$_POST['due_date']??''; $status=$_POST['status']??'pending'; $hours=(float)($_POST['hours']??1); $notes=trim($_POST['notes']??'');
    if ($title==='' || $subject==='' || $due==='') flash('error','Please fill in title, subject and due date.');
    elseif (!in_array($priority,['low','medium','high'],true) || !in_array($status,['pending','in-progress','completed'],true)) flash('error','Invalid task option.');
    else {
        if ($id) { $stmt=$pdo->prepare('UPDATE tasks SET title=?, subject=?, priority=?, due_date=?, status=?, notes=?, hours=? WHERE id=? AND user_id=?'); $stmt->execute([$title,$subject,$priority,$due,$status,$notes,$hours,$id,$uid]); flash('success','Task updated.'); }
        else { $stmt=$pdo->prepare('INSERT INTO tasks (user_id,title,subject,priority,due_date,status,notes,hours) VALUES (?,?,?,?,?,?,?,?)'); $stmt->execute([$uid,$title,$subject,$priority,$due,$status,$notes,$hours]); flash('success','Task added.'); }
        redirect('tasks.php');
    }
}
app_header($id?'Edit Task':'Add Task'); app_shell_start('tasks');
?>
<section class="page active"><div class="page-header"><div><h2 class="page-title"><?= $id?'Edit Task':'Add New Task' ?></h2><p class="page-subtitle">Save task information to the database</p></div></div><?php auth_message(); ?>
<form class="card" method="post" action="task_form.php<?= $id?'?id='.$id:'' ?>">
<div class="form-group"><label class="form-label">Task Title</label><input class="form-input" type="text" name="title" value="<?= e($task['title']) ?>" required></div>
<div class="form-row-2"><div class="form-group"><label class="form-label">Subject</label><input class="form-input" type="text" name="subject" value="<?= e($task['subject']) ?>" required></div><div class="form-group"><label class="form-label">Priority</label><select class="form-input" name="priority"><option value="low" <?= $task['priority']==='low'?'selected':'' ?>>Low</option><option value="medium" <?= $task['priority']==='medium'?'selected':'' ?>>Medium</option><option value="high" <?= $task['priority']==='high'?'selected':'' ?>>High</option></select></div></div>
<div class="form-row-2"><div class="form-group"><label class="form-label">Due Date</label><input class="form-input" type="date" name="due_date" value="<?= e($task['due_date']) ?>" required></div><div class="form-group"><label class="form-label">Status</label><select class="form-input" name="status"><option value="pending" <?= $task['status']==='pending'?'selected':'' ?>>Pending</option><option value="in-progress" <?= $task['status']==='in-progress'?'selected':'' ?>>In Progress</option><option value="completed" <?= $task['status']==='completed'?'selected':'' ?>>Completed</option></select></div></div>
<div class="form-group"><label class="form-label">Estimated Hours</label><input class="form-input" type="number" step="0.5" min="0" name="hours" value="<?= e($task['hours']) ?>"></div>
<div class="form-group"><label class="form-label">Notes</label><textarea class="form-input form-textarea" name="notes"><?= e($task['notes']) ?></textarea></div>
<button class="btn-primary" type="submit">Save Task</button> <a class="btn-secondary" href="tasks.php">Cancel</a>
</form></section><?php app_shell_end(); ?>
