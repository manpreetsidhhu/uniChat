<?php
// Include database connection and helper functions
require_once 'db.php';
require_once 'functions.php';

// Check if the user is logged in
if(!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("Location: index.php");
    exit();
}

// Get the current user's ID and details
$user_id = $_SESSION['user_id'];
$user = get_user($user_id, $conn);
$msg = ''; // Initialize a message variable for feedback

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle profile image update
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION); // Get the file extension
        $filename = 'profile_'.$user_id.'.'.$ext; // Create a unique filename
        $path = 'uploads/'.$filename; // Set the upload path
        
        // Create the uploads directory if it doesn't exist
        if(!file_exists('uploads')) mkdir('uploads', 0777, true);
        
        // Move the uploaded file to the uploads directory
        if(move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
            // Update the user's profile image in the database
            mysqli_query($conn, "UPDATE users SET profile_image='$path' WHERE id=$user_id");
            $msg = 'Profile image updated!'; // Set a success message
            $user['profile_image'] = $path; // Update the user's profile image in the session
            $_SESSION['profile_image'] = $path;
        }
    }
    
    // Handle password update
    if(isset($_POST['current_pwd']) && isset($_POST['new_pwd'])) {
        $current = $_POST['current_pwd']; // Get the current password
        $new = $_POST['new_pwd']; // Get the new password
        
        // Verify the current password
        if(password_verify($current, $user['password'])) {
            $hashed = password_hash($new, PASSWORD_DEFAULT); // Hash the new password
            mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE id=$user_id"); // Update the password in the database
            $msg = 'Password updated!'; // Set a success message
        } else {
            $msg = 'Current password incorrect!'; // Set an error message
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - uniChat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Core animations for all pages */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); } 100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); } }
        
        .animate-fade-in { animation: fadeIn 0.5s ease-out; }
        .animate-slide-up { animation: slideUp 0.5s ease-out; }
        .hover-lift { transition: transform 0.2s ease-out; }
        .hover-lift:hover { transform: translateY(-3px); }
        
        .input-focus-effect { transition: all 0.2s ease; }
        .input-focus-effect:focus { transform: scale(1.01); }
        
        .pulse-effect:hover { animation: pulse 2s infinite; }
    </style>
</head>
<body class="bg-gray-50 font-['Plus_Jakarta_Sans']">
    <div class="max-w-3xl mx-auto my-8 bg-white rounded-xl shadow-md overflow-hidden animate-fade-in">
        <div class="flex justify-between items-center p-6 border-b border-gray-100">
            <h1 class="text-2xl font-bold text-gray-800">Profile Settings</h1>
            <a href="chat.php" class="text-indigo-600 hover:text-indigo-800 flex items-center hover-lift group">
                <span class="mr-2 group-hover:mr-3 transition-all">Back to Chat</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <?php if($msg): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mx-4 mt-4 animate-slide-up">
                <p><i class="fas fa-check-circle mr-2"></i><?php echo $msg; ?></p>
            </div>
        <?php endif; ?>
        
        <div class="p-6">
            <div class="text-center mb-8 animate-slide-up" style="animation-delay: 0.1s">
                <form method="post" enctype="multipart/form-data" id="profile-pic-form">
                    <div class="relative w-28 h-28 mx-auto pulse-effect">
                        <?php if(!empty($user['profile_image'])): ?>
                            <img src="<?php echo $user['profile_image']; ?>" class="w-full h-full rounded-full object-cover border-4 border-indigo-100 hover:border-indigo-300 transition-colors">
                        <?php else: ?>
                            <div class="w-full h-full rounded-full bg-indigo-100 flex items-center justify-center text-3xl font-bold text-indigo-600 hover:bg-indigo-200 transition-colors">
                                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <label for="profile-image" class="absolute -bottom-1 -right-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full w-9 h-9 flex items-center justify-center cursor-pointer shadow-md hover:shadow-lg transform hover:scale-110 transition-all">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" name="image" id="profile-image" accept="image/jpeg, image/png" class="hidden" onchange="document.getElementById('profile-pic-form').submit()">
                    </div>
                    <p class="mt-2 text-gray-500">@<?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <p class="text-sm text-gray-400 mt-1">Upload a new profile picture (JPG, PNG, max 2MB)</p>
                </form>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="animate-slide-up" style="animation-delay: 0.2s">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Personal Information</h2>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium text-gray-700">Full Name</label>
                        <input type="text" class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 input-focus-effect" placeholder="Enter your full name">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium text-gray-700">Username</label>
                        <input type="text" class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly>
                    </div>
                </div>
                
                <div class="animate-slide-up" style="animation-delay: 0.3s">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Change Password</h2>
                    <form method="post">
                        <div class="mb-4">
                            <label class="block mb-1 font-medium text-gray-700">Current Password</label>
                            <input type="password" name="current_pwd" class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 input-focus-effect" placeholder="Enter current password">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1 font-medium text-gray-700">New Password</label>
                            <input type="password" name="new_pwd" class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 input-focus-effect" placeholder="Enter new password">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1 font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" name="confirm_pwd" class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 input-focus-effect" placeholder="Confirm new password">
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-medium transition-transform hover:scale-105 shadow-md hover:shadow-lg transform">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="text-center py-4 text-gray-500 text-sm animate-fade-in" style="animation-delay: 0.4s">
        © 2025 uniChat App
    </footer>
</body>
</html>