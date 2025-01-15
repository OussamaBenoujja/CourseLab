<?php

class Course
{
    private $course_id;
    private $title;
    private $description;
    private $teacher_id;
    private $category;
    private $banner_image;
    private $created_at;
    private $content;
    private $content_type;

    public function __construct($course_id, $title, $description, $teacher_id, $category, $banner_image, $created_at, $content, $content_type)
    {
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

    //getters

    public function getCourseId()
    {
        return $this->course_id;
    }


}