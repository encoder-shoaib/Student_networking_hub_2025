<?php
// about.php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: login.html');
    exit();
}

$email = $user['email'];
$gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=200&d=mp";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Student Networking Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/8bcfb398b0.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-gray-800 p-4 fixed w-full z-10 sticky top-0">
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
            <div class="search-container">
                <div class="relative md:ml-4 flex gap-4">
                    <input id="searchInput" type="text" class="bg-white text-gray-900 px-4 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search">
                    <div>
                        <button id="searchButton" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full">Search</button>
                    </div>
                </div>
                <div id="results" class="results"></div>
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

    <!-- About Section -->
    <section class="flex-grow flex items-center justify-center py-12">
        <div class="bg-white p-10 rounded-lg shadow-lg w-full max-w-4xl">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">About Student Networking Hub</h2>
            <div class="text-gray-600 space-y-6">
                <p>
                    Welcome to the <strong>Student Networking Hub</strong>, a platform dedicated to connecting students worldwide. Our mission is to create a vibrant community where students can share knowledge, collaborate on projects, and build professional networks that empower their future careers.
                </p>
                <p>
                    Launched in 2025, the Student Networking Hub was created to bridge the gap between students from diverse backgrounds and institutions. Whether you're a computer science major looking to collaborate on a coding project or a literature student seeking peer feedback, our platform provides the tools to connect, engage, and grow.
                </p>
                <h3 class="text-xl font-semibold text-gray-700">Our Vision</h3>
                <p>
                    We aim to foster a global student community that thrives on collaboration, innovation, and mutual support. By providing a space to showcase skills, share ideas, and form meaningful connections, we empower students to take charge of their academic and professional journeys.
                </p>
                <h3 class="text-xl font-semibold text-gray-700">Why Choose Us?</h3>
                <ul class="list-disc list-inside space-y-2">
                    <li>Connect with students from various universities and disciplines.</li>
                    <li>Share your projects, ideas, and achievements through posts.</li>
                    <li>Engage with peers through likes, comments, and friend requests.</li>
                    <li>Access a platform designed for collaboration and professional growth.</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto text-center">
            <p>&copy; 2025 Student Networking Hub. All rights reserved.</p>
        </div>
    </footer>

    <section id="search-hide" class="relative z-10 flex-col mx-20 justify-center items-center hidden">
        <div id="results" class="results bg-white p-20 border rounded-xl"></div>
        <button type="button" id="search-cancel" class="mt-3 inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
    </section>

    <script src="js/home.js"></script>
</body>
</html>