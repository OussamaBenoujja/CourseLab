<?php

require_once '../data/db_config.php';
require_once '../control/User.php';
require_once '../control/Student.php';

session_start();

if(isset($_GET['id'])){
    $id = $_SESSION['user_id'];
    $course_id = $_GET['id'];
    if(isset($_GET['type'])){$course_type = $_GET['type'];}
    $action = $_GET['action'];
    if($action == 'leave'){
        $st = new Student($db);
        $st->setUserId($id);
        $st->leaveCourse($course_id);
        header('Location: student_dashboard.php?unenrolled=true');
    }
    
    if($action == 'view'){
        if($course_type == 'text'){
            header('Location: view_course.php?id='.$course_id);
        } else if($course_type == 'video'){
            header('Location: view_video.php?id='.$course_id);
        }
    }

} else {
    header('Location: login.php');
    exit();
}

?>