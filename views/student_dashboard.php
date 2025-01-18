<?php
session_start();
require_once '../data/db_config.php';
require_once '../control/User.php';
require_once '../control/Student.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header('Location: login.php');
    exit();
}

$student = new Student($db);
$student->setUserId($_SESSION['user_id']);
$student->fetchProfile();

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $isAjax) {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    switch ($action) {
        case 'profile':
            ?>
            <form id="profile-form" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="first_name">First Name</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($student->getFirstName()); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="last_name">Last Name</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($student->getLastName()); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($student->getEmail()); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="bio">Bio</label>
                    <textarea name="bio" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($student->getBio()); ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="profile_image">Profile Image</label>
                    <input type="file" name="profile_image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="banner_image">Banner Image</label>
                    <input type="file" name="banner_image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Profile</button>
            </form>
            <?php
            break;
        case 'enrolled-courses':
            ?>
            <h2>Enrolled Courses</h2>
            <?php
            break;
        case 'view-courses':
            ?>
            <h2>Available Courses</h2>
            <?php
            break;
        default:
            break;
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans" style="visibility: hidden;">

<div class="flex flex-col sm:flex-row">
    <button id="sidebar-toggle" class="sm:hidden px-4 py-2 text-gray-600 hover:text-gray-800 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <aside id="sidebar" class="w-full sm:w-1/5 bg-gray-200 p-4 flex-shrink-0">
        <a href="#" class="nav-link block mb-4 text-blue-600 hover:text-blue-800" data-section="profile">Profile</a>
        <a href="#" class="nav-link block mb-4 text-blue-600 hover:text-blue-800" data-section="enrolled-courses">Enrolled Courses</a>
        <a href="#" class="nav-link block mb-4 text-blue-600 hover:text-blue-800" data-section="view-courses">View Courses</a>
    </aside>

    <div id="main-content" class="w-full sm:w-4/5 p-4 flex-1">
        <div id="profile-image-container" style="display: none;" class="mb-6">
            <img id="profile-image-preview" src="<?php echo htmlspecialchars($student->getProfileImage()); ?>" alt="Profile Image" class="w-32 h-32 rounded-full object-cover">
        </div>

        <div id="content">
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('body').css('visibility', 'visible');

    $('#sidebar-toggle').click(function() {
        $('#sidebar').toggleClass('hidden');
        $('#main-content').toggleClass('w-full sm:w-full');
    });

    $('.nav-link').first().click();

    $('.nav-link').click(function(e) {
        e.preventDefault();
        var section = $(this).data('section');

        if (section === 'profile') {
            $('#profile-image-container').show();
        } else {
            $('#profile-image-container').hide();
        }

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
        var valid = true;
        var firstName = $('input[name=first_name]').val().trim();
        var lastName = $('input[name=last_name]').val().trim();
        var email = $('input[name=email]').val().trim();
        var bio = $('textarea[name=bio]').val().trim();

        if (firstName === '') {
            alert('First Name is required.');
            valid = false;
        } else if (!/^[a-zA-Z ]+$/.test(firstName)) {
            alert('First Name can only contain letters and spaces.');
            valid = false;
        }

        if (lastName === '') {
            alert('Last Name is required.');
            valid = false;
        } else if (!/^[a-zA-Z ]+$/.test(lastName)) {
            alert('Last Name can only contain letters and spaces.');
            valid = false;
        }

        if (email === '') {
            alert('Email is required.');
            valid = false;
        } else if (!/\S+@\S+\.\S+/.test(email)) {
            alert('Email is not in a valid format.');
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>