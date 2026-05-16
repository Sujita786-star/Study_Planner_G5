<?php
require_once 'functions.php';
session_destroy();
app_header('StudyFlow – Logged Out');
?>
<body class="auth-page"><div class="auth-bg"><div class="bg-orb orb1"></div><div class="bg-orb orb2"></div><div class="bg-orb orb3"></div></div><div class="auth-container"><div class="auth-brand"><div class="brand-icon">👋</div><h1 class="brand-name">Goodbye!</h1><p class="brand-tagline">You have been logged out successfully.</p></div><div class="auth-card" style="text-align:center"><h2 class="auth-title">Session ended</h2><p class="auth-sub">Come back anytime to continue planning your study.</p><a class="btn-primary full-width" style="display:block" href="index.php">Back to Sign In</a></div></div></body></html>