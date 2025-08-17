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
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO comments (post_id, username, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $post_id, $username, $comment);
    $stmt->execute();
    echo json_encode(["success" => true]);
}

$conn->close();
?>
