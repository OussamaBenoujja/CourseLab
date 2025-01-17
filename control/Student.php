<?php

require_once 'User.php';

class Student extends User
{
    private $enrolledCourses = [];


    public function __construct(PDO $db)
{
    parent::__construct($db);
   
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
