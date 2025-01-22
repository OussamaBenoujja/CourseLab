<?php

session_start();

require_once '../data/db_config.php';
require_once '../control/User.php';
require_once '../control/Admin.php';
require_once '../control/Teacher.php';
require_once '../control/Student.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['role'] === 'teacher') {
    $teacher = new Teacher($db);
    $teacher->setUserId($_SESSION['user_id']);
    $teacher->loadProfile();
    $first_name = $teacher->getFirstName();
    $last_name = $teacher->getLastName();
    $email = $teacher->getEmail();
    $role = $teacher->getRole();
    $profile_image = $teacher->getProfileImage();
    $banner_image = $teacher->getBannerImage();
    $bio = $teacher->getBio();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $teacher->setFirstName($_POST['first_name']);
    $teacher->setLastName($_POST['last_name']);
    $teacher->setBio($_POST['bio']);
    
    
    if ($_FILES['profile_image']['name']) {
        $profile_image_path = "../up/" . basename($_FILES['profile_image']['name']);
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $profile_image_path)) {
            $teacher->setProfileImage($profile_image_path);
        }
    }
    
    
    if ($_FILES['banner_image']['name']) {
        $banner_image_path = "../up/" . basename($_FILES['banner_image']['name']);
        if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $banner_image_path)) {
            $teacher->setBannerImage($banner_image_path);
        }
    }

   
    try {
        if ($teacher->storeProfile()) {
            header('Location: profile.php');
            exit();
        } else {
            throw new Exception("Error updating profile");
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<?php include '../includes/navbar.php'; ?>  
    <div class="container mx-auto mt-12">
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <!-- Profile Section -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="relative">
                <!-- Banner Image -->
                <img src="<?php echo htmlspecialchars($banner_image); ?>" alt="Banner" class="w-full h-60 object-cover">

                <!-- Edit Banner Button (modal trigger) -->
                <button class="absolute top-4 right-4 bg-white text-black rounded-full p-2 shadow-lg" onclick="openBannerModal()">
                    <i class="fas fa-edit"></i> Edit Banner
                </button>
            </div>

            <div class="flex items-center p-4">
                <!-- Profile Image -->
                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile" class="w-24 h-24 rounded-full border-4 border-white shadow-lg">

                <div class="ml-4">
                    <h1 class="text-2xl font-semibold"><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h1>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($email); ?></p>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($role); ?></p>
                    <p class="mt-2"><?php echo htmlspecialchars($bio); ?></p>
                </div>

                <!-- Edit Profile Button (modal trigger) -->
                <button class="ml-auto bg-blue-500 text-white py-2 px-4 rounded-lg" onclick="openProfileModal()">
                    Edit Profile
                </button>
            </div>
        </div>

        <!-- Modal to Edit Profile -->
        <div id="profileModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center items-center">
            <div class="bg-white p-8 rounded-lg w-1/3">
                <h2 class="text-2xl font-semibold mb-4">Edit Profile</h2>

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" class="w-full p-2 border border-gray-300 rounded mt-1">
                    </div>
                    <div class="mb-4">
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" class="w-full p-2 border border-gray-300 rounded mt-1">
                    </div>
                    <div class="mb-4">
                        <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                        <textarea id="bio" name="bio" class="w-full p-2 border border-gray-300 rounded mt-1"><?php echo htmlspecialchars($bio); ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="profile_image" class="block text-sm font-medium text-gray-700">Profile Image</label>
                        <input type="file" id="profile_image" name="profile_image" class="w-full p-2 border border-gray-300 rounded mt-1">
                    </div>
                    <div class="mb-4">
                        <label for="banner_image" class="block text-sm font-medium text-gray-700">Banner Image</label>
                        <input type="file" id="banner_image" name="banner_image" class="w-full p-2 border border-gray-300 rounded mt-1">
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="closeProfileModal()" class="mr-4 text-gray-500">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function openProfileModal() {
            document.getElementById('profileModal').classList.remove('hidden');
            document.getElementById('profileModal').classList.add('flex');
        }
        
        function closeProfileModal() {
            document.getElementById('profileModal').classList.remove('flex');
            document.getElementById('profileModal').classList.add('hidden');
        }
    </script>

</body>
</html>