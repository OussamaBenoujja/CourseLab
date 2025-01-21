<?php

require_once 'User.php';
require_once 'Course.php';
require_once 'Tag.php';
require_once 'Category.php';
require_once 'Student.php';
require_once 'Teacher.php';

class Admin extends User
{
    public function validateTeacherAccount($teacher_id)
    {
        $query = "UPDATE users SET isActive = 'yes' WHERE user_id = :teacher_id AND role = 'teacher'";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['teacher_id' => $teacher_id]);
    }

    public function manageUserStatus($user_id, $status)
    {
        $validStatuses = ['active', 'suspended', 'deleted'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid status");
        }

        $query = "UPDATE users SET status = :status WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['status' => $status, 'user_id' => $user_id]);
    }

    public function manageCourse($course_id, $action)
    {
        switch ($action) {
            case 'approve':
                $query = "UPDATE courses SET status = 'approved' WHERE course_id = :course_id";
                break;
            case 'suspend':
                $query = "UPDATE courses SET status = 'suspended' WHERE course_id = :course_id";
                break;
            case 'delete':
                $query = "DELETE FROM courses WHERE course_id = :course_id";
                break;
            default:
                throw new Exception("Invalid action");
        }
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['course_id' => $course_id]);
    }

    public function bulkInsertTags(array $tags)
    {
        $query = "INSERT INTO tags (name) VALUES (:name)";
        $stmt = $this->db->prepare($query);
        
        $this->db->beginTransaction();
        try {
            foreach ($tags as $tag) {
                $stmt->execute(['name' => $tag]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getGlobalStatistics()
    {
        // Basic counts
        $basicStats = $this->db->query("
            SELECT 
                (SELECT COUNT(*) FROM courses) as total_courses,
                (SELECT COUNT(*) FROM users WHERE role = 'student') as total_students,
                (SELECT COUNT(*) FROM users WHERE role = 'teacher') as total_teachers,
                (SELECT COUNT(*) FROM enrollments) as total_enrollments
        ")->fetch(PDO::FETCH_ASSOC);

        // Courses by category
        $categoryStats = $this->db->query("
            SELECT c.category_name, COUNT(*) as count
            FROM courses co
            JOIN categories c ON co.category = c.category_id
            GROUP BY c.category_id, c.category_name
            ORDER BY count DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Most popular course
        $popularCourse = $this->db->query("
            SELECT c.course_id, c.title, COUNT(e.enrollment_id) as enrollment_count
            FROM courses c
            JOIN enrollments e ON c.course_id = e.course_id
            GROUP BY c.course_id, c.title
            ORDER BY enrollment_count DESC
            LIMIT 1
        ")->fetch(PDO::FETCH_ASSOC);

        // Top 3 teachers
        $topTeachers = $this->db->query("
            SELECT 
                u.user_id,
                u.first_name,
                u.last_name,
                COUNT(DISTINCT c.course_id) as course_count,
                COUNT(DISTINCT e.student_id) as student_count,
                COALESCE(AVG(r.rating), 0) as avg_rating
            FROM users u
            JOIN courses c ON u.user_id = c.teacher_id
            LEFT JOIN enrollments e ON c.course_id = e.course_id
            LEFT JOIN reviews r ON c.course_id = r.course_id
            WHERE u.role = 'teacher'
            GROUP BY u.user_id, u.first_name, u.last_name
            ORDER BY student_count DESC, avg_rating DESC
            LIMIT 3
        ")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'basic_stats' => $basicStats,
            'category_stats' => $categoryStats,
            'popular_course' => $popularCourse,
            'top_teachers' => $topTeachers
        ];
    }

    public function getPendingTeachers()
    {
        $query = "SELECT user_id, first_name, last_name, email, created_at 
                 FROM users 
                 WHERE role = 'teacher' AND isActive = 'no'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function deactivateTeacherAccount($teacher_id)
    {
        $query = "UPDATE users SET isActive = 'no' WHERE user_id = :teacher_id AND role = 'teacher'";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['teacher_id' => $teacher_id]);
    }

    public function getAllUsers()
    {
        $query = "SELECT user_id, first_name, last_name, email, role, status, created_at 
                 FROM users 
                 ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>