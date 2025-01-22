

<?php

session_start();

include '../includes/auth_check.php';

require_once '../control/User.php';
require_once '../control/Student.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$st = new Student($db);
$st->setUserId($_SESSION['user_id']);
if(isset($_GET['course_id'])){
    $course_id = $_GET['course_id'];
    $st->joinCourse($course_id);
    header('Location: home.php?enrolled=true');
}

?>