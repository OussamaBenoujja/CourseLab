<?php

require_once 'Course.php';

class TextContent extends Course {
    public function __construct(PDO $db, $course_id = null) {
        parent::__construct($db, $course_id);
        $this->content_type = 'text';
    }

    public function displayContent() {
        $contentPath = $this->getContent();
        if (!empty($contentPath) && file_exists($contentPath)) {
            echo file_get_contents($contentPath);
        } else {
            echo "Content not available.";
        }
    }

    public function saveContent() {
        $contentPath = $this->getContent();
        if (!empty($contentPath)) {
            if (file_put_contents($contentPath, $this->content) === false) {
                throw new Exception("Failed to save course content.");
            }
        } else {
            throw new Exception("Content path not set.");
        }
        $this->saveCourse();
    }

    public function setContent($content) {
        $this->content = $content;
    }
}