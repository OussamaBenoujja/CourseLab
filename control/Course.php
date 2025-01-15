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

    // Getters
    public function getCourseId()
    {
        return $this->course_id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getTeacherId()
    {
        return $this->teacher_id;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getBannerImage()
    {
        return $this->banner_image;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getContentType()
    {
        return $this->content_type;
    }

    // Setters
    public function setCourseId($course_id)
    {
        $this->course_id = $course_id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setTeacherId($teacher_id)
    {
        $this->teacher_id = $teacher_id;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function setBannerImage($banner_image)
    {
        $this->banner_image = $banner_image;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }



}