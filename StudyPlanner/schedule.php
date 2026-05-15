<?php
require_once 'functions.php';
require_login();

$user = current_user();
$uid = $user['id'];

if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $stmt = mysqli_prepare($conn,
        "DELETE FROM sessions WHERE id=? AND user_id=?"
    );

    mysqli_stmt_bind_param($stmt, "ii", $id, $uid);
    mysqli_stmt_execute($stmt);

    flash('success', 'Study session deleted.');
    redirect('schedule.php');
}

$stmt = mysqli_prepare($conn,
    "SELECT * FROM sessions
     WHERE user_id=?
     ORDER BY session_date ASC, start_time ASC"
);

mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$sessions = [];

while ($row = mysqli_fetch_assoc($result)) {
    $sessions[] = $row;
}

app_header('StudyFlow – Schedule');
app_shell_start('schedule');
?>

<section class="page active" id="schedulePage">

<?php auth_message(); ?>

<div class="page-header">
    <div>
        <h2 class="page-title">Schedule</h2>
        <p class="page-subtitle">Plan your study sessions</p>
    </div>

    <a href="session_form.php" class="btn-primary">
        ＋ Add Session
    </a>
</div>

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Scheduled Sessions</h3>
    </div>

    <div class="sessions-list">

        <?php if (empty($sessions)): ?>

            <div class="empty-state-sm">
                No sessions added yet.
            </div>

        <?php endif; ?>

        <?php foreach ($sessions as $s): ?>

            <div class="session-item">

                <div class="session-info">

                    <div class="session-title">
                        <?= e($s['title']) ?>
                    </div>

                    <div class="session-meta">
                        <?= e($s['subject']) ?>
                        •
                        <?= date('d M Y', strtotime($s['session_date'])) ?>
                        •
                        <?= substr($s['start_time'],0,5) ?>
                        -
                        <?= substr($s['end_time'],0,5) ?>
                    </div>

                </div>

                <div class="task-card-actions">

                    <a class="task-action-btn"
                       href="session_form.php?id=<?= $s['id'] ?>">
                        ✏️
                    </a>

                    <a class="task-action-btn delete"
                       href="schedule.php?delete=<?= $s['id'] ?>"
                       onclick="return confirm('Delete session?')">
                        🗑
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

</section>

<?php app_shell_end(); ?>