<?php
// like_post.php
include('db.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Check if user already liked the post
    $check_like = $conn->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
    $check_like->bind_param("ii", $post_id, $user_id);
    $check_like->execute();
    $result = $check_like->get_result();

    if ($result->num_rows > 0) {
        // Unlike
        $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
        $stmt = $conn->prepare("UPDATE posts SET likes = likes - 1 WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        echo json_encode(["success" => true, "action" => "unliked"]);
    } else {
        // Like
        $stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
        $stmt = $conn->prepare("UPDATE posts SET likes = likes + 1 WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        echo json_encode(["success" => true, "action" => "liked"]);
    }
}
$conn->close();
?>