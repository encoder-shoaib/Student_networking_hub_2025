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
            echo "<div class='user-profile'>";
            echo "<h3 class='font-semibold'>{$row['username']}</h3>";
            echo "<a href='profile.php?id={$row['id']}' class='text-blue-500'>View Profile</a>";
            echo "</div>";
        }
    } else {
        // No user found, show error message
        echo "<div class='text-red-500'>No user found matching your search.</div>";
    }
}

$conn->close();
?>



            <!-- Search Container -->
             <div class="search-container">
                    <div class="relative md:ml-4 flex gap-4">
                        <input id="searchInput" type="text" class="bg-white text-gray-900 px-4 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search">
                     <div>
                            <button id="searchButton" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full">Search</button>
                    </div>
                </div>
                 <div id="results" class="results">
                        <!-- Search results will appear here -->
                    </div>
            </div>