<?php
require_once __DIR__ . '/../data/db_config.php';
require_once __DIR__ . '/../control/Course.php';
require_once __DIR__ . '/../control/TextContent.php';
require_once __DIR__ . '/../control/VideoContent.php';


ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/delete_errors.log');


ob_start();
header('Content-Type: application/json');

try {
    
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        throw new Exception("Invalid request method", 405);
    }

    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
        throw new Exception("Unauthorized access", 401);
    }

    
    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        throw new Exception("Invalid course ID", 400);
    }
    $course_id = (int)$_GET['id'];

    // Validate and sanitize content type
    if (!isset($_GET['type']) || !in_array($_GET['type'], ['text', 'video'])) {
        throw new Exception("Invalid course type", 400);
    }
    $content_type = $_GET['type'];

    $course = $content_type === 'text' 
        ? new TextContent($db, $course_id)
        : new VideoContent($db, $course_id);
    if ($course->getTeacherId() !== $_SESSION['user_id']) {
        throw new Exception("Course ownership verification failed", 403);
    }

    $course->deleteCourse();

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Course deleted successfully'
    ]);
    exit();

} catch (Exception $e) {
    
    ob_end_clean();
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
    exit();
}