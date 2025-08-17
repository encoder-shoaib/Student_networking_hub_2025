<?php
// reject_friend.php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['request_id'])) {
    $request_id = intval($_POST['request_id']);

    $stmt = $conn->prepare("UPDATE friend_requests SET status = 'rejected' WHERE id = ? AND receiver_id = ?");
    $stmt->bind_param("ii", $request_id, $_SESSION['user_id']);
    $stmt->execute();

    header("Location: profile.php");
    exit();
}
?>