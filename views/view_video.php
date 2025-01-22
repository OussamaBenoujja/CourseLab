<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


if (isset($_GET['id'])) {
    require_once '../control/Course.php';
    require_once '../control/videocontent.php';
    require_once '../control/User.php';
    require_once '../control/Teacher.php';
    require_once '../data/db_config.php';


    $course = new VideoContent($db, $_GET['id']);

    $course_id = $course->getCourseId();
    $title = $course->getTitle();
    $description = $course->getDescription();
    $category = $course->getCategory();
    $banner_image = $course->getBannerImage();
    $course_created_at = $course->getCreatedAt();
    $content = $course->getContent();
    $content_type = $course->getContentType();
    $teacher_id = $course->getTeacherId();
    $tags = $course->getTags();
    $category_name = $course->getCategory_name();

    $courseMaker = new Teacher($db, $teacher_id);
    $teacher_first_name = $courseMaker->getFirstName();
    $teacher_last_name = $courseMaker->getLastName();
    $teacher_profile_image = $courseMaker->getProfileImage();


} else {
    throw new Exception("Course id not provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .animated-header {
            animation: fadeIn 1s ease-in-out;
        }

        .hover-glow:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
        }

        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-gray-50">
<?php include '../includes/navbar.php'; ?>  
    <div class="min-h-screen">
        <!-- Course Header -->
        <div class="relative h-[400px] bg-gradient-to-r from-blue-600 to-purple-600">
            <?php if ($banner_image): ?>
                <img src="<?php echo htmlspecialchars($banner_image); ?>" alt="Course Banner" class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-60">
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent"></div>
            <div class="relative container mx-auto px-4 pt-20 animated-header">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4"><?php echo htmlspecialchars($title); ?></h1>



                <div class="flex items-center space-x-4">
                    <?php if ($teacher_profile_image): ?>
                        <img src="<?php echo htmlspecialchars($teacher_profile_image); ?>" alt="Teacher Profile" class="w-12 h-12 rounded-full border-2 border-white">
                    <?php endif; ?>
                    <div class="text-white">
                        <p class="text-sm opacity-90">Instructor</p>
                        <p class="font-medium"><?php echo htmlspecialchars($teacher_first_name . ' ' . $teacher_last_name); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-4 -mt-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Video Content -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-glow">
                        <?php if ($content): ?>
                            <div class="video-container">
                                <video id="video-player" controls class="w-full">
                                    <source src="<?php echo htmlspecialchars($content); ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        <?php else: ?>
                            <div class="p-6 text-gray-500">Video content is not available.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Video Player and Progress -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mt-4 hover-glow">

                        <div class="flex items-center justify-between mt-4">
                            <h3 class="text-lg font-semibold">Course Progress</h3>
                            <span id="completion-status" class="text-blue-600 font-medium">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%;"></div>
                        </div>
                    </div>

                    <script>
                        
                        const videoPlayer = document.getElementById('video-player');
                        const completionStatusElement = document.getElementById('completion-status');
                        const progressBarElement = document.getElementById('progress-bar');

                        
                        videoPlayer.addEventListener('timeupdate', () => {
                            
                            const completionStatus = (videoPlayer.currentTime / videoPlayer.duration) * 100;
                            completionStatusElement.textContent = `${Math.round(completionStatus)}%`;
                            progressBarElement.style.width = `${completionStatus}%`;

                        });
                    </script>

                    <!-- Reviews Section -->
                    <!-- <div class="mt-8 bg-white rounded-xl shadow-lg p-6 hover-glow">
                        <h2 class="text-2xl font-bold mb-6">Reviews</h2>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
                            <div class="mb-8 border-b border-gray-200 pb-8">
                                <h3 class="text-lg font-semibold mb-4">Write a Review</h3>
                                <form action="submit_review.php" method="POST" class="space-y-4">
                                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

                                    <div class="flex items-center space-x-2 text-2xl">
                                        <?php for($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" class="hidden">
                                            <label for="star<?php echo $i; ?>" class="cursor-pointer text-gray-300 hover:text-yellow-400">
                                                <i class="fas fa-star"></i>
                                            </label>
                                        <?php endfor; ?>
                                    </div>

                                    <textarea name="comment" placeholder="Write your review here..." required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Submit Review
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <?php if ($review_id): ?>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex items-center space-x-2 text-yellow-400 mb-3">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="fas fa-star"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <p class="text-gray-700 mb-3"><?php echo htmlspecialchars($comment); ?></p>
                                <p class="text-sm text-gray-500">Reviewed on <?php echo date('F j, Y', strtotime($reviewed_at)); ?></p>
                            </div>
                        <?php endif; ?>
                    </div> -->
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4 hover-glow">
                        <div class="space-y-6">
                            <div class="border-b border-gray-200 pb-6">
                                <h3 class="text-lg font-semibold mb-4">Course Details</h3>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm text-gray-500">Category</p>
                                        <p class="font-medium"><?php echo htmlspecialchars($category_name); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Tags</p>
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <?php if(!empty($tags)): ?>
                                                <?php foreach($tags as $tag): ?>
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                                        <?php echo htmlspecialchars($tag->name); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-gray-500 text-sm">No tags available</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Created</p>
                                        <p class="font-medium"><?php echo date('F j, Y', strtotime($course_created_at)); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Video Duration</p>
                                        <p class="font-medium">
                                            <i class="fas fa-clock mr-2"></i>
                                            <span id="video-duration">Loading...</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <?php if ($description): ?>
                                <div>
                                    <h3 class="text-lg font-semibold mb-3">Description</h3>
                                    <p class="text-gray-600 leading-relaxed"><?php echo htmlspecialchars($description); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Calculate and display video duration
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.querySelector('video');
            if (video) {
                video.addEventListener('loadedmetadata', function() {
                    const duration = Math.round(video.duration);
                    const minutes = Math.floor(duration / 60);
                    const seconds = duration % 60;
                    document.getElementById('video-duration').textContent = 
                        `${minutes}:${seconds.toString().padStart(2, '0')}`;
                });
            }
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>