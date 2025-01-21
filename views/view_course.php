<?php


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {

    require_once '../control/Course.php';
    require_once '../control/textcontent.php';
    require_once '../control/User.php';
    require_once '../control/Teacher.php';
    require_once '../data/db_config.php';


    $course = new TextContent($db, $_GET['id']);

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
        /* Custom animations */
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
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Course Header -->
        <div class="relative h-[400px] bg-gradient-to-r from-blue-600 to-purple-600">
            <?php if ($banner_image): ?>
                <img src="<?php echo htmlspecialchars($banner_image); ?>" alt="Course Banner" class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-60">
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent"></div>
            <div class="relative container mx-auto px-4 pt-20 animated-header">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4"> <?php echo htmlspecialchars($title); ?> </h1>

                <!-- Rating Display -->
                <div class="flex items-center space-x-2 text-yellow-400 mb-6">
                    <?php
                    if ($rating) {
                        $full_stars = floor($rating);
                        $half_star = $rating - $full_stars >= 0.5;

                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $full_stars) {
                                echo '<i class="fas fa-star"></i>';
                            } elseif ($half_star && $i == $full_stars + 1) {
                                echo '<i class="fas fa-star-half-alt"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                    ?>
                    <span class="text-white ml-2">(<?php echo number_format($rating, 1); ?>)</span>
                    <?php } ?>
                </div>

                <div class="flex items-center space-x-4">
                    <?php if ($teacher_profile_image): ?>
                        <img src="<?php echo htmlspecialchars($teacher_profile_image); ?>" alt="Teacher Profile" class="w-12 h-12 rounded-full border-2 border-white">
                    <?php endif; ?>
                    <div class="text-white">
                        <p class="text-sm opacity-90">Instructor</p>
                        <p class="font-medium"> <?php echo htmlspecialchars($teacher_first_name . ' ' . $teacher_last_name); ?> </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-4 -mt-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Course Content -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg  hover-glow">
                        <?php if ($content): ?>
                            <iframe src="<?php echo htmlspecialchars($content); ?>" class="w-full h-[1000px] border-0"></iframe>
                        <?php else: ?>
                            <div class="p-6 text-gray-500">Course content is not available.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Reviews Section -->
                    <div class="mt-8 bg-white rounded-xl shadow-lg p-6 hover-glow">
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
                                <p class="text-gray-700 mb-3"> <?php echo htmlspecialchars($comment); ?> </p>
                                <p class="text-sm text-gray-500">Reviewed on <?php echo date('F j, Y', strtotime($reviewed_at)); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
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
                                        <p class="font-medium"> <?php echo htmlspecialchars($category); ?> </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Created</p>
                                        <p class="font-medium"> <?php echo date('F j, Y', strtotime($course_created_at)); ?> </p>
                                    </div>
                                </div>
                            </div>

                            <?php if ($description): ?>
                                <div>
                                    <h3 class="text-lg font-semibold mb-3">Description</h3>
                                    <p class="text-gray-600 leading-relaxed"> <?php echo htmlspecialchars($description); ?> </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
