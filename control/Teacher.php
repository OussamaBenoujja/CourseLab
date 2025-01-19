<?php

require_once 'User.php';
require_once 'Course.php';

class Teacher extends User
{

    public function __construct(PDO $db, $user_id)
    {
        parent::__construct($db, $user_id);
        $this->db = $db;
        $this->user_id = $user_id;
    }

    public function getTeacherCourses()
    {
        $stmt = $this->db->prepare("SELECT * FROM course_details WHERE teacher_id = :user_id");
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $courses;
    }

    public function getTeacherStats()
    {
        $stats = array();

        // Total Courses
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM courses WHERE teacher_id = :user_id");
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_courses'] = $result['total'];

        // Total Students
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT student_id) AS total
            FROM enrollments
            WHERE course_id IN (
                SELECT course_id FROM courses WHERE teacher_id = :user_id
            )
        ");
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_students'] = $result['total'];

        // Average Rating
        $stmt = $this->db->prepare("
            SELECT AVG(rating) AS average
            FROM reviews
            WHERE course_id IN (
                SELECT course_id FROM courses WHERE teacher_id = :user_id
            )
        ");
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['average_rating'] = $result['average'] ?: 0;

        return $stats;
    }
}
?>