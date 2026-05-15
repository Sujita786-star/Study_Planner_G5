<?php
require_once 'functions.php';
require_login();

$user = current_user();
$uid = $user['id'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$session = [
    'title' => '',
    'subject' => '',
    'session_date' => date('Y-m-d'),
    'start_time' => '09:00',
    'end_time' => '10:00',
    'location' => '',
    'notes' => ''
];

if ($id) {

    $stmt = mysqli_prepare($conn,
        "SELECT * FROM sessions
         WHERE id=? AND user_id=?"
    );

    mysqli_stmt_bind_param($stmt, "ii", $id, $uid);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $session = mysqli_fetch_assoc($result);

    if (!$session) {
        redirect('schedule.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $subject = trim($_POST['subject']);
    $date = $_POST['session_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $location = trim($_POST['location']);
    $notes = trim($_POST['notes']);

    if (
        $title == '' ||
        $subject == '' ||
        $date == '' ||
        $start == '' ||
        $end == ''
    ) {

        flash('error', 'Please fill all required fields.');

    } else {

        if ($id) {

            $stmt = mysqli_prepare($conn,
                "UPDATE sessions
                 SET title=?, subject=?, session_date=?,
                     start_time=?, end_time=?,
                     location=?, notes=?
                 WHERE id=? AND user_id=?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssssssii",
                $title,
                $subject,
                $date,
                $start,
                $end,
                $location,
                $notes,
                $id,
                $uid
            );

            mysqli_stmt_execute($stmt);

            flash('success', 'Session updated.');

        } else {

            $stmt = mysqli_prepare($conn,
                "INSERT INTO sessions
                (
                    user_id,
                    title,
                    subject,
                    session_date,
                    start_time,
                    end_time,
                    location,
                    notes
                )
                VALUES (?,?,?,?,?,?,?,?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "isssssss",
                $uid,
                $title,
                $subject,
                $date,
                $start,
                $end,
                $location,
                $notes
            );

            mysqli_stmt_execute($stmt);

            flash('success', 'Session added.');
        }

        redirect('schedule.php');
    }
}

app_header($id ? 'Edit Session' : 'Add Session');
app_shell_start('schedule');
?>

<section class="page active">

<?php auth_message(); ?>

<div class="page-header">
    <div>
        <h2 class="page-title">
            <?= $id ? 'Edit Session' : 'Add Study Session' ?>
        </h2>

        <p class="page-subtitle">
            Save schedule information
        </p>
    </div>
</div>

<form method="POST" class="card">

    <div class="form-group">
        <label class="form-label">Session Title</label>

        <input
            type="text"
            name="title"
            class="form-input"
            value="<?= e($session['title']) ?>"
            required
        >
    </div>

    <div class="form-row-2">

        <div class="form-group">
            <label class="form-label">Subject</label>

            <input
                type="text"
                name="subject"
                class="form-input"
                value="<?= e($session['subject']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label class="form-label">Date</label>

            <input
                type="date"
                name="session_date"
                class="form-input"
                value="<?= e($session['session_date']) ?>"
                required
            >
        </div>

    </div>

    <div class="form-row-2">

        <div class="form-group">
            <label class="form-label">Start Time</label>

            <input
                type="time"
                name="start_time"
                class="form-input"
                value="<?= substr($session['start_time'],0,5) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label class="form-label">End Time</label>

            <input
                type="time"
                name="end_time"
                class="form-input"
                value="<?= substr($session['end_time'],0,5) ?>"
                required
            >
        </div>

    </div>

    <div class="form-group">
        <label class="form-label">Location</label>

        <input
            type="text"
            name="location"
            class="form-input"
            value="<?= e($session['location']) ?>"
        >
    </div>

    <div class="form-group">
        <label class="form-label">Notes</label>

        <textarea
            name="notes"
            class="form-input form-textarea"
        ><?= e($session['notes']) ?></textarea>
    </div>

    <button type="submit" class="btn-primary">
        Save Session
    </button>

    <a href="schedule.php" class="btn-secondary">
        Cancel
    </a>

</form>

</section>

<?php app_shell_end(); ?>