<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database connection and helper functions
require_once 'db.php';
require_once 'functions.php';

// Initialize error message variable
$error = '';

// Handle user registration
if (isset($_POST['register'])) {
    // Sanitize and retrieve form inputs
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Check if fields are empty
    if (empty($username) || empty($password)) {
        $error = "Please fill all fields";
    } else {
        // Attempt to register the user
        if (register_user($username, $password, $conn)) {
            $success = "Registration successful! Please login.";
        } else {
            $error = "Username already exists";
        }
    }
}

// Handle user login
if (isset($_POST['login'])) {
    // Sanitize and retrieve form inputs
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Check if fields are empty
    if (empty($username) || empty($password)) {
        $error = "Please fill all fields";
    } else {
        // Attempt to log in the user
        if (login_user($username, $password, $conn)) {
            // Redirect to chat page on successful login
            header("Location: chat.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
}

// Redirect to chat page if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: chat.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>uniChat - Messaging Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Plus Jakarta Sans',sans-serif}
        
        /* Core animations for all pages */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .animate-fade-in { animation: fadeIn 0.5s ease-out; }
        .animate-slide-up { animation: slideUp 0.5s ease-out; }
        
        /* Login-specific animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .float-icon { animation: float 3s ease-in-out infinite; }
        
        .input-focus-effect { transition: all 0.2s ease; }
        .input-focus-effect:focus { transform: scale(1.01); box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2); }
        
        .btn-hover-effect { transition: all 0.3s ease; }
        .btn-hover-effect:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); }
    </style>
</head>
<body class="bg-gradient-to-r from-indigo-100 to-purple-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden animate-fade-in">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-5 text-center">
            <div class="flex items-center justify-center mb-2">
                <i class="fas fa-bolt text-3xl text-white mr-2 float-icon"></i>
                <h1 class="text-2xl font-bold text-white">uniChat</h1>
            </div>
            <p class="text-indigo-100 text-sm">Modern messaging platform</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mx-4 mt-3">
                <p><i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mx-4 mt-3">
                <p><i class="fas fa-check-circle mr-2"></i><?php echo $success; ?></p>
            </div>
        <?php endif; ?>
        
        <div class="p-0">
            <ul class="flex text-center border-b">
                <li class="flex-1"><a href="#login" id="loginTab" class="block py-3 font-medium">Sign In</a></li>
                <li class="flex-1"><a href="#register" id="registerTab" class="block py-3 font-medium text-gray-500">Register</a></li>
            </ul>
            
            <div class="p-6">
                <div id="loginForm">
                    <form method="post" class="space-y-4">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500"><i class="fas fa-user"></i></span>
                            <input type="text" name="username" class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-indigo-300" placeholder="Username">
                        </div>
                        
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-indigo-300" placeholder="Password">
                        </div>
                        
                        <button type="submit" name="login" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg font-medium flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </button>
                    </form>
                </div>
                
                <div id="registerForm" class="hidden">
                    <form method="post" class="space-y-4">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500"><i class="fas fa-user"></i></span>
                            <input type="text" name="username" class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-indigo-300" placeholder="Choose a username">
                        </div>
                        
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500"><i class="fas fa-lock"></i></span>
                            <input required type="password" name="password" class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-indigo-300" placeholder="Create a password">
                        </div>
                        
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500"><i class="fas fa-check-circle"></i></span>
                            <input required type="password" name="confirm_password" class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-indigo-300" placeholder="Confirm password">
                        </div>
                        
                        <button type="submit" name="register" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg font-medium flex items-center justify-center">
                            <i class="fas fa-user-plus mr-2"></i>Create Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const tabs = {login: document.getElementById('loginTab'), register: document.getElementById('registerTab')};
        const forms = {login: document.getElementById('loginForm'), register: document.getElementById('registerForm')};
        
        for(let type in tabs) {
            tabs[type].addEventListener('click', e => {
                e.preventDefault();
                forms.login.classList.toggle('hidden', type !== 'login');
                forms.register.classList.toggle('hidden', type !== 'register');
                
                tabs.login.className = type === 'login' ? 'block py-3 font-medium text-indigo-600 border-b-2 border-indigo-600' : 'block py-3 font-medium text-gray-500';
                tabs.register.className = type === 'register' ? 'block py-3 font-medium text-indigo-600 border-b-2 border-indigo-600' : 'block py-3 font-medium text-gray-500';
            });
        }
        
        tabs.login.className = 'block py-3 font-medium text-indigo-600 border-b-2 border-indigo-600';
    </script>
</body>
</html>