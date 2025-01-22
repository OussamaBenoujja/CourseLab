<?php
session_start();

include '../includes/auth_check.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: err_pages/404.php");
    exit();
}

require_once '../data/db_config.php';
require_once '../control/Course.php';
require_once '../control/videocontent.php';
require_once '../control/Category.php';
require_once '../control/Tag.php';


$category = new Category($db);
$categories = $category->getAllCategories();

$tag = new Tag($db);
$tags = $tag->getAllTags();


$video_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$video_id) {
    header("Location: err_pages/404.php");
    exit();
}


$videoContent = new VideoContent($db, $video_id);


if ($videoContent->getTeacherId() !== $_SESSION['user_id']) {
    header("Location: err_pages/403.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_video'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $category_id = $_POST['category'];
        $selected_tags = isset($_POST['tags']) ? $_POST['tags'] : [];

        $banner_path = '';
        if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['banner_image']['name'];
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed)) {
                throw new Exception('Invalid image format. Allowed formats: ' . implode(', ', $allowed));
            }

            
            $upload_dir = "../up/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $banner_filename = time() . '_banner_' . preg_replace("/[^a-zA-Z0-9]/", "_", $title) . '.' . $file_ext;
            $banner_path = $upload_dir . $banner_filename;

            if (!move_uploaded_file($_FILES['banner_image']['tmp_name'], $banner_path)) {
                throw new Exception('Failed to upload banner image.');
            }
        }

        if (empty($title) || empty($description) || empty($category_id)) {
            $error_message = "All fields are required.";
        } else {
            $videoContent->setTitle($title);
            $videoContent->setDescription($description);
            $videoContent->setCategory($category_id);
            $videoContent->setBannerImage($banner_path);

            // Handle new video upload if provided
            if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['video']['tmp_name'];
                $filename = basename($_FILES['video']['name']);
                $target_path = '../up/videos/' . $filename;

                if (move_uploaded_file($tmp_name, $target_path)) {
                    // Delete old video file
                    $old_path = $videoContent->getContent();
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                    $videoContent->setContent($target_path);
                } else {
                    $error_message = "Failed to save the new video file.";
                }
            }
            
            foreach ($selected_tags as $tagId) {
                $tag = new Tag($db, $tagId);
                $videoContent->addTag($tag);
            }

            $videoContent->saveContent();
            $success_message = "Video updated successfully.";
        }
    }
}

$current_tag_ids = $tag->getAllTags();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Video Content</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ffmpeg.js/0.10.1/ffmpeg.min.js"></script>
</head>
<body class="bg-gray-100 p-6">
<?php include '../includes/navbar.php'; ?>  
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6">Edit Video Content</h1>

        <?php if (isset($success_message)) echo "<p class='text-green-500 mb-4'>$success_message</p>"; ?>
        <?php if (isset($error_message)) echo "<p class='text-red-500 mb-4'>$error_message</p>"; ?>

        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Current Video</h2>
            <video controls class="w-full mb-4">
                <source src="<?= htmlspecialchars($videoContent->getContent()) ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Banner</label>
                <input type="file" id="title" name="banner_image" placeholder="upload banner image" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($videoContent->getTitle()) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required><?= htmlspecialchars($videoContent->getDescription()) ?></textarea>
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category" id="category" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['category_id']) ?>" <?= $videoContent->getCategory() == $cat['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                <select name="tags[]" id="tags" multiple class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 min-h-[120px]">
                    <?php foreach ($tags as $tag): ?>
                        <option value="<?= htmlspecialchars($tag['tag_id']) ?>" <?= in_array($tag['tag_id'], $current_tag_ids) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tag['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="video" class="block text-sm font-medium text-gray-700">Replace Video (optional)</label>
                <input type="file" name="video" id="video" accept="video/*" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div id="video-editor" class="hidden mt-4">
                <video id="preview-video" controls class="w-full mb-4"></video>
            
            </div>

            <div class="flex space-x-4">
                <button type="submit" name="update_video" class="flex-1 px-4 py-2 bg-indigo-500 text-white rounded-md shadow-sm hover:bg-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Update Video</button>
                <a href="manage_videos.php" class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-md shadow-sm hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-center">Cancel</a>
            </div>
        </form>
    </div>

    <script>

        const videoInput    = document.getElementById('video');
        const videoEditor   = document.getElementById('video-editor');
        const previewVideo  = document.getElementById('preview-video');

        videoInput.addEventListener('change', async (event) => {
            const file = event.target.files[0];
            if (file) {
                const url = URL.createObjectURL(file);
                previewVideo.src = url;
                videoEditor.classList.remove('hidden');

            }
        });

    </script>
</body>
</html>