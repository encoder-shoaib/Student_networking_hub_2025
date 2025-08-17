<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email, age, location, phone, university, education_duration, skills FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user exists
if (!$user) {
    echo "<script>alert('User not found. Please log in again.'); window.location.href='login.html';</script>";
    exit();
}

// Assuming you already fetched user data
$email = $user['email']; // User email fetched from database

// Generate the Gravatar URL using the MD5 hash of the user's email address
$gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=200&d=mp";

// Optional: Check if the Gravatar image exists
$headers = get_headers($gravatar_url);
if (strpos($headers[0], '200') === false) {
    // Fallback image if Gravatar doesn't exist
    $gravatar_url = "path/to/default/image.jpg"; // Fallback image
}

// Handle new post submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["post_image"])) {
    $username = $_POST['username'];
    $content = $_POST['content'];
    
    // Upload the image
    $image_name = $_FILES["post_image"]["name"];
    $image_tmp_name = $_FILES["post_image"]["tmp_name"];
    $image_folder = "uploads/" . basename($image_name);

    if (move_uploaded_file($image_tmp_name, $image_folder)) {
        // Insert post into database, including email
        $stmt = $conn->prepare("INSERT INTO posts (username, profile_picture, content, image, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $profile_picture, $content, $image_folder, $email);
        $profile_picture = "path_to_default_profile_picture"; // Default profile picture
        $stmt->execute();
        $stmt->close();
 
        // Redirect to the index page after successful post submission
        header("Location: index.php");
        exit();
    }
}

// Fetch posts from the database, only where the email matches the logged-in user
$sql = "SELECT * FROM posts WHERE email = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result(); // Get the result set

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">




    <!-- Navbar -->
    <nav class="bg-gray-800 p-4 fixed w-full z-10 sticky top-0 mb-1">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img class="w-10 mr-3" src="./src/graduation-cap.png" alt="Graduation Cap">
                <a href="#" class="text-white text-lg font-bold hidden lg:block">Student Networking Hub</a>
            </div>
            <div class="hidden md:flex">
                <a href="./index.php" class="text-gray-300 hover:text-white px-4">Home</a>
                <a href="#" class="text-gray-300 hover:text-white px-4">About</a>
                <a href="#" class="text-gray-300 hover:text-white px-4">Services</a>
                <a href="#" class="text-gray-300 hover:text-white px-4">Contact</a>
            </div>
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
                <div class="text-white flex justify-between items-center">
                    <a href="./login.html" class="hover:underline" class="ps-4">Log Out</a>
                </div>
            </div>
            <div class="md:hidden">
                <button class="text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    
    <!-- Main Container -->
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden mt-10">
        <!-- Banner Section -->
        <div class="relative">
            <img src="./src/profile-banner.webp" alt="Banner Image" class="w-full h-48 object-cover">
            <div class="absolute -bottom-14 left-6 z-10">
                <!-- Display Gravatar profile picture -->
                <img src="<?php echo $gravatar_url; ?>" alt="Profile Picture" class="w-28 h-28 rounded-full border-4 border-white">
            </div>
        </div>

        <!-- Profile Details -->
        <div class="pt-16 px-6 pb-6">
            <h2 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($user['username']); ?> 221-15-4955</h2>
            <p class="text-gray-600 mt-2"><strong>Location:</strong> <?php echo htmlspecialchars($user['location']); ?></p>
            <p class="text-gray-600"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p class="text-gray-600"><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
        </div>

        <!-- Education Section -->
        <div class="border-t px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-700">Education</h3>
            <p class="text-gray-600 mt-2"><strong>University:</strong> <?php echo htmlspecialchars($user['university']); ?></p>
            <p class="text-gray-600"><strong>Duration:</strong> <?php echo htmlspecialchars($user['education_duration']); ?></p>
        </div>

        <!-- Skills Section -->
        <div class="border-t px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-700">Skills</h3>
            <p class="text-gray-600 mt-2"><strong>Skills List:</strong> <?php echo htmlspecialchars($user['skills']); ?></p>
        </div>

    </div>

<section class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden mt-10">  
    <!-- Display Posts -->
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card bg-white shadow-xl flex flex-col p-6 mb-6 rounded-lg lg:w-[800px]">
            <div class="flex items-center mb-4">
                <img src="<?php echo $gravatar_url; ?>" alt="Profile Picture" class="w-14 h-14 rounded-full border-4 border-white">
                <div>
                    <h2 class="font-semibold text-lg"><?php echo $row['username']; ?></h2>
                    <p class="text-sm text-gray-500"><?php echo $row['created_at']; ?></p>
                </div>
            </div>
            <div class="card-body flex-1 py-5">
                <p class="text-gray-700"><?php echo $row['content']; ?></p>
            </div>
            <figure>
                <img src="<?php echo $row['image']; ?>" alt="Post Image" class="w-full h-auto rounded-xl object-cover mt-4 shadow-md">
            </figure>
            <div class="flex items-center justify-between mt-6">
                <div class="flex items-center space-x-4">
                    <button class="flex items-center space-x-2 text-gray-500 hover:text-blue-500 transition duration-150">
                        <i class="fa-regular fa-heart"></i>
                        <span>24</span>
                    </button>
                    <button class="flex items-center space-x-2 text-gray-500 hover:text-blue-500 transition duration-150">
                        <i class="fa-regular fa-comment"></i>
                        <span>16</span>
                    </button>
                </div>
                <div>
                    <button class="flex items-center space-x-2 text-gray-500 hover:text-blue-500 transition duration-150">
                        <i class="fa-solid fa-share"></i>
                        <span>20</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</section>

</body>
</html>
