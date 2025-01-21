<?php

require_once 'User.php';
require_once 'Course.php';

class Teacher extends User {
    private $isActive;
    private $courseStats;
    private $studentStats;
    private $ratingStats;
    private $engagementStats;

    public function __construct(PDO $db, $user_id = null) {
        parent::__construct($db, $user_id);
        $this->db = $db;
        $this->user_id = $user_id;
        $this->isActive = $this->fetchIsActive();
        $this->initializeStats();
    }

    public function signup()
    {
        $this->setRole('teacher');
        return parent::signup();
    }

    private function initializeStats() {
        $this->courseStats = $this->calculateCourseStats();
        $this->studentStats = $this->calculateStudentStats();
        $this->ratingStats = $this->calculateRatingStats();
        $this->engagementStats = $this->calculateEngagementStats();
    }

    private function fetchIsActive(): bool {
        $query = "SELECT isActive FROM users WHERE user_id = :user_id AND role = 'teacher'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['isActive'] === 'yes';
    }

    public function isActive(): bool {
        return $this->isActive;
    }

    public function activateTeacher(): void {
        $this->updateIsActive('yes');
    }

    public function deactivateTeacher(): void {
        $this->updateIsActive('no');
    }

    private function updateIsActive(string $status): void {
        $query = "UPDATE users SET isActive = :status WHERE user_id = :user_id AND role = 'teacher'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $this->isActive = ($status === 'yes');
        }
    }

    private function calculateCourseStats(): array {
        $stats = [];
        
        $query = "SELECT 
            COUNT(*) as total_courses,
            SUM(CASE WHEN content_type = 'video' THEN 1 ELSE 0 END) as video_courses,
            SUM(CASE WHEN content_type = 'text' THEN 1 ELSE 0 END) as text_courses
        FROM courses 
        WHERE teacher_id = :user_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $query = "SELECT 
            c.category_name,
            COUNT(*) as count
        FROM courses co
        JOIN categories c ON co.category = c.category_id
        WHERE teacher_id = :user_id
        GROUP BY c.category_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        $stats['by_category'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }

    private function calculateStudentStats(): array {
        $query = "SELECT 
            COUNT(DISTINCT e.student_id) as total_students,
            COUNT(CASE WHEN e.completion_status = 1 THEN 1 END) as completed_courses,
            COUNT(e.enrollment_id) as total_enrollments,
            COUNT(CASE WHEN e.enrollment_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_enrollments_30d
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        WHERE c.teacher_id = :user_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function calculateRatingStats(): array {
        $query = "SELECT 
            COALESCE(AVG(r.rating), 0) as average_rating,
            COUNT(r.review_id) as total_reviews,
            COUNT(CASE WHEN r.rating = 5 THEN 1 END) as five_star_reviews,
            COUNT(CASE WHEN r.rating = 4 THEN 1 END) as four_star_reviews,
            COUNT(CASE WHEN r.rating = 3 THEN 1 END) as three_star_reviews,
            COUNT(CASE WHEN r.rating = 2 THEN 1 END) as two_star_reviews,
            COUNT(CASE WHEN r.rating = 1 THEN 1 END) as one_star_reviews,
            COUNT(CASE WHEN r.reviewed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_reviews_30d
        FROM reviews r
        JOIN courses c ON r.course_id = c.course_id
        WHERE c.teacher_id = :user_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function calculateEngagementStats(): array {
        $query = "SELECT 
            c.course_id,
            c.title,
            COUNT(DISTINCT e.student_id) as enrollment_count,
            COUNT(DISTINCT r.review_id) as review_count,
            COALESCE(AVG(r.rating), 0) as avg_rating,
            SUM(CASE WHEN e.completion_status = 1 THEN 1 ELSE 0 END) as completions
        FROM courses c
        LEFT JOIN enrollments e ON c.course_id = e.course_id
        LEFT JOIN reviews r ON c.course_id = r.course_id
        WHERE c.teacher_id = :user_id
        GROUP BY c.course_id, c.title
        ORDER BY enrollment_count DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeacherCourses(): array {
        $stmt = $this->db->prepare("SELECT * FROM course_details WHERE teacher_id = :user_id ORDER BY course_created_at DESC");
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeacherStats(): array {
        return [
            'courses' => $this->courseStats,
            'students' => $this->studentStats,
            'ratings' => $this->ratingStats,
            'engagement' => $this->engagementStats
        ];
    }
}

?>
