<?php
require_once 'functions.php';
$link = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email === '') {
        flash('error', 'Please enter your email address.');
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $token = bin2hex(random_bytes(24));
            $expires = date('Y-m-d H:i:s', time() + 3600);
            $update = $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?');
            $update->execute([$token, $expires, $user['id']]);
            $link = 'reset_password.php?token=' . urlencode($token);
            flash('success', 'Reset link created. For this XAMPP assignment version, click the link below.');
        } else {
            flash('success', 'If that email exists, a reset link has been created.');
        }
    }
}
app_header('StudyFlow – Forgot Password');
?>
<body class="auth-page">
  <div class="auth-bg"><div class="bg-orb orb1"></div><div class="bg-orb orb2"></div><div class="bg-orb orb3"></div></div>
  <div class="auth-container">
    <div class="auth-brand"><div class="brand-icon">🔐</div><h1 class="brand-name">Forgot Password</h1><p class="brand-tagline">Reset your StudyFlow account</p></div>
    <?php auth_message(); ?>
    <form class="auth-card" method="post" action="forgot_password.php">
      <h2 class="auth-title">Reset password</h2>
      <p class="auth-sub">Enter your email address to create a password reset link.</p>
      <div class="form-group"><label class="form-label">Email address</label><input type="email" name="email" class="form-input" required></div>
      <button class="btn-primary full-width" type="submit">Create Reset Link</button>
      <?php if ($link): ?><p class="auth-switch"><a href="<?= e($link) ?>">Open reset password page</a></p><?php endif; ?>
      <p class="auth-switch"><a href="index.php">Back to sign in</a></p>
    </form>
  </div>
</body>
</html>
