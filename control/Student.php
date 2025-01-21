<?php

require_once 'User.php';

class Student extends User
{
    private $enrolledCourses = [];


    public function __construct(PDO $db)
    {
        parent::__construct($db);
   
    }

    public function fetchProfile()
    {
        $query = "SELECT * FROM users WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $this->email = $result['email'];
            $this->first_name = $result['first_name'];
            $this->last_name = $result['last_name'];
            $this->bio = $result['bio'];
            $this->profile_image = $result['profile_image'];
            $this->banner_image = $result['banner_image'];
            return true;
        } else {
            return false;
        }
    }

    public function joinCourse($course)
    {
        
        $query = "SELECT COUNT(*) FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $this->user_id);
        $stmt->bindParam(':course_id', $course);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            return false;
        }
        
        $query = "INSERT INTO enrollments (student_id, course_id) VALUES (:student_id, :course_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $this->user_id);
        $stmt->bindParam(':course_id', $course);
        
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    public function viewMyCourses()
    {
        $query = "SELECT * FROM course_details WHERE student_id = :student_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $this->user_id);
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $courses;
    }

    public function leaveCourse($course)
    {
        $query = "DELETE FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $this->user_id);
        $stmt->bindParam(':course_id', $course);
        return $stmt->execute();
    }

    public function signup()
    {
        $this->setRole('student');
        return parent::signup();
    }


}
?>
