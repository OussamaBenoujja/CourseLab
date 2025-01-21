<?php

session_start();

require_once '../control/User.php';
require_once '../data/db_config.php';
require_once '../control/Course.php';



if(isset($_GET['enrolled'])) {
    echo "
    <script>alert('You have successfully enrolled in the course.')</script>
    ";
    
}

$filter = $_GET['filter'] ?? null;
$search = $_GET['search'] ?? null;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 8; 


$courses = Course::getFilteredCourses($db, $filter, $search, $page, $items_per_page);


$total_courses = Course::getTotalCourses($db, $filter, $search);
$total_pages = ceil($total_courses / $items_per_page);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Home</title>
</head>
<body class="bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">

    
    <header class="bg-blue-600 text-white py-4 shadow-md">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold">Welcome to CourseLab</h1>
        </div>
    </header>

    
    <div class="container mx-auto px-4 py-6">
        <form method="GET" class="flex flex-wrap gap-4 items-center">
            <select name="filter" class="border-gray-300 rounded-md p-2">
                <option value="">All Courses</option>
                <option value="popular" <?php echo $filter == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                <option value="recent" <?php echo $filter == 'recent' ? 'selected' : ''; ?>>Recently Created</option>
            </select>
            <input
                type="text"
                name="search"
                placeholder="Search courses..."
                value="<?php echo htmlspecialchars($search); ?>"
                class="border-gray-300 rounded-md p-2 flex-grow" />
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                Apply
            </button>
        </form>
    </div>

  
    <main class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-semibold mb-6">Available Courses</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php if (empty($courses)): ?>
                <p class="col-span-full text-center text-gray-600 dark:text-gray-400">No courses found.</p>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden dark:bg-gray-800">
                        <a href="#!">
                            <img
                                class="w-full h-48 object-cover"
                                src="<?php echo $course['banner_image']; ?>"
                                alt="<?php echo htmlspecialchars($course['title']); ?>" />
                        </a>
                        <div class="p-4">
                            <h3 class="text-lg font-bold mb-2"><?php echo $course['title']; ?></h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                <?php echo $course['description']; ?>
                            </p>
                            <a href="enroll.php?course_id=<?php echo $course['course_id']; ?>">
                            <button
                                type="button"
                                
                                class="w-full text-center py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-semibold transition ease-in-out duration-150">
                                Enroll
                            </button>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Pagination -->
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-center space-x-2">
            <?php if ($total_pages > 1): ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>"
                       class="px-4 py-2 border rounded-md <?php echo $i == $page ? 'bg-blue-600 text-white' : 'text-gray-800'; ?>">
                       <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
