<?php

require_once 'User.php';

class Student extends User
{
    private $enrolledCourses = [];

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

    public function signup($email, $password, $first_name, $last_name)
    {
        $sql = "INSERT INTO users (email, password, first_name, last_name, role) VALUES (:email, :password, :first_name, :last_name, 'student')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email, 'password' => $password, 'first_name' => $first_name, 'last_name' => $last_name]);
        return $this->db->lastInsertId();
    }

    
}
?>
