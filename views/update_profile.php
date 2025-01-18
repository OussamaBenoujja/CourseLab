<?php
session_start();
require_once '../data/db_config.php';
require_once '../control/User.php';

// Debugging: Start session and check if user is logged in
echo "Session started. Checking user role...\n";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    die('Unauthorized access.');
}
echo "User is logged in and is a student.\n";

// Create a User object and set the user ID
$user = new User($db);
$user->setUserId($_SESSION['user_id']);
echo "User object created. User ID: " . $user->getUserId() . "\n";

// Debugging: Check if form data is received
echo "Checking form data...\n";
if (isset($_POST['first_name'])) {
    $user->setFirstName($_POST['first_name']);
    echo "First name set: " . $user->getFirstName() . "\n";
}
if (isset($_POST['last_name'])) {
    $user->setLastName($_POST['last_name']);
    echo "Last name set: " . $user->getLastName() . "\n";
}
if (isset($_POST['email'])) {
    $user->setEmail($_POST['email']);
    echo "Email set: " . $user->getEmail() . "\n";
}
if (isset($_POST['bio'])) {
    $user->setBio($_POST['bio']);
    echo "Bio set: " . $user->getBio() . "\n";
}

// Handle file uploads
$uploadDir = '../up/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true); // Create the directory if it doesn't exist
    echo "Upload directory created: $uploadDir\n";
}

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
    $fileName = basename($_FILES['profile_image']['name']);
    $filePath = $uploadDir . $fileName;
    echo "Profile image uploaded. Moving to: $filePath\n";
    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $filePath)) {
        $user->setProfileImage($filePath);
        echo "Profile image set: " . $user->getProfileImage() . "\n";
    } else {
        echo 'Error moving profile image.\n';
        exit();
    }
} else {
    echo "No profile image uploaded or upload error.\n";
}

if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == UPLOAD_ERR_OK) {
    $fileName = basename($_FILES['banner_image']['name']);
    $filePath = $uploadDir . $fileName;
    echo "Banner image uploaded. Moving to: $filePath\n";
    if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $filePath)) {
        $user->setBannerImage($filePath);
        echo "Banner image set: " . $user->getBannerImage() . "\n";
    } else {
        echo 'Error moving banner image.\n';
        exit();
    }
} else {
    echo "No banner image uploaded or upload error.\n";
}

// Debugging: Check if all data is set correctly before updating
echo "Data to be updated:\n";
echo "First Name: " . $user->getFirstName() . "\n";
echo "Last Name: " . $user->getLastName() . "\n";
echo "Email: " . $user->getEmail() . "\n";
echo "Bio: " . $user->getBio() . "\n";
echo "Profile Image: " . $user->getProfileImage() . "\n";
echo "Banner Image: " . $user->getBannerImage() . "\n";

// Update the profile in the database using the User class method
echo "Attempting to update profile...\n";
if ($user->updateProfile()) {
    echo 'Profile updated successfully.\n';
} else {
    echo 'Error updating profile.\n';
}
?>