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
        
        $this->enrolledCourses[] = $course;
        return true;
    }

    public function viewMyCourses()
    {
        return $this->enrolledCourses;
    }

    public function leaveCourse($course)
    {
        $key = array_search($course, $this->enrolledCourses);
        if ($key !== false) {
            unset($this->enrolledCourses[$key]);
            return true;
        }
        return false;
    }

    public function signup()
    {
        $this->setRole('student');
        return parent::signup();
    }


}
?>
