<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["post_image"])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT email, username FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    $email = $user['email'];
    $username = $user['username'];
    $content = trim($_POST['content']);
    $gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=200&d=mp";

    $image_name = $_FILES["post_image"]["name"];
    $image_tmp_name = $_FILES["post_image"]["tmp_name"];
    $image_folder = "Uploads/" . basename($image_name);

    if (move_uploaded_file($image_tmp_name, $image_folder)) {
        $stmt = $conn->prepare("INSERT INTO posts (username, profile_picture, content, image, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $gravatar_url, $content, $image_folder, $email);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Failed to upload image.'); window.location.href='index.php';</script>";
    }
}
?>