<?php

require_once '../data/db_config.php';

class Course
{
    protected PDO $db;
    private $course_id;
    private $title;
    private $description;
    private $teacher_id;
    private $category;
    private $banner_image;
    private $created_at;
    private $content;
    private $content_type;
    private $enrolledStudents = [];
    private $reviews = [];
    private $tags = [];

    public function __construct(PDO $db, $course_id, $title, $description, $teacher_id, $category, $banner_image, $created_at, $content, $content_type)
    {
        $this->db = $db;
        $this->course_id = $course_id;
        $this->title = $title;
        $this->description = $description;
        $this->teacher_id = $teacher_id;
        $this->category = $category;
        $this->banner_image = $banner_image;
        $this->created_at = $created_at;
        $this->content = $content;
        $this->content_type = $content_type;
    }

    // Getters and Setters (omitted for brevity)

    public function saveContent($content, $content_type)
    {
        $this->content = $content;
        $this->content_type = $content_type;

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM courses WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $stmt = $this->db->prepare("UPDATE courses SET title = :title, description = :description, teacher_id = :teacher_id, category = :category, banner_image = :banner_image, created_at = :created_at, content = :content, content_type = :content_type WHERE course_id = :course_id");
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':teacher_id', $this->teacher_id, PDO::PARAM_INT);
            $stmt->bindParam(':category', $this->category);
            $stmt->bindParam(':banner_image', $this->banner_image);
            $stmt->bindParam(':created_at', $this->created_at);
            $stmt->bindParam(':content', $this->content);
            $stmt->bindParam(':content_type', $this->content_type);
            $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare("INSERT INTO courses (course_id, title, description, teacher_id, category, banner_image, created_at, content, content_type) VALUES (:course_id, :title, :description, :teacher_id, :category, :banner_image, :created_at, :content, :content_type)");
            $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':teacher_id', $this->teacher_id, PDO::PARAM_INT);
            $stmt->bindParam(':category', $this->category);
            $stmt->bindParam(':banner_image', $this->banner_image);
            $stmt->bindParam(':created_at', $this->created_at);
            $stmt->bindParam(':content', $this->content);
            $stmt->bindParam(':content_type', $this->content_type);
        }

        $stmt->execute();
        return true;
    }

    public static function getAllCourses(PDO $db)
    {
        $courses = [];
        $stmt = $db->prepare("SELECT DISTINCT course_id, title, description, category, banner_image, course_created_at, content, content_type, teacher_id FROM course_details");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $course = new Course(
                $db,
                $row['course_id'],
                $row['title'],
                $row['description'],
                $row['teacher_id'],
                $row['category'],
                $row['banner_image'],
                $row['course_created_at'],
                $row['content'],
                $row['content_type']
            );
            $courses[] = $course;
        }

        return $courses;
    }

    public static function getCourseById(PDO $db, $id)
    {
        $stmt = $db->prepare("SELECT * FROM course_details WHERE course_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return null;
        }

        $course = new Course(
            $db,
            $rows[0]['course_id'],
            $rows[0]['title'],
            $rows[0]['description'],
            $rows[0]['teacher_id'],
            $rows[0]['category'],
            $rows[0]['banner_image'],
            $rows[0]['course_created_at'],
            $rows[0]['content'],
            $rows[0]['content_type']
        );

        foreach ($rows as $row) {
            if (!empty($row['student_id'])) {
                $student = [
                    'id' => $row['student_id'],
                    'first_name' => $row['student_first_name'],
                    'last_name' => $row['student_last_name'],
                    'profile_image' => $row['student_profile_image'],
                    'enrollment_date' => $row['enrollment_date'],
                    'completion_status' => $row['completion_status']
                ];
                $course->enrolledStudents[] = $student;
            }

            if (!empty($row['review_id'])) {
                $review = [
                    'id' => $row['review_id'],
                    'comment' => $row['comment'],
                    'rating' => $row['rating'],
                    'reviewed_at' => $row['reviewed_at']
                ];
                $course->reviews[] = $review;
            }
        }

        return $course;
    }


    public function deleteCourse()
{
    try {
        // Start a database transaction
        $this->db->beginTransaction();

        // Retrieve content and banner_image paths
        $stmt = $this->db->prepare("SELECT content, content_type, banner_image FROM courses WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Delete content file if it's a video
            if ($row['content_type'] == 'video' || $row['content_type'] == 'file/html') {
                $contentPath = $row['content'];
                if (file_exists($contentPath)) {
                    unlink($contentPath);
                }
            }

            // Delete banner image file
            $bannerImagePath = $row['banner_image'];
            if (file_exists($bannerImagePath)) {
                unlink($bannerImagePath);
            }
        }

        // Delete related database entries
        $stmt = $this->db->prepare("DELETE FROM enrollments WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->db->prepare("DELETE FROM reviews WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->db->prepare("DELETE FROM certificates WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->db->prepare("DELETE FROM coursetags WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->db->prepare("DELETE FROM courses WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();

        // Commit the transaction
        $this->db->commit();
        return true;
    } catch (Exception $e) {
        // Roll back the transaction in case of any error
        $this->db->rollBack();
        throw $e;
    }
}

}
?>