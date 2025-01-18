<?php
session_start();
require_once '../data/db_config.php';
require_once '../control/User.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    die('Unauthorized access.');
}

$user = new User($db);
$user->setUserId($_SESSION['user_id']);

if (isset($_POST['first_name'])) {
    $user->setFirstName($_POST['first_name']);
}
if (isset($_POST['last_name'])) {
    $user->setLastName($_POST['last_name']);
}
if (isset($_POST['email'])) {
    $user->setEmail($_POST['email']);
}
if (isset($_POST['bio'])) {
    $user->setBio($_POST['bio']);
}

$uploadDir = '../up/';
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
    $fileName = basename($_FILES['profile_image']['name']);
    $filePath = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $filePath)) {
        $user->setProfileImage($filePath);
    }
}

if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == UPLOAD_ERR_OK) {
    $fileName = basename($_FILES['banner_image']['name']);
    $filePath = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $filePath)) {
        $user->setBannerImage($filePath);
    }
}

if ($user->updateProfile()) {
    echo 'Profile updated successfully.';
} else {
    echo 'Error updating profile.';
}
?>