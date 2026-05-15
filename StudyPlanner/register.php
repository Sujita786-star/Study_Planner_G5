<?php
require_once 'functions.php';
require_once 'config.php';

if (!empty($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $confirm === '') {

        flash('error', 'Please fill in all fields.');

    } elseif ($password !== $confirm) {

        flash('error', 'Passwords do not match.');

    } elseif (strlen($password) < 8) {

        flash('error', 'Password must be at least 8 characters.');

    } else {

        // hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // mysqli insert
        $stmt = $conn->prepare("
            INSERT INTO users (full_name, email, password_hash)
            VALUES (?, ?, ?)
        ");

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {

            flash('success', 'Registration successful! Please login.');
            redirect('index.php');

        } else {

            flash('error', 'Email already exists or error occurred.');
        }

        $stmt->close();
    }
}

app_header('StudyFlow – Register');
?>