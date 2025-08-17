<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_networking_hub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $post_id = $_POST['post_id'];
    $username = $_POST['username'];

    // Check if user already liked the post
    $check_like = $conn->prepare("SELECT * FROM likes WHERE post_id = ? AND username = ?");
    $check_like->bind_param("is", $post_id, $username);
    $check_like->execute();
    $result = $check_like->get_result();

    if ($result->num_rows > 0) {
        // Unlike the post
        $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND username = ?");
        $stmt->bind_param("is", $post_id, $username);
        $stmt->execute();
        $stmt = $conn->prepare("UPDATE posts SET likes = likes - 1 WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
    } else {
        // Like the post
        $stmt = $conn->prepare("INSERT INTO likes (post_id, username) VALUES (?, ?)");
        $stmt->bind_param("is", $post_id, $username);
        $stmt->execute();
        $stmt = $conn->prepare("UPDATE posts SET likes = likes + 1 WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
    }
    echo json_encode(["success" => true]);
}
$conn->close();
?>
