<?php
// index.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_networking_hub";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header('Location: login.html');
    exit();
}

$email = $user['email'];
$gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=200&d=mp";

// Handle new post submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["post_image"])) {
    $username = $_POST['username'];
    $content = $_POST['content'];

    // Validate file upload
    if ($_FILES["post_image"]["error"] !== UPLOAD_ERR_OK) {
        die("File upload error: " . $_FILES["post_image"]["error"]);
    }

    // Check file type and size
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $_FILES["post_image"]["tmp_name"]);
    finfo_close($file_info);

    if (!in_array($mime_type, $allowed_types)) {
        die("Only JPEG, PNG, and GIF images are allowed.");
    }

    if ($_FILES["post_image"]["size"] > 5 * 1024 * 1024) { // 5MB limit
        die("File is too large. Maximum size is 5MB.");
    }

    // Create uploads directory if it doesn't exist
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Generate unique filename
    $image_ext = pathinfo($_FILES["post_image"]["name"], PATHINFO_EXTENSION);
    $image_name = uniqid() . '.' . $image_ext;
    $image_folder = "uploads/" . $image_name;

    if (move_uploaded_file($_FILES["post_image"]["tmp_name"], $image_folder)) {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, username, profile_picture, content, image) VALUES (?, ?, ?, ?, ?)");

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $bind_result = $stmt->bind_param("issss", $user_id, $username, $gravatar_url, $content, $image_folder);

        if ($bind_result === false) {
            die("Bind failed: " . $stmt->error);
        }

        $execute_result = $stmt->execute();

        if ($execute_result === false) {
            die("Execute failed: " . $stmt->error);
        }

        $stmt->close();

        header("Location: index.php");
        exit();
    } else {
        die("Failed to move uploaded file.");
    }
}

// Fetch all posts
$sql = "SELECT p.*, u.email FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/8bcfb398b0.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 h-screen">
    <!-- Navbar -->
    <nav class="bg-gray-800 p-4 fixed w-full z-10 sticky top-0 mb-1">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img class="w-10 mr-3" src="./src/graduation-cap.png" alt="Graduation Cap">
                <a href="#" class="text-white text-lg font-bold hidden lg:block">Student Networking Hub</a>
            </div>
            <div class="hidden md:flex">
                <a href="./index.php" class="text-gray-300 hover:text-white px-4">Home</a>
                <a href="./about.php" class="text-gray-300 hover:text-white px-4">About</a>
                <a href="./services.php" class="text-gray-300 hover:text-white px-4">Services</a>
                <a href="./contact.php" class="text-gray-300 hover:text-white px-4">Contact</a>
            </div>
            <!-- Search Container -->
            <div class="search-container">
                <div class="relative md:ml-4 flex gap-4">
                    <input id="searchInput" type="text" class="bg-white text-gray-900 px-4 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search">
                    <div>
                        <button id="searchButton" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full">Search</button>
                    </div>
                </div>
            </div>
            <div class="text-white flex justify-between items-center">
                <a href="./logout.php" class="hover:underline ps-4">Log Out</a>
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

    <!-- Header -->
    <section class="lg:px-16 px-5 lg:px-36 lg:flex justify-center pt-18 mb-28">
        <div class="flex gap-8 items-start justify-center">
            <!-- Left Sidebar - Profile Card -->
            <div class="hidden lg:block">
                <div class="card w-[270px] bg-white shadow-xl">
                    <div class="relative h-40 overflow-hidden" id="profile-page">
                        <img class="object-cover w-full h-full brightness-50" src="./src/profile-banner.webp" alt="Profile Banner">
                        <div class="absolute bottom-0 left-0 w-full flex justify-center">
                            <img src="<?php echo $gravatar_url; ?>" alt="Profile Picture" class="w-28 h-28 rounded-full border-4 border-white">
                        </div>
                    </div>
                    <div class="profile-desc text-center pb-12 pt-5">
                        <h2 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($user['username']); ?></h2>
                        <p class="text-gray-600 mt-2 p-3">Any one can join with us if you want. Connect with us on social media!</p>
                    </div>
                </div>

                <!-- Pages You May Like Section -->
                <div class="card p-6 my-8 bg-white shadow-xl">
                    <h4 class="text-xl font-bold pb-4">Page you may like</h4>
                    <div>
                        <ul>
                            <div class="flex items-center justify-between mb-4">
                                <div class="block">
                                    <a href="#">
                                        <figure>
                                            <img src="./src/profile-1.webp" alt="profile picture" class="rounded-full w-9 h-9">
                                        </figure>
                                    </a>
                                </div>

                                <div class="ml-4 block">
                                    <h3><a href="#" class="text-blue-500">Travel The World</a></h3>
                                    <p><a href="#" class="text-gray-500">adventure</a></p>
                                </div>

                                <div class="block">
                                    <button class="relative w-6 h-6">
                                        <img src="./src/heart-color.webp" alt="" class="w-full h-full absolute inset-0">
                                        <img src="./src/heart.webp" alt="" class="w-full h-full absolute inset-0 opacity-0 hover:opacity-100">
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mb-4">
                                <div class="block">
                                    <a href="#">
                                        <figure>
                                            <img src="./src/profile-35x35-4.webp" alt="profile picture" class="rounded-full w-9 h-9">
                                        </figure>
                                    </a>
                                </div>

                                <div class="ml-4 block">
                                    <h3><a href="#" class="text-blue-500">Travel The World</a></h3>
                                    <p><a href="#" class="text-gray-500">adventure</a></p>
                                </div>

                                <div class="block">
                                    <button class="relative w-6 h-6">
                                        <img src="./src/heart-color.webp" alt="" class="w-full h-full absolute inset-0">
                                        <img src="./src/heart.webp" alt="" class="w-full h-full absolute inset-0 opacity-0 hover:opacity-100">
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mb-4">
                                <div class="block">
                                    <a href="#">
                                        <figure>
                                            <img src="./src/profile-35x35-7 (1).webp" alt="profile picture" class="rounded-full w-9 h-9">
                                        </figure>
                                    </a>
                                </div>

                                <div class="ml-4 block">
                                    <h3><a href="#" class="text-blue-500">Travel The World</a></h3>
                                    <p><a href="#" class="text-gray-500">adventure</a></p>
                                </div>

                                <div class="block">
                                    <button class="relative w-6 h-6">
                                        <img src="./src/heart-color.webp" alt="" class="w-full h-full absolute inset-0">
                                        <img src="./src/heart.webp" alt="" class="w-full h-full absolute inset-0 opacity-0 hover:opacity-100">
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mb-4">
                                <div class="block">
                                    <a href="#">
                                        <figure>
                                            <img src="./src/profile-35x35-9.webp" alt="profile picture" class="rounded-full w-9 h-9">
                                        </figure>
                                    </a>
                                </div>

                                <div class="ml-4 block">
                                    <h3><a href="#" class="text-blue-500">Travel The World</a></h3>
                                    <p><a href="#" class="text-gray-500">adventure</a></p>
                                </div>

                                <div class="block">
                                    <button class="relative w-6 h-6">
                                        <img src="./src/heart-color.webp" alt="" class="w-full h-full absolute inset-0">
                                        <img src="./src/heart.webp" alt="" class="w-full h-full absolute inset-0 opacity-0 hover:opacity-100">
                                    </button>
                                </div>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Middle Section - News Feed -->
            <div>
                <!-- Create Post Box -->
                <div class="flex items-center justify-center p-4 mb-8 bg-white">
                    <img src="<?php echo $gravatar_url; ?>" alt="Profile Picture" class="w-14 h-14 rounded-full border-4 border-white">
                    <div class="flex flex-grow">
                        <input type="text" class="bg-gray-200 text-gray-900 rounded-full px-4 py-1 me-3 flex-grow" placeholder="Share your thoughts...">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full" id='button-post'>Post</button>
                    </div>
                </div>

                <!-- Search Results Section -->
                <section id="search-hide" class="relative z-10 flex-col mx-20 justify-center items-center hidden">
                    <div id="results" class="results bg-white p-20 border rounded-xl">
                        <!-- Search results will appear here -->
                    </div>
                    <button type="button" id='search-cancel' class="mt-3 inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
                </section>

                <!-- Display Posts -->
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card bg-white shadow-xl flex flex-col p-6 mb-6 rounded-lg lg:w-[800px]">
                        <div class="flex items-center mb-4">
                            <img src="<?php echo $gravatar_url; ?>" alt="Profile Picture" class="w-14 h-14 rounded-full border-4 border-white">
                            <div>
                                <h2 class="font-semibold text-lg"><?php echo htmlspecialchars($row['username']); ?></h2>
                                <p class="text-sm text-gray-500"><?php echo $row['created_at']; ?></p>
                            </div>
                        </div>
                        <div class="card-body flex-1 py-5">
                            <p class="text-gray-700"><?php echo htmlspecialchars($row['content']); ?></p>
                        </div>
                        <figure>
                            <img src="<?php echo $row['image']; ?>" alt="Post Image" class="w-full h-auto rounded-xl object-cover mt-4 shadow-md">
                        </figure>
                        <!-- Like and Comment Buttons -->
                        <div class="flex items-center justify-between mt-6">
                            <div class="flex items-center space-x-4">
                                <button class="like-button flex items-center space-x-2 text-gray-500 hover:text-blue-500 transition duration-150" data-post-id="<?php echo $row['id']; ?>">
                                    <i class="fa-regular fa-heart"></i>
                                    <span><?php echo $row['likes']; ?></span>
                                </button>
                                <button class="comment-button flex items-center space-x-2 text-gray-500 hover:text-blue-500 transition duration-150" data-post-id="<?php echo $row['id']; ?>">
                                    <i class="fa-regular fa-comment"></i>
                                    <span>
                                        <?php
                                        $comment_count_query = "SELECT COUNT(*) as comment_count FROM comments WHERE post_id = ?";
                                        $stmt = $conn->prepare($comment_count_query);
                                        $stmt->bind_param("i", $row['id']);
                                        $stmt->execute();
                                        $comment_count_result = $stmt->get_result()->fetch_assoc();
                                        echo $comment_count_result['comment_count'];
                                        ?>
                                    </span>
                                </button>
                            </div>
                            <div>
                                <button class="flex items-center space-x-2 text-gray-500 hover:text-blue-500 transition duration-150">
                                    <i class="fa-solid fa-share"></i>
                                    <span>20</span>
                                </button>
                            </div>
                        </div>
                        <!-- Comment Section -->
                        <div class="comment-section mt-4" data-post-id="<?php echo $row['id']; ?>">
                            <div class="comments-list">
                                <?php
                                $comment_query = "SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE post_id = ? ORDER BY c.created_at DESC";
                                $stmt = $conn->prepare($comment_query);
                                $stmt->bind_param("i", $row['id']);
                                $stmt->execute();
                                $comments = $stmt->get_result();
                                while ($comment = $comments->fetch_assoc()): ?>
                                    <div class="comment flex items-start mb-2">
                                        <img src="<?php echo $gravatar_url; ?>" alt="Profile Picture" class="w-8 h-8 rounded-full mr-2">
                                        <div>
                                            <p class="font-semibold"><?php echo htmlspecialchars($comment['username']); ?></p>
                                            <p class="text-gray-600"><?php echo htmlspecialchars($comment['comment']); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo $comment['created_at']; ?></p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <form class="comment-form mt-4" method="POST">
                                <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                                <textarea name="comment" placeholder="Write a comment..." class="w-full p-2 border border-gray-300 rounded-lg" required></textarea>
                                <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full">Comment</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Right Sidebar - Additional Content -->
            <div class="hidden lg:block w-[270px]">
                <!-- Recent Notifications -->
                <div class="card w-[270px] bg-white shadow-xl flex flex-col">
                    <h1 class="text-xl p-3 ps-5 font-bold">Recent Notifications</h1>
                    <div class="px-2">
                        <div class="flex items-center p-4">
                            <img src="./src/profile-35x35-9.webp" alt="Profile Picture" class="w-10 h-10 rounded-full mr-4">
                            <div>
                                <h2 class="font-semibold">Any one can join with us if you want</h2>
                                <p class="text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4">
                            <img src="./src/profile-35x35-8.webp" alt="Profile Picture" class="w-10 h-10 rounded-full mr-4">
                            <div>
                                <h2 class="font-semibold">Any one can join with us if you want</h2>
                                <p class="text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4">
                            <img src="./src/profile-35x35-7.webp" alt="Profile Picture" class="w-10 h-10 rounded-full mr-4">
                            <div>
                                <h2 class="font-semibold">Any one can join with us if you want</h2>
                                <p class="text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4">
                            <img src="./src/profile-35x35-6.webp" alt="Profile Picture" class="w-10 h-10 rounded-full mr-4">
                            <div>
                                <h2 class="font-semibold">Any one can join with us if you want</h2>
                                <p class="text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4">
                            <img src="./src/profile-35x35-4.webp" alt="Profile Picture" class="w-10 h-10 rounded-full mr-4">
                            <div>
                                <h2 class="font-semibold">Any one can join with us if you want</h2>
                                <p class="text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advertisement Section -->
                <div class="bg-white w-[270px] shadow-lg p-6 my-8">
                    <h4 class="text-lg font-bold mb-4">Advertisement</h4>
                    <div class="mt-4">
                        <a href="#" class="block">
                            <img src="./src/add-2.jpg" alt="advertisement" class="w-full h-auto rounded">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Post Modal Section -->
    <section id='post-section' class="fixed top-0 left-0 w-full bg-gray-100 text-gray-900 font-sans leading-relaxed hidden z-50">
        <div class="container mx-auto p-6">
            <div class="bg-white shadow-lg rounded-lg p-8 mb-8">
                <form action="index.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <input type="text" name="username" placeholder="Your Name" value="<?php echo htmlspecialchars($user['username']); ?>" required class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <textarea name="content" placeholder="What's on your mind?" required class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="mb-4">
                        <input type="file" name="post_image" accept="image/*" required class="w-full text-gray-500 file:border-gray-300 file:bg-gray-100 file:p-3 file:rounded-lg">
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-500 text-white font-semibold rounded-lg shadow-lg hover:bg-blue-600 transition duration-200">Post</button>
                </form>
            </div>
        </div>
    </section>

    <script src="js/home.js"></script>
</body>
</html>
<?php $conn->close(); ?>