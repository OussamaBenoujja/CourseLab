<?php
require_once '../data/db_config.php';
require_once '../control/Admin.php';
require_once '../control/Category.php';
require_once '../control/Tag.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: auth/login.php');
    exit();
}

$admin = new Admin($db);
$category = new Category($db);
$tag = new Tag($db);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['activate_teacher'])) {
        $admin->validateTeacherAccount($_POST['teacher_id']);
    } elseif (isset($_POST['deactivate_teacher'])) {
        $admin->deactivateTeacherAccount($_POST['teacher_id']);
    }
    
    
    if (isset($_POST['manage_user'])) {
        $admin->manageUserStatus($_POST['user_id'], $_POST['status']);
    }
    
    
    if (isset($_POST['bulk_tags'])) {
        $tags = explode(',', $_POST['tags']);
        $admin->bulkInsertTags($tags);
    }
    
    
    if (isset($_POST['create_category'])) {
        $category->createCategory($_POST['category_name']);
    }
    if (isset($_POST['delete_category'])) {
        $category->deleteCategory($_POST['category_id']);
    }
}


$stats = $admin->getGlobalStatistics();
$pendingTeachers = $admin->getPendingTeachers();
$allUsers = $admin->getAllUsers();
$allCategories = $category->getAllCategories();
$allTags = $tag->getAllTags();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CourseLab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<?php include '../includes/navbar.php'; ?>  
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Total Courses</h3>
                    <p class="text-3xl font-bold text-blue-600"><?= $stats['basic_stats']['total_courses'] ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Total Students</h3>
                    <p class="text-3xl font-bold text-green-600"><?= $stats['basic_stats']['total_students'] ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Total Teachers</h3>
                    <p class="text-3xl font-bold text-purple-600"><?= $stats['basic_stats']['total_teachers'] ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Total Enrollments</h3>
                    <p class="text-3xl font-bold text-orange-600"><?= $stats['basic_stats']['total_enrollments'] ?></p>
                </div>
            </div>

            
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-4">User Management</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($allUsers as $user): ?>
                                <tr>
                                    <td class="px-6 py-4"><?= htmlspecialchars($user['first_name'].' '.$user['last_name']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="px-6 py-4"><?= ucfirst($user['role']) ?></td>
                                    <td class="px-6 py-4"><?= ucfirst($user['status']) ?></td>
                                    <td class="px-6 py-4">
                                        <form method="POST" class="flex gap-2">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <select name="status" class="border rounded px-2 py-1">
                                                <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                                <option value="deleted" <?= $user['status'] === 'deleted' ? 'selected' : '' ?>>Delete</option>
                                            </select>
                                            <button type="submit" name="manage_user" 
                                                    class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-4">Teacher Approvals</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Join Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($pendingTeachers as $teacher): ?>
                                <tr>
                                    <td class="px-6 py-4"><?= htmlspecialchars($teacher['first_name'].' '.$teacher['last_name']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($teacher['email']) ?></td>
                                    <td class="px-6 py-4"><?= date('m/d/Y', strtotime($teacher['created_at'])) ?></td>
                                    <td class="px-6 py-4 space-x-2">
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="teacher_id" value="<?= $teacher['user_id'] ?>">
                                            <button type="submit" name="activate_teacher" 
                                                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                                Approve
                                            </button>
                                            <button type="submit" name="deactivate_teacher" 
                                                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                                Reject
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-4">Category Management</h2>
                    <form method="POST" class="mb-4">
                        <div class="flex gap-4">
                            <input type="text" name="category_name" 
                                   class="flex-1 border rounded px-4 py-2" 
                                   placeholder="Enter new category name" required>
                            <button type="submit" name="create_category" 
                                    class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                                Add Category
                            </button>
                        </div>
                    </form>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <?php foreach ($allCategories as $category): ?>
                        <div class="border rounded p-4 flex justify-between items-center">
                            <span><?= htmlspecialchars($category['category_name']) ?></span>
                            <form method="POST">
                                <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>">
                                <button type="submit" name="delete_category" 
                                        class="text-red-500 hover:text-red-700">
                                    Delete
                                </button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-4">Tag Management</h2>
                    <form method="POST" class="mb-4">
                        <div class="flex gap-4">
                            <input type="text" name="tags" 
                                   class="flex-1 border rounded px-4 py-2" 
                                   placeholder="Enter tags (comma separated)">
                            <button type="submit" name="bulk_tags" 
                                    class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                                Add Tags
                            </button>
                        </div>
                    </form>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($allTags as $tag): ?>
                        <span class="bg-gray-200 px-3 py-1 rounded-full text-sm">
                            <?= htmlspecialchars($tag['name']) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-4">Course Statistics</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Categories -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">By Category</h3>
                            <div class="space-y-4">
                                <?php foreach ($stats['category_stats'] as $category): ?>
                                <div class="flex justify-between items-center border-b pb-2">
                                    <span><?= htmlspecialchars($category['category_name']) ?></span>
                                    <span class="font-medium"><?= $category['count'] ?> courses</span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Most Popular Course</h3>
                            <?php if ($stats['popular_course']): ?>
                            <div class="border rounded p-4">
                                <h4 class="font-medium text-lg"><?= htmlspecialchars($stats['popular_course']['title']) ?></h4>
                                <p class="text-gray-600">Enrollments: <?= $stats['popular_course']['enrollment_count'] ?></p>
                            </div>
                            <?php else: ?>
                            <p class="text-gray-500">No popular courses found</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>