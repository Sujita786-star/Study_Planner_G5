<?php
require_once 'functions.php';
$token = $_GET['token'] ?? $_POST['token'] ?? '';
$valid = false;
if ($token !== '') {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()');
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    $valid = (bool)$user;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (!$valid) flash('error', 'This reset link is invalid or expired.');
    elseif ($password !== $confirm) flash('error', 'Passwords do not match.');
    elseif (strlen($password) < 8) flash('error', 'Password must be at least 8 characters.');
    else {
        $update = $pdo->prepare('UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?');
        $update->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        flash('success', 'Password updated. You can now sign in.');
        redirect('index.php');
    }
}
app_header('StudyFlow – Reset Password');
?>
<body class="auth-page"><div class="auth-bg"><div class="bg-orb orb1"></div><div class="bg-orb orb2"></div><div class="bg-orb orb3"></div></div><div class="auth-container">
<div class="auth-brand"><div class="brand-icon">🔑</div><h1 class="brand-name">New Password</h1><p class="brand-tagline">Choose a secure password</p></div><?php auth_message(); ?>
<form class="auth-card" method="post" action="reset_password.php">
<input type="hidden" name="token" value="<?= e($token) ?>">
<h2 class="auth-title">Set new password</h2>
<?php if (!$valid): ?><p class="auth-sub">This reset link is invalid or expired. Please request a new link.</p><a class="btn-primary full-width" style="display:block;text-align:center" href="forgot_password.php">Request New Link</a><?php else: ?>
<div class="form-group"><label class="form-label">New password</label><input type="password" name="password" class="form-input" required minlength="8"></div>
<div class="form-group"><label class="form-label">Confirm new password</label><input type="password" name="confirm_password" class="form-input" required minlength="8"></div>
<button class="btn-primary full-width" type="submit">Update Password</button><?php endif; ?>
<p class="auth-switch"><a href="index.php">Back to sign in</a></p>
</form></div></body></html>
