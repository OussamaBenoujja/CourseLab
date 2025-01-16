<?php

require_once '../data/db_config.php';

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

    function __contruct($db, $user_id, $email, $password, $first_name, $last_name, $role, $profile_image, $created_at, $banner_image, $bio)
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
        if ($this->role == 'Student' || $this->role == 'Teacher') {

            $query = "INSERT INTO users (email, password, first_name, last_name, role) VALUES (:email, :password, :first_name, :last_name, :role)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':first_name', $this->first_name);
            $stmt->bindParam(':last_name', $this->last_name);
            $stmt->bindParam(':role', $this->role);
            $stmt->execute();
            return $this->db->lastInsertId();

        }elseif ($this->role == 'Admin') {
            return false;
        }
    }


    //methods

    public function login($email, $password)
    {
        $query = "SELECT * FROM users WHERE email = :email AND password = :password";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result)
        {
            return $result;
        }
        else
        {
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