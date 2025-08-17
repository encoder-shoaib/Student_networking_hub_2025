<?php
// profile.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include('db.php');

$profile_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];
$own_profile = ($profile_id == $_SESSION['user_id']);

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<script>alert('User not found.'); window.location.href='index.php';</script>";
    exit();
}

$email = $user['email'];
$gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=200&d=mp";

// Fetch posts for this user
$sql = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$post_result = $stmt->get_result();

// Check friend status if not own profile
$friend_status = null;
if (!$own_profile) {
    $check_stmt = $conn->prepare("SELECT status FROM friend_requests WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)");
    $check_stmt->bind_param("iiii", $_SESSION['user_id'], $profile_id, $profile_id, $_SESSION['user_id']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_row = $check_result->fetch_assoc()) {
        $friend_status = $check_row['status'];
    }
}

// Fetch friend requests if own profile
$requests = [];
if ($own_profile) {
    $req_sql = "SELECT fr.*, u.username FROM friend_requests fr JOIN users u ON fr.sender_id = u.id WHERE receiver_id = ? AND status = 'pending'";
    $req_stmt = $conn->prepare($req_sql);
    $req_stmt->bind_param("i", $profile_id);
    $req_stmt->execute();
    $requests = $req_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/8bcfb398b0.js" crossorigin="anonymous"></script>
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

    <!-- Main Container -->
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden mt-10">
        <!-- Banner Section -->
        <div class="relative">
            <img src="./src/profile-banner.webp" alt="Banner Image" class="w-full h-48 object-cover">
            <div class="absolute -bottom-14 left-6 z-10">
                <img src="<?php echo $gravatar_url; ?>" alt="Profile Picture" class="w-28 h-28 rounded-full border-4 border-white">
            </div>
        </div>

        <!-- Profile Details -->
        <div class="pt-16 px-6 pb-6">
            <h2 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($user['username']); ?></h2>
            <p class="text-gray-600 mt-2"><strong>Location:</strong> <?php echo htmlspecialchars($user['location']); ?></p>
            <p class="text-gray-600"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p class="text-gray-600"><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            <?php if (!$own_profile): ?>
                <?php if (!$friend_status): ?>
                    <form action="send_friend_request.php" method="POST">
                        <input type="hidden" name="receiver_id" value="<?php echo $profile_id; ?>">
                        <button type="submit" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded">Send Friend Request</button>
                    </form>
                <?php elseif ($friend_status == 'pending'): ?>
                    <p class="text-gray-600">Friend Request Pending</p>
                <?php elseif ($friend_status == 'accepted'): ?>
                    <p class="text-green-600">Friends</p>
                <?php endif; ?>
            <?php endif; ?>
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

        <?php if ($own_profile): ?>
            <!-- Friend Requests Section -->
            <div class="border-t px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-700">Friend Requests</h3>
                <?php if (empty($requests)): ?>
                    <p class="text-gray-600">No pending requests.</p>
                <?php else: ?>
                    <?php foreach ($requests as $req): ?>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-gray-600"><?php echo htmlspecialchars($req['username']); ?></p>
                            <div>
                                <form action="accept_friend.php" method="POST" class="inline">
                                    <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                    <button type="submit" class="bg-green-500 text-white py-1 px-2 rounded">Accept</button>
                                </form>
                                <form action="reject_friend.php" method="POST" class="inline">
                                    <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                    <button type="submit" class="bg-red-500 text-white py-1 px-2 rounded">Reject</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <section class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden mt-10">
        <!-- Display Posts -->
        <?php while ($row = $post_result->fetch_assoc()): ?>
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
    </section>

    <script src="js/home.js"></script>
</body>
</html>
<?php $conn->close(); ?>