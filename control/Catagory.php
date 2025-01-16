

<?php

require_once '../data/db_config.php';




class Category{
    protected $db;
    protected $category_id;
    protected $category_name;
    protected $created_at;


    function __contruct($db, $category_id, $category_name, $created_at)
    {
        $this->db = $db;
        $this->category_id = $category_id;
        $this->category_name = $category_name;
        $this->created_at = $created_at;
    }


    //getters

    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function getCategoryName()
    {
        return $this->category_name;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    //setters

    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }
    public function setCategoryName($category_name)
    {
        $this->category_name = $category_name;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function createCategory($category_name)
    {
        $sql = "INSERT INTO categories (category_name) VALUES (:category_name)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['category_name' => $category_name]);
        return $this->db->lastInsertId();
    }

    public function deleteCategory($category_id)
    {
        $sql = "DELETE FROM categories WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['category_id' => $category_id]);
        return true;
    }

    public function updateCategory($category_id, $category_name)
    {
        $sql = "UPDATE categories SET category_name = :category_name WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['category_name' => $category_name, 'category_id' => $category_id]);
        return true;
    }


}


?>