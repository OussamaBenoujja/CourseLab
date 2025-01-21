<?php
session_start();
require_once '../data/db_config.php';
require_once '../control/Course.php';
require_once '../control/Teacher.php';
require_once '../control/TextContent.php';
require_once '../control/VideoContent.php';

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../error.php");
    exit();
}

$teacher = new Teacher($db, $_SESSION['user_id']);
$courses = $teacher->getTeacherCourses();
$stats = $teacher->getTeacherStats();
$categories_json = json_encode($stats['courses']['by_category']);
$engagement_json = json_encode($stats['engagement']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - CourseLab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .student-tooltip {
            display: none;
            position: fixed;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            z-index: 50;
            min-width: 200px;
            max-height: 300px;
            overflow-y: auto;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Statistics Section -->
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Course Statistics</h2>
        
        <!-- Primary Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Total Courses</h3>
                <p class="text-3xl font-bold text-indigo-600"><?= $stats['courses']['total_courses'] ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Total Students</h3>
                <p class="text-3xl font-bold text-indigo-600"><?= $stats['students']['total_students'] ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Video Courses</h3>
                <p class="text-3xl font-bold text-indigo-600"><?= $stats['courses']['video_courses'] ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Text Courses</h3>
                <p class="text-3xl font-bold text-indigo-600"><?= $stats['courses']['text_courses'] ?></p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4">Course Categories</h3>
                <canvas id="categoryChart"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4">Student Engagement</h3>
                <canvas id="engagementChart"></canvas>
            </div>
        </div>

        <!-- Secondary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Completed Courses</h3>
                <p class="text-3xl font-bold text-indigo-600"><?= $stats['students']['completed_courses'] ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Recent Enrollments (30d)</h3>
                <p class="text-3xl font-bold text-indigo-600"><?= $stats['students']['new_enrollments_30d'] ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Total Reviews</h3>
                <p class="text-3xl font-bold text-indigo-600"><?= $stats['ratings']['total_reviews'] ?></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-4 mb-8">
            <a href="write_chapter.php" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Create Text Course
            </a>
            <a href="make_video.php" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Create Video Course
            </a>
        </div>

        <!-- Courses Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Your Courses</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Students</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($courses as $course): 
                            $courseObj = ($course['content_type'] === 'text') 
                                ? new TextContent($db, $course['course_id']) 
                                : new VideoContent($db, $course['course_id']);
                            $students = $courseObj->getEnrolledStudents();
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap relative"
                                onmousemove="updateTooltipPosition(event, <?= $course['course_id'] ?>)"
                                onmouseenter="showTooltip(<?= $course['course_id'] ?>)"
                                onmouseleave="hideTooltip(<?= $course['course_id'] ?>)">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($course['title']) ?>
                                </div>
                                <div id="tooltip-<?= $course['course_id'] ?>" class="student-tooltip">
                                    <h4 class="font-semibold mb-2">Enrolled Students:</h4>
                                    <?php if (!empty($students)): ?>
                                        <ul class="space-y-1">
                                            <?php foreach ($students as $student): ?>
                                                <li class="text-sm text-gray-600">
                                                    <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-sm text-gray-500">No students enrolled yet</p>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $course['content_type'] === 'video' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' ?>">
                                    <?= ucfirst($course['content_type']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= count($students) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= number_format($course['rating'] ?? 0, 1) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if($course['content_type'] === 'text'): ?>
                                    <a href="edit_course.php?id=<?= $course['course_id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <a href="view_course.php?id=<?= $course['course_id'] ?>" class="text-green-600 hover:text-green-900 mr-3">View</a>
                                <?php else: ?>
                                    <a href="edit_video.php?id=<?= $course['course_id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <a href="view_video.php?id=<?= $course['course_id'] ?>" class="text-green-600 hover:text-green-900 mr-3">View</a>
                                <?php endif; ?>
                                <button onclick="deleteCourse(<?= $course['course_id'] ?>)" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Course Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Course</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to delete this course? This action cannot be undone.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="deleteConfirm" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete
                    </button>
                    <button onclick="closeDeleteModal()" class="ml-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Section -->
    <script>
        // Category Distribution Chart
        const categoryData = <?= $categories_json ?>;
        const categoryLabels = categoryData.map(item => item.category_name);
        const categoryCounts = categoryData.map(item => item.count);

        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryCounts,
                    backgroundColor: [
                        '#4F46E5', '#10B981', '#EF4444', '#F59E0B',
                        '#8B5CF6', '#3B82F6', '#EC4899'
                    ]
                }]
            }
        });

        // Student Engagement Chart
        const engagementData = <?= $engagement_json ?>;
        const courseTitles = engagementData.map(item => item.title);
        const enrollments = engagementData.map(item => item.enrollment_count);

        new Chart(document.getElementById('engagementChart'), {
            type: 'bar',
            data: {
                labels: courseTitles,
                datasets: [{
                    label: 'Number of Enrollments',
                    data: enrollments,
                    backgroundColor: '#4F46E5'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Tooltip Functions
        function updateTooltipPosition(event, courseId) {
            const tooltip = document.getElementById(`tooltip-${courseId}`);
            if (tooltip) {
                tooltip.style.left = `${event.clientX + 10}px`;
                tooltip.style.top = `${event.clientY + 10}px`;
            }
        }

        function showTooltip(courseId) {
            const tooltip = document.getElementById(`tooltip-${courseId}`);
            if (tooltip) {
                tooltip.style.display = 'block';
            }
        }

        function hideTooltip(courseId) {
            const tooltip = document.getElementById(`tooltip-${courseId}`);
            if (tooltip) {
                tooltip.style.display = 'none';
            }
        }

        // Delete Course Logic
        let courseIdToDelete = null;

        function deleteCourse(courseId) {
            courseIdToDelete = courseId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            courseIdToDelete = null;
        }

        document.getElementById('deleteConfirm').addEventListener('click', function() {
            if (courseIdToDelete) {
                fetch(`delete_course.php?id=${courseIdToDelete}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error deleting course');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting course');
                });
            }
            closeDeleteModal();
        });
    </script>
</body>
</html>