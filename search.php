<?php
// search.php
include('db.php');

if (isset($_GET['q'])) {
    $search_query = $_GET['q'];

    $search_query = $conn->real_escape_string($search_query);

    $sql = "SELECT * FROM users WHERE username LIKE '%$search_query%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<h3 class='font-semibold text-pink-500'>Name: {$row['username']}</h3>";
            echo "<a href='profile.php?id={$row['id']}' class='text-blue-500'>View Profile</a>";
            echo "</div>";
        }
    } else {
        echo "<div class='text-red-500'>No user found matching your search.</div>";
    }
}

$conn->close();
?>