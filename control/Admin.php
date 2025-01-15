<?php

require_once 'User.php';

class Admin extends User
{
    public function validateTeacherAccount($user)
    {
        if ($user->getRole() === 'teacher') {
            return true;
        }
        return false;
    }

    public function manageUser($user, $action)
    {
        switch ($action) {
            case 'add':
                return true;
            case 'edit':
                return true;
            case 'delete':
                return true;
            default:
                return false;
        }
    }

    public function manageCourse($course, $action)
    {
        switch ($action) {
            case 'add':
                return true;
            case 'edit':
                return true;
            case 'delete':
                return true;
            default:
                return false;
        }
    }

    public function manageTags($tag, $action)
    {
        switch ($action) {
            case 'add':
                return true;
            case 'edit':
                return true;
            case 'delete':
                return true;
            default:
                return false;
        }
    }

    public function manageCategories($category, $action)
    {
        switch ($action) {
            case 'add':
                return true;
            case 'edit':
                return true;
            case 'delete':
                return true;
            default:
                return false;
        }
    }

    public function viewGlobalStatistics()
    {
        $statistics = [
            'total_users' => 0, 
            'total_courses' => 0, 
            'total_enrollments' => 0, 
            'total_teachers' => 0, 
            'total_students' => 0, 
        ];

        return $statistics;
    }
}
?>
