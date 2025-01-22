<?php



if (isset($_SESSION['user_id']) && $_SESSION['status'] === 'suspended') {
    header('Location: suspended.php');
    exit();
}

?>