<?php
session_start();


require_once __DIR__ . '/../../control/User.php';
require_once __DIR__ . '/../../data/db_config.php';

// Retrieve POST variables
$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';


// Create a User instance
$user = new User($db);



$result = $user->login($email, $password);


if ($result) {
    
    $_SESSION['user_id'] = $result['user_id'];
    $_SESSION['role'] = $result['role'];
    $_SESSION['first_name'] = $result['first_name'];
    $_SESSION['last_name'] = $result['last_name']; 
    $_SESSION['email'] = $result['email'];
    $_SESSION['profile_image'] = $result['profile_image'];
    $_SESSION['banner_image'] = $result['banner_image'];
    $_SESSION['bio'] = $result['bio'];
    
    header('Location: dashboard.php');
    exit();
} else {
    // Login failed
    $_SESSION['error'] = 'Invalid email or password.';
    header('Location: login.php');
    exit();
}
?>