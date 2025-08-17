<?php
include('db.php');

// Check if the uploads directory exists and create it if not
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Input sanitization
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $age = intval($_POST['age']);
    $password = $_POST['password'];
    $location = trim($_POST['location']);
    $phone = trim($_POST['phone']);
    $university = trim($_POST['university']);
    $education_duration = trim($_POST['education_duration']);
    $skills = trim($_POST['skills']);
    $image_url = trim($_POST['image_url']);  // Get the image URL from the POST request (assuming it's passed in the form)

    // Validate inputs
    if (empty($username) || empty($email) || empty($age) || empty($password) || empty($location) || empty($phone)) {
        echo "<script>alert('All fields are required.'); window.location.href='register.html';</script>";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check for duplicate email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Email is already registered. Please try another email.'); window.location.href='register.html';</script>";
    } else {
        // Profile photo handling from URL
        $profile_photo = null;
        if (!empty($image_url)) {
            // Validate the image URL (simple check)
            if (filter_var($image_url, FILTER_VALIDATE_URL)) {
                $profile_photo = $image_url;  // Store the URL directly
            } else {
                echo "<script>alert('Invalid image URL.'); window.location.href='register.html';</script>";
                exit();
            }
        }

        // Insert user data
        $stmt = $pdo->prepare("INSERT INTO users (username, email, age, password, location, phone, university, education_duration, skills, profile_photo) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $age, $hashed_password, $location, $phone, $university, $education_duration, $skills, $profile_photo])) {
            session_start();
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header("Location: login.html");
            exit();
        } else {
            echo "<script>alert('Registration failed. Please try again.'); window.location.href='register.html';</script>";
        }
    }
}
?>
