<?php

require_once '../data/db_config.php';


abstract class Course
{
    protected PDO $db;
    protected $course_id;
    protected $title;
    protected $description;
    protected $teacher_id;
    protected $category;
    protected $banner_image;
    protected $created_at;
    protected $content;
    protected $content_type;

    public function __construct(PDO $db, $course_id = null)
    {
        $this->db = $db;
        if ($course_id) {
            $this->loadCourse($course_id);
        }
    }


    //getters

    public function getCourseId()
    {
        return $this->course_id;
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

    public function getTags()
    {
        return $this->tags;
    }

    public function getReviews()
    {
        return $this->reviews;
    }

    public function getEnrollments()
    {
        return $this->enrollments;
    }

    //setters

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

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }




    public function loadCourse($course_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $this->course_id = $result['course_id'];
            $this->title = $result['title'];
            $this->description = $result['description'];
            $this->teacher_id = $result['teacher_id'];
            $this->category = $result['category'];
            $this->banner_image = $result['banner_image'];
            $this->created_at = $result['created_at'];
            $this->content = $result['content'];
            $this->content_type = $result['content_type'];
        } else {
            throw new Exception("Course not found.");
        }
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContentType()
    {
        return $this->content_type;
    }

    public function getStudentCount()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS count FROM enrollments WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function getAverageRating()
    {
        $stmt = $this->db->prepare("SELECT AVG(rating) AS average FROM reviews WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['average'] ?: 0;
    }


    public function saveCourse() {
        if ($this->course_id) {
            $stmt = $this->db->prepare("UPDATE courses SET title = :title, description = :description, teacher_id = :teacher_id, category = :category, banner_image = :banner_image, content = :content, content_type = :content_type WHERE course_id = :course_id");
            $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare("INSERT INTO courses (title, description, teacher_id, category, banner_image, content, content_type) VALUES (:title, :description, :teacher_id, :category, :banner_image, :content, :content_type)");
        }
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':teacher_id', $this->teacher_id, PDO::PARAM_INT);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':banner_image', $this->banner_image);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':content_type', $this->content_type);
        $stmt->execute();
        if (!$this->course_id) {
            $this->course_id = $this->db->lastInsertId();
        }
        // Save tags association
        $this->saveTags();
    }

    public function deleteCourse() {
        // Delete course tags associations
        $stmt = $this->db->prepare("DELETE FROM coursetags WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();
        // Delete reviews
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();
        // Delete enrollments
        $stmt = $this->db->prepare("DELETE FROM enrollments WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();
        // Delete the course
        $stmt = $this->db->prepare("DELETE FROM courses WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function addTag(Tag $tag) {
        // Check if tag is already associated
        $stmt = $this->db->prepare("SELECT * FROM coursetags WHERE course_id = :course_id AND tag_id = :tag_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->bindParam(':tag_id', $tag->tag_id, PDO::PARAM_INT);
        $stmt->execute();
        if (!$stmt->fetch()) {
            $stmt = $this->db->prepare("INSERT INTO coursetags (course_id, tag_id) VALUES (:course_id, :tag_id)");
            $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
            $stmt->bindParam(':tag_id', $tag->tag_id, PDO::PARAM_INT);
            $stmt->execute();
            $this->tags[] = $tag;
        }
    }

    public function removeTag(Tag $tag) {
        $stmt = $this->db->prepare("DELETE FROM coursetags WHERE course_id = :course_id AND tag_id = :tag_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->bindParam(':tag_id', $tag->tag_id, PDO::PARAM_INT);
        $stmt->execute();
        // Remove from tags array
        foreach ($this->tags as $key => $t) {
            if ($t->tag_id == $tag->tag_id) {
                unset($this->tags[$key]);
                break;
            }
        }
    }

    public function addReview(Review $review) {
        $review->course_id = $this->course_id;
        $review->saveReview();
        $this->reviews[] = $review;
    }

    public function loadTags() {
        $stmt = $this->db->prepare("SELECT tag_id FROM coursetags WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();
        $tags = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tag = new Tag($this->db, $row['tag_id']);
            $tags[] = $tag;
        }
        $this->tags = $tags;
    }

    public function loadReviews() {
        $stmt = $this->db->prepare("SELECT review_id FROM reviews WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();
        $reviews = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $review = new Review($this->db, $row['review_id']);
            $reviews[] = $review;
        }
        $this->reviews = $reviews;
    }

    public function loadEnrollments() {
        $stmt = $this->db->prepare("SELECT enrollment_id FROM enrollments WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();
        $enrollments = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $enrollment = new Enrollment($this->db, $row['enrollment_id']);
            $enrollments[] = $enrollment;
        }
        $this->enrollments = $enrollments;
    }

    public function saveTags() {
        // First, delete all existing tags for this course
        $stmt = $this->db->prepare("DELETE FROM coursetags WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->execute();
        // Then, insert new associations
        foreach ($this->tags as $tag) {
            $this->addTag($tag);
        }
    }

    public static function getAllCourses(PDO $db) {
        $stmt = $db->prepare("SELECT * FROM courses");
        $stmt->execute();
        $courses = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $course = new Course($db, $row['course_id']);
            $courses[] = $course;
        }
        return $courses;
    }

    public static function getCourseById(PDO $db, $course_id) {
        $stmt = $db->prepare("SELECT * FROM course_details WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->execute();
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        return $course;
    }

    // Abstract methods
    abstract public function displayContent();
    abstract public function saveContent();
}