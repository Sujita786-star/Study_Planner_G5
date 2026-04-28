<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {

        $stmt->bind_result($id, $fullname, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {

            $_SESSION['user_id'] = $id;
            $_SESSION['fullname'] = $fullname;

            header("Location: dashboard.php");

        } else {
            echo "Wrong password!";
        }

    } else {
        echo "User not found!";
    }
}
?>