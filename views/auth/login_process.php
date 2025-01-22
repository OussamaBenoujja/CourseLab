<?php
session_start();


require_once __DIR__ . '/../../control/User.php';
require_once __DIR__ . '/../../data/db_config.php';


$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';



$user = new User($db);



$result = $user->login($email, $password);


if ($result) {
    $user->setUserId($result['user_id']);
    $user->loadProfile();

    $_SESSION['user_id']        = $user->getUserId();
    $_SESSION['role']           = $user->getRole();
    $_SESSION['first_name']     = $user->getFirstName();
    $_SESSION['last_name']      = $user->getLastName();
    $_SESSION['email']          = $user->getEmail();
    $_SESSION['profile_image']  = $user->getProfileImage();
    $_SESSION['banner_image']   = $user->getBannerImage();
    $_SESSION['bio']            = $user->getBio();
    
    header('Location: ../home.php');
    exit();
} else {
    
    $_SESSION['error'] = 'Invalid email or password.';
    header('Location: login.php');
    exit();
}
?>