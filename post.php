<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_networking_hub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle new post submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["post_image"])) {
    // Assuming user is logged in and user data is available in session
    session_start();
    $user_id = $_SESSION['user_id'];  // Get the logged-in user's ID
    $query = "SELECT email FROM users WHERE id = $user_id";  // Fetch user email
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    
    $email = $user['email'];  // User's email

    $username = $_POST['username'];
    $content = $_POST['content'];
    
    // Handle image upload
    $image_name = $_FILES["post_image"]["name"];
    $image_tmp_name = $_FILES["post_image"]["tmp_name"];
    $image_folder = "uploads/" . basename($image_name);

    if (move_uploaded_file($image_tmp_name, $image_folder)) {
        // Insert post into the database, including the email
        $stmt = $conn->prepare("INSERT INTO posts (username, profile_picture, content, image, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $profile_picture, $content, $image_folder, $email);
        $profile_picture = "path_to_default_profile_picture"; // Replace with actual profile picture if available
        $stmt->execute();
        $stmt->close();
        
        // Redirect to the index page after successful post submission
        header("Location: index.php");
        exit();
    }
}

// Fetch posts
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<?php $conn->close(); ?>
