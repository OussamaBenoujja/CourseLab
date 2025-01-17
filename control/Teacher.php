<?php

require_once 'User.php';

class Teacher extends User
{
    
    private $createdCourses = [];

    
    public function __construct(PDO $db)
    {
        parent::__construct($db);
    }

    public function signup()
    {
        $this->setRole('teacher');
        return parent::signup();
    }

    public function addCourse($course)
    {
        $this->createdCourses[] = $course;
        return true;
    }

    public function editCourse($course)
    {
        foreach ($this->createdCourses as $key => $createdCourse) {
            if ($createdCourse->getCourseId() === $course->getCourseId()) {
                $this->createdCourses[$key] = $course;
                return true;
            }
        }
        return false;
    }

    public function deleteCourse($course)
    {
        foreach ($this->createdCourses as $key => $createdCourse) {
            if ($createdCourse->getCourseId() === $course->getCourseId()) {
                unset($this->createdCourses[$key]);
                return true;
            }
        }
        return false;
    }

    public function viewStatistics()
    {
        $statistics = [
            'total_courses' => count($this->createdCourses),
            'total_students' => 0,
            'average_rating' => 0,
        ];

        return $statistics;
    }
}
?>
