<?php
// register.php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $age = intval($_POST['age']);
    $password = $_POST['password'];
    $location = trim($_POST['location']);
    $phone = trim($_POST['phone']);
    $university = trim($_POST['university']);
    $education_duration = trim($_POST['education_duration']);
    $skills = trim($_POST['skills']);

    if (empty($username) || empty($email) || empty($age) || empty($password) || empty($location) || empty($phone)) {
        echo "<script>alert('All fields are required.'); window.location.href='register.html';</script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Email is already registered. Please try another email.'); window.location.href='register.html';</script>";
    } else {
        $profile_photo = null; // Using Gravatar, no upload

        $stmt = $pdo->prepare("INSERT INTO users (username, email, age, password, location, phone, university, education_duration, skills, profile_photo)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $age, $hashed_password, $location, $phone, $university, $education_duration, $skills, $profile_photo])) {
            header("Location: login.html");
            exit();
        } else {
            echo "<script>alert('Registration failed. Please try again.'); window.location.href='register.html';</script>";
        }
    }
}
?>