<?php
session_start();
require_once '../data/db_config.php';
require_once '../control/User.php';
require_once '../control/Student.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header('Location: login.php');
    exit();
}

if(isset($_GET['unenrolled'])) {
    echo "
    <script>alert('You have successfully unrolled from the course.')</script>
    ";
    
}

$student = new Student($db);
$student->setUserId($_SESSION['user_id']);
$student->fetchProfile();

$Enrolledcourses = $student->viewMyCourses();

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
            $enrolledCourses = $student->viewMyCourses();
            ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if(!$enrolledCourses){ ?>
                    <p class="col-span-full text-center text-gray-600 dark:text-gray-400 text-xl">No courses found.</p>
                    <?php } ?>
                <?php foreach ($enrolledCourses as $course): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Course Banner -->
                        <div class="h-48 overflow-hidden">
                            <img src="<?php echo htmlspecialchars($course['banner_image'] ?? '/assets/default-course-banner.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($course['title']); ?>" 
                                 class="w-full h-full object-cover">
                        </div>
                        
                        <!-- Course Content -->
                        <div class="p-4">
                            <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($course['title']); ?></h3>
                            
                            <!-- Teacher Info -->
                            <div class="flex items-center mb-3">
                                <img src="<?php echo htmlspecialchars($course['teacher_profile_image'] ?? '/assets/default-profile.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($course['teacher_first_name'] . ' ' . $course['teacher_last_name']); ?>" 
                                     class="w-8 h-8 rounded-full mr-2">
                                <span class="text-gray-600">
                                    <?php echo htmlspecialchars($course['teacher_first_name'] . ' ' . $course['teacher_last_name']); ?>
                                </span>
                            </div>
                            
                            <!-- Course Details -->
                            <p class="text-gray-600 mb-3 line-clamp-2">
                                <?php echo htmlspecialchars($course['description']); ?>
                            </p>
                            
                            <!-- Progress Section -->
                            <div class="mb-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>Progress</span>
                                    <span><?php echo htmlspecialchars($course['completion_status']); ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" 
                                         style="width: <?php echo htmlspecialchars($course['completion_status']); ?>%">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Course Meta -->
                            <div class="flex justify-between text-sm text-gray-500">
                                <span>Category: <?php echo htmlspecialchars($course['category_name']); ?></span>
                                <span>Enrolled: <?php echo date('M d, Y', strtotime($course['enrollment_date'])); ?></span>
                            </div>
                            
                            <!-- Review Section (if exists) -->
                            <?php if (isset($course['review_id'])): ?>
                            <div class="mt-3 pt-3 border-t">
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $course['rating']): ?>
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            <?php else: ?>
                                                <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-sm text-gray-500 ml-2">
                                        <?php echo date('M d, Y', strtotime($course['reviewed_at'])); ?>
                                    </span>
                                </div>
                                <?php if (!empty($course['comment'])): ?>
                                <p class="text-sm text-gray-600 italic">
                                    "<?php echo htmlspecialchars($course['comment']); ?>"
                                </p>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Action Button -->
                            <a href="view_course_action.php?id=<?php echo htmlspecialchars($course['course_id']); ?>&type=<?php echo htmlspecialchars($course['content_type']); ?>&action=view">
                            <button 
                                    class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                                Continue Learning
                            </button>
                                </a>
                            <a href="view_course_action.php?id=<?php echo htmlspecialchars($course['course_id']); ?>&action=leave">
                            <button  
                                    class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                                Leave Course
                            </button>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


            <?php
            break;

        
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
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans" style="visibility: hidden;">
<?php include '../includes/navbar.php'; ?>  
<div class="flex flex-col sm:flex-row">
    <button id="sidebar-toggle" class="sm:hidden px-4 py-2 text-gray-600 hover:text-gray-800 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <aside id="sidebar" class="w-full sm:w-1/5 bg-gray-200 p-4 flex-shrink-0">
        <a href="#" class="nav-link block mb-4 text-blue-600 hover:text-blue-800" data-section="profile">Profile</a>
        <a href="#" class="nav-link block mb-4 text-blue-600 hover:text-blue-800" data-section="enrolled-courses">Enrolled Courses</a>
    </aside>

    <div id="main-content" class="w-full sm:w-4/5 p-4 flex-1">
    <div id="profile-image-container" style="display: none;" class="mb-6 relative h-48">
            <!-- Banner Image -->
            <img id="banner-image-preview" 
                 src="<?php echo htmlspecialchars($student->getBannerImage()); ?>" 
                 alt="Banner Image" 
                 class="w-full h-32 object-cover rounded-t">
                 
            <!-- Profile Image -->
            <div class="absolute top-24 left-0 w-full flex justify-center">
                <img id="profile-image-preview" 
                     src="<?php echo htmlspecialchars($student->getProfileImage()); ?>" 
                     alt="Profile Image" 
                     class="w-32 h-32 rounded-full object-cover border-4 border-white">
            </div>
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

    $(document).on('submit', '#profile-form', function(e) {
        e.preventDefault();
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

        if (valid) {
            // Handle form submission via AJAX
            var formData = new FormData(this);
            $.ajax({
                url: 'update_profile.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert('Profile updated successfully!');
                        location.reload();
                    } else {
                        alert('Error updating profile: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while updating the profile.');
                }
            });
        }
    });
});
</script>

</body>
</html>