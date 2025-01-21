<?php
session_start();
require_once '../../control/User.php';
require_once '../../control/Student.php';
require_once '../../control/Teacher.php';
require_once '../../data/db_config.php';

$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$role = isset($_POST['role']) ? $_POST['role'] : '';

$hashed_password = password_hash($password, PASSWORD_DEFAULT);


if ($role === 'student') {
    $user = new Student($db);
} elseif ($role === 'teacher') {
    $user = new Teacher($db);
} else {
    die("Invalid role selected.");
}


$user->setEmail($email);
$user->setPassword($hashed_password);
$user->setFirstName($first_name);
$user->setLastName($last_name);


$user_id = $user->signup();

if ($user_id) {
    // session storage and redirection
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $role;
    header('Location: dashboard.php');
    exit();
} else {
    die("Signup failed. Please try again.");
}
?>