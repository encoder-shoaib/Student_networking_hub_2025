<?php
// contact.php
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

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($message)) {
        $error = "All fields are required.";
    } else {
        // In a real application, save to database or send email
        $success = "Your message has been sent successfully! We'll get back to you within 24 hours.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Student Networking Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/8bcfb398b0.js" crossorigin="anonymous"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .contact-card {
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.25);
        }
        .input-focus:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
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
                <a href="./services.php" class="text-white hover:text-gray-200 px-4 transition duration-300">Services</a>
                <a href="./contact.php" class="text-white font-semibold px-4 transition duration-300">Contact</a>
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
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Get In Touch</h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    We're here to help and answer any questions you might have. We look forward to hearing from you!
                </p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 justify-center items-start">
                <!-- Contact Form -->
                <div class="contact-card bg-white p-8 rounded-xl w-full lg:w-1/2">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Send Us a Message</h2>

                    <?php if(isset($error)): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            <p><?php echo $error; ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($success)): ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p><?php echo $success; ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="contact.php" method="POST" class="space-y-6">
                        <div>
                            <label for="name" class="block text-gray-700 font-medium mb-2">Your Name</label>
                            <input type="text" id="name" name="name" class="w-full p-4 border border-gray-300 rounded-lg input-focus focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="John Doe" required>
                        </div>
                        <div>
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                            <input type="email" id="email" name="email" class="w-full p-4 border border-gray-300 rounded-lg input-focus focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="john@example.com" required>
                        </div>
                        <div>
                            <label for="message" class="block text-gray-700 font-medium mb-2">Your Message</label>
                            <textarea id="message" name="message" class="w-full p-4 border border-gray-300 rounded-lg input-focus focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="How can we help you?" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="w-full gradient-bg hover:opacity-90 text-white font-semibold py-4 px-6 rounded-lg transition duration-300 transform hover:scale-105">
                            Send Message <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="contact-card bg-white p-8 rounded-xl w-full lg:w-1/2">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Contact Information</h2>
                    <p class="text-gray-600 mb-8">
                        Feel free to reach out to us through any of these channels. Our team is always ready to assist you with your inquiries.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-3 rounded-full mr-4">
                                <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Our Location</h3>
                                <p class="text-gray-600">123 University Avenue<br>Campus Town, CT 12345</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-purple-100 p-3 rounded-full mr-4">
                                <i class="fas fa-envelope text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Email Us</h3>
                                <p class="text-gray-600">support@studenthub.edu<br>info@studenthub.edu</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <i class="fas fa-phone-alt text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Call Us</h3>
                                <p class="text-gray-600">+1 (555) 123-4567<br>Mon-Fri, 9am-5pm EST</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10">
                        <h3 class="font-semibold text-gray-800 mb-4">Connect With Us</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition duration-300">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="bg-pink-600 text-white p-3 rounded-full hover:bg-pink-700 transition duration-300">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="bg-blue-400 text-white p-3 rounded-full hover:bg-blue-500 transition duration-300">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="bg-gray-800 text-white p-3 rounded-full hover:bg-gray-900 transition duration-300">
                                <i class="fab fa-github"></i>
                            </a>
                            <a href="#" class="bg-red-600 text-white p-3 rounded-full hover:bg-red-700 transition duration-300">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>
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
                        <h3 class="text-lg font-semibold mb-4">Legal</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-300 hover:text-white transition duration-300">Privacy Policy</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition duration-300">Terms of Service</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition duration-300">Cookie Policy</a></li>
                        </ul>
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