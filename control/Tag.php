<?php

require_once(dirname(__FILE__) . '/../data/db_config.php');

class Tag {
    protected $db;
    public $tag_id;
    public $name;

    public function __construct(PDO $db, $tag_id = null) {
        $this->db = $db;
        if ($tag_id) {
            $this->loadTag($tag_id);
        }
    }

    public function loadTag($tag_id) {
        $stmt = $this->db->prepare("SELECT * FROM tags WHERE tag_id = :tag_id");
        $stmt->execute(['tag_id' => $tag_id]);
        $tag = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($tag) {
            $this->tag_id = $tag['tag_id'];
            $this->name = $tag['name'];
        } else {
            // Handle tag not found
            throw new Exception("Tag not found.");
        }
    }

    public function getAllTags() {
        $stmt = $this->db->prepare("SELECT * FROM tags");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}