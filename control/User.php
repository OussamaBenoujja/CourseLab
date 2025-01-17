<?php

require_once __DIR__ .'\..\data\db_config.php';

class User
{
    protected PDO $db ;
    protected $user_id;
    protected $email;
    protected $password;
    protected $first_name;
    protected $last_name;
    protected $role;
    protected $profile_image;
    protected $created_at;
    protected $banner_image;
    protected $bio;

    function __construct(PDO $db, $user_id = null, $email = null, $password = null, $first_name = null, $last_name = null, $role = null, $profile_image = null, $created_at = null, $banner_image = null, $bio = null)
    {
        $this->db = $db;
        $this->user_id = $user_id;
        $this->email = $email;
        $this->password = $password;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->role = $role;
        $this->profile_image = $profile_image;
        $this->created_at = $created_at;
        $this->banner_image = $banner_image;
        $this->bio = $bio;
    }

    //getters
    public function getUserId()
    {
        return $this->user_id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getProfileImage()
    {
        return $this->profile_image;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getBannerImage()
    {
        return $this->banner_image;
    }

    public function getBio()
    {
        return $this->bio;
    }

    //setters

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }
    
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }
    public function setProfileImage($profile_image)
    {
        $this->profile_image = $profile_image;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function setBannerImage($banner_image)
    {
        $this->banner_image = $banner_image;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;
    }
    
    public function signup()
    {
        if ($this->role == 'student' || $this->role == 'teacher') {
            $query = "INSERT INTO users (email, password, first_name, last_name, role) VALUES (:email, :password, :first_name, :last_name, :role)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':first_name', $this->first_name);
            $stmt->bindParam(':last_name', $this->last_name);
            $stmt->bindParam(':role', $this->role);
            $stmt->execute();
            return $this->db->lastInsertId();
        } elseif ($this->role == 'admin') {
            return false;
        }
    }


    //methods

    public function login($email, $password)
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && password_verify($password, $result['password'])) {
            return $result;
        } else {
            return false;
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: ../home.php');
    }

    public function updateProfile($user_id, $first_name, $last_name, $email, $bio, $profile_image, $banner_image)
    {
        $query = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, bio = :bio, profile_image = :profile_image, banner_image = :banner_image WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':profile_image', $profile_image);
        $stmt->bindParam(':banner_image', $banner_image);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return true;
    }


}


?>