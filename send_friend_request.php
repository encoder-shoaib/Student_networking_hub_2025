<?php
// send_friend_request.php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['receiver_id'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = intval($_POST['receiver_id']);

    if ($sender_id == $receiver_id) {
        header("Location: profile.php?id=$receiver_id");
        exit();
    }

    // Check if request already exists
    $check_stmt = $conn->prepare("SELECT * FROM friend_requests WHERE sender_id = ? AND receiver_id = ?");
    $check_stmt->bind_param("ii", $sender_id, $receiver_id);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO friend_requests (sender_id, receiver_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $sender_id, $receiver_id);
        $stmt->execute();
    }

    header("Location: profile.php?id=$receiver_id");
    exit();
}
?>