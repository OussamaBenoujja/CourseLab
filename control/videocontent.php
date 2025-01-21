<?php

require_once 'Course.php';

class VideoContent extends Course {
    public function __construct(PDO $db, $course_id = null) {
        parent::__construct($db, $course_id);
        $this->content_type = 'video';
    }

    public function displayContent() {
        echo "<video controls><source src='{$this->content}' type='video/mp4'>Your browser does not support the video tag.</video>";
    }

    public function saveContent() {

        $this->saveCourse();
    }
}