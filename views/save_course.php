<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    http_response_code(403);
    echo "Access forbidden.";
    exit();
}


require_once '../data/db_config.php';


require_once '../control/Course.php';
require_once '../control/textcontent.php';
require_once '../control/Category.php';
require_once '../control/Tag.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $courseName = $_POST['course-name'];
    $courseDescription = $_POST['course-description'];
    $category_id = $_POST['category'];
    $tags = $_POST['tags'];
    $content = $_POST['editor'];

    
    if (empty($courseName) || empty($courseDescription) || empty($category_id) || empty($content)) {
        die('All fields are required.');
    }

    
    $textCourse = new TextContent($db);
    $textCourse->title = $courseName;
    $textCourse->description = $courseDescription;
    $textCourse->teacher_id = $_SESSION['user_id'];
    $textCourse->category = $category_id;
    $textCourse->content = $content;

    try {
        
        $db->beginTransaction();
        $textCourse->saveContent();

        foreach ($tags as $tagId) {
            $tag = new Tag($db, $tagId);
            $textCourse->addTag($tag);
        }

        $db->commit();
        header("Location: success.php");
        exit();

    } catch (Exception $e) {
        $db->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>