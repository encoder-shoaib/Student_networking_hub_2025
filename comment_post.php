<?php
// comment_post.php
include('db.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['post_id']) || !isset($_POST['comment'])) {
        echo json_encode(["success" => false, "error" => "Missing required fields"]);
        exit();
    }

    $post_id = filter_var($_POST['post_id'], FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $comment = trim($_POST['comment']);

    if (!$post_id || empty($comment)) {
        echo json_encode(["success" => false, "error" => "Invalid input data"]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, username, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $post_id, $user_id, $username, $comment);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Database error: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}

$conn->close();
?>