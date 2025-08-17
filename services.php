<?php
// services.php
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
    <title>Services - Student Networking Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/8bcfb398b0.js" crossorigin="anonymous"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .service-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border-left-color: #667eea;
        }
        .icon-container {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="gradient-bg p-4 fixed w-full z-10 sticky top-0">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img class="w-10 mr-3" src="./src/graduation-cap.png" alt="Graduation Cap">
                <a href="#" class="text-white text-lg font-bold hidden lg:block">Student Networking Hub</a>
            </div>
            <div class="hidden md:flex">
                <a href="./index.php" class="text-white hover:text-gray-200 px-4 transition duration-300">Home</a>
                <a href="./about.php" class="text-white hover:text-gray-200 px-4 transition duration-300">About</a>
                <a href="./services.php" class="text-white font-semibold px-4 transition duration-300">Services</a>
                <a href="./contact.php" class="text-white hover:text-gray-200 px-4 transition duration-300">Contact</a>
            </div>
            <div class="search-container">
                <div class="relative md:ml-4 flex gap-4">
                    <input id="searchInput" type="text" class="bg-white bg-opacity-90 text-gray-900 px-4 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search">
                    <div>
                        <button id="searchButton" class="bg-white bg-opacity-90 hover:bg-opacity-100 text-blue-600 font-bold py-2 px-4 rounded-full transition duration-300">Search</button>
                    </div>
                </div>
                <div id="results" class="results"></div>
            </div>
            <div class="flex items-center">
                <img src="<?php echo $gravatar_url; ?>" alt="Profile" class="w-8 h-8 rounded-full mr-2 border-2 border-white">
                <a href="./logout.php" class="text-white hover:text-gray-200 transition duration-300">Log Out</a>
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

    <!-- Main Content -->
    <main class="flex-grow pt-24 pb-12">
        <div class="container mx-auto px-4">
            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Our Comprehensive Services</h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Empowering your academic journey through innovative networking solutions designed for student success.
                </p>
            </div>

            <!-- Services Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="service-card bg-white p-8 rounded-lg shadow-md">
                    <div class="icon-container bg-blue-100">
                        <i class="fas fa-user-graduate text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Profile Creation</h3>
                    <p class="text-gray-600 mb-4">
                        Build a professional profile showcasing your academic achievements, skills, and interests. Stand out to peers and potential collaborators.
                    </p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Highlight your academic journey</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Showcase projects and skills</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Customize your public presence</span>
                        </li>
                    </ul>
                </div>

                <!-- Service 2 -->
                <div class="service-card bg-white p-8 rounded-lg shadow-md">
                    <div class="icon-container bg-purple-100">
                        <i class="fas fa-users text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Social Networking</h3>
                    <p class="text-gray-600 mb-4">
                        Connect with peers, join academic groups, and build your professional network within the student community.
                    </p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Find classmates and study partners</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Join subject-specific groups</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Engage in academic discussions</span>
                        </li>
                    </ul>
                </div>

                <!-- Service 3 -->
                <div class="service-card bg-white p-8 rounded-lg shadow-md">
                    <div class="icon-container bg-green-100">
                        <i class="fas fa-share-alt text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Content Sharing</h3>
                    <p class="text-gray-600 mb-4">
                        Share your academic work, ideas, and achievements with the community. Get feedback and inspire others.
                    </p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Post projects and research</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Share academic resources</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Receive constructive feedback</span>
                        </li>
                    </ul>
                </div>

                <!-- Service 4 -->
                <div class="service-card bg-white p-8 rounded-lg shadow-md">
                    <div class="icon-container bg-yellow-100">
                        <i class="fas fa-search text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Search & Discover</h3>
                    <p class="text-gray-600 mb-4">
                        Powerful search tools to find peers, mentors, or collaborators based on skills, courses, or interests.
                    </p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Filter by academic interests</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Find project collaborators</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Connect with alumni mentors</span>
                        </li>
                    </ul>
                </div>

                <!-- Service 5 -->
                <div class="service-card bg-white p-8 rounded-lg shadow-md">
                    <div class="icon-container bg-red-100">
                        <i class="fas fa-calendar-check text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Event Management</h3>
                    <p class="text-gray-600 mb-4">
                        Discover and organize academic events, workshops, and networking opportunities.
                    </p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Find study groups and workshops</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Organize academic meetups</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>RSVP to campus events</span>
                        </li>
                    </ul>
                </div>

                <!-- Service 6 -->
                <div class="service-card bg-white p-8 rounded-lg shadow-md">
                    <div class="icon-container bg-indigo-100">
                        <i class="fas fa-briefcase text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Career Support</h3>
                    <p class="text-gray-600 mb-4">
                        Access internship opportunities, job postings, and career development resources.
                    </p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Explore internship opportunities</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Connect with industry professionals</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Access career resources</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="gradient-bg rounded-xl p-8 md:p-12 mt-16 text-center text-white">
                <h2 class="text-2xl md:text-3xl font-bold mb-4">Ready to Enhance Your Student Experience?</h2>
                <p class="text-lg mb-6 max-w-2xl mx-auto">
                    Join thousands of students who are already networking, collaborating, and achieving more together.
                </p>
                <a href="./index.php" class="inline-block bg-white text-blue-600 font-semibold py-3 px-8 rounded-lg hover:bg-gray-100 transition duration-300">
                    Get Started Now
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="gradient-bg text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <div class="flex items-center">
                        <img class="w-10 mr-3" src="./src/graduation-cap.png" alt="Graduation Cap">
                        <span class="text-xl font-bold">Student Networking Hub</span>
                    </div>
                    <p class="mt-2 text-gray-300">Connecting students for a brighter future.</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="./index.php" class="text-gray-300 hover:text-white transition duration-300">Home</a></li>
                            <li><a href="./about.php" class="text-gray-300 hover:text-white transition duration-300">About</a></li>
                            <li><a href="./services.php" class="text-gray-300 hover:text-white transition duration-300">Services</a></li>
                            <li><a href="./contact.php" class="text-gray-300 hover:text-white transition duration-300">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Resources</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-300 hover:text-white transition duration-300">Blog</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition duration-300">Help Center</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition duration-300">Tutorials</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition duration-300">FAQs</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Connect</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-300 hover:text-white transition duration-300"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-gray-300 hover:text-white transition duration-300"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-gray-300 hover:text-white transition duration-300"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-gray-300 hover:text-white transition duration-300"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Student Networking Hub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <section id="search-hide" class="relative z-10 flex-col mx-20 justify-center items-center hidden">
        <div id="results" class="results bg-white p-20 border rounded-xl"></div>
        <button type="button" id="search-cancel" class="mt-3 inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
    </section>

    <script src="js/home.js"></script>
</body>
</html>