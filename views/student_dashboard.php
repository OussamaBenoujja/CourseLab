<?php
session_start();
require_once '../data/db_config.php';
require_once '../control/User.php';
require_once '../control/Student.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header('Location: login.php');
    exit();
}
?>

<?php
if (!isset($_POST['action'])) {
?>


<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="flex">
    <aside class="w-1/5 bg-gray-200 p-4">
        <a href="#" class="nav-link block mb-4 text-blue-600 hover:text-blue-800" data-section="profile">Profile</a>
        <a href="#" class="nav-link block mb-4 text-blue-600 hover:text-blue-800" data-section="enrolled-courses">Enrolled Courses</a>
        <a href="#" class="nav-link block mb-4 text-blue-600 hover:text-blue-800" data-section="view-courses">View Courses</a>
    </aside>

    <div id="content" class="w-4/5 p-4">
        <!-- Content will be loaded here -->
    </div>
</div>

<script>
$(document).ready(function() {
    // Load default section on page load
    $('.nav-link').first().click();

    $('.nav-link').click(function(e) {
        e.preventDefault();
        var section = $(this).data('section');
        $.ajax({
            url: 'student_dashboard.php',
            type: 'POST',
            data: { action: section },
            dataType: 'html',
            success: function(data) {
                $('#content').html(data);
            }
        });
    });

    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'update_profile.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                alert(data);
                // Reload the profile section
                $('.nav-link[data-section="profile"]').click();
            }
        });
    });
});
</script>

<?php
} else {
    $action = $_POST['action'] ;
    switch ($action) {
        case 'profile':
            $student = new Student($db);
            $student->setUserId($_SESSION['user_id']);
            if ($student->fetchProfile()) {
                echo '<form id="profile-form" action="update_profile.php" enctype="multipart/form-data">';
                echo '<div class="mb-4">';
                echo '<label class="block text-gray-700 text-sm font-bold mb-2" for="first_name">First Name</label>';
                echo '<input type="text" name="first_name" value="' . htmlspecialchars($student->getFirstName()) . '" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">';
                echo '</div>';
                echo '<div class="mb-4">';
                echo '<label class="block text-gray-700 text-sm font-bold mb-2" for="last_name">Last Name</label>';
                echo '<input type="text" name="last_name" value="' . htmlspecialchars($student->getLastName()) . '" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">';
                echo '</div>';
                echo '<div class="mb-4">';
                echo '<label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>';
                echo '<input type="email" name="email" value="' . htmlspecialchars($student->getEmail()) . '" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">';
                echo '</div>';
                echo '<div class="mb-4">';
                echo '<label class="block text-gray-700 text-sm font-bold mb-2" for="bio">Bio</label>';
                echo '<textarea name="bio" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">' . htmlspecialchars($student->getBio()) . '</textarea>';
                echo '</div>';
                echo '<div class="mb-4">';
                echo '<label class="block text-gray-700 text-sm font-bold mb-2" for="profile_image">Profile Image</label>';
                echo '<input type="file" name="profile_image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">';
                echo '</div>';
                echo '<div class="mb-4">';
                echo '<label class="block text-gray-700 text-sm font-bold mb-2" for="banner_image">Banner Image</label>';
                echo '<input type="file" name="banner_image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">';
                echo '</div>';
                echo '<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Profile</button>';
                echo '</form>';
            } else {
                echo '<p>No profile information found.</p>';
            }
            break;
        case 'enrolled-courses':
            $student = new Student($db);
            $student->setUserId($_SESSION['user_id']);
            // Implement logic to fetch and display enrolled courses
            echo '<h2>Enrolled Courses</h2>';
            // Add enrolled courses display here
            break;
        case 'view-courses':
            // Implement logic to fetch and display available courses
            echo '<h2>Available Courses</h2>';
            // Add available courses display here
            break;
        default:
            echo '<p>Please select a section from the sidebar.</p>';
            break;
    }
    exit();
}
?>