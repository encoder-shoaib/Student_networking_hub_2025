<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_networking_hub";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['q'])) {
    $search_query = $_GET['q'];
    
    // Escape the input to prevent SQL injection
    $search_query = $conn->real_escape_string($search_query);
    
    // Query to search for users by username
    $sql = "SELECT * FROM users WHERE username LIKE '%$search_query%'";
    $result = $conn->query($sql);
    
    // Check if any user is found
    if ($result->num_rows > 0) {
        // Output user profiles
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<h3 class='font-semibold text-pink-500'>Name: {$row['username']}</h3>";
            echo "<a href='profile.php?id={$row['id']}' class='text-blue-500'>View Profile</a>";
            echo "</div>";
            echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>"; // Refresh the page after 2 seconds

        }
    } else {
        // No user found, show error message
        echo "<div class='text-red-500'>No user found matching your search.</div>";
        echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>"; // Refresh the page after 2 seconds
    }
}

$conn->close();
?>
