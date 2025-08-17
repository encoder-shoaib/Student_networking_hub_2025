<?php
session_start();
include('db.php');

$email = $user['email']; // User email fetched from database
echo $email; // Debugging

$gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=200&d=mp";
echo $gravatar_url; // Debugging


?>