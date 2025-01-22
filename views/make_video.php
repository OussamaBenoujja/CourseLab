<?php
session_start();
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    


    if (isset($_POST['save_video'])) {
        $videoContent = new VideoContent($db);
        
        $title = $_POST['title'];
        $description = $_POST['description'];
        $category_id = $_POST['category'];
        $tags = isset($_POST['tags']) ? $_POST['tags'] : [];


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
            
            if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['video']['tmp_name'];
                $filename = basename($_FILES['video']['name']);
                $target_path = '../up/videos/' . $filename;

                if (move_uploaded_file($tmp_name, $target_path)) {
                    $videoContent->setTitle($title);
                    $videoContent->setDescription($description);
                    $videoContent->setCategory($category_id);
                    $videoContent->setContent($target_path);
                    $videoContent->setTeacherId($_SESSION['user_id']);
                    $videoContent->setBannerImage($banner_path); 
                    $videoId = $videoContent->saveContent();

                    if (!empty($tags)) {
                        foreach ($tags as $tagId) {
                            $tag = new Tag($db, $tagId);
                            $videoContent->addTag($tag);
                        }
                    }

                    $success_message = "Video uploaded and saved successfully.";
                } else {
                    $error_message = "Failed to save the uploaded video.";
                }
            } else {
                $error_message = "Please upload a valid video file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Chapter - Video</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ffmpeg.js/0.10.1/ffmpeg.min.js"></script>
</head>
<body class="bg-gray-100 p-6">
<?php include '../includes/navbar.php'; ?>  
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6">Upload and Edit Video</h1>

        <?php if (isset($success_message)) echo "<p class='text-green-500 mb-4'>$success_message</p>"; ?>
        <?php if (isset($error_message)) echo "<p class='text-red-500 mb-4'>$error_message</p>"; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">


            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Banner</label>
                <input type="file" id="title" name="banner_image" placeholder="upload banner image" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" id="title" name="title" placeholder="Enter video title" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" placeholder="Enter video description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category" id="category" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['category_id']) ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                <select name="tags[]" id="tags" multiple class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 min-h-[120px]">
                    <?php foreach ($tags as $tag): ?>
                        <option value="<?= htmlspecialchars($tag['tag_id']) ?>"><?= htmlspecialchars($tag['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="video" class="block text-sm font-medium text-gray-700">Upload Video</label>
                <input type="file" name="video" id="video" accept="video/*" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>

            <div id="video-editor" class="hidden mt-4">
                <video id="uploaded-video" controls class="w-full mb-4"></video>
                <div class="flex space-x-4">
                    <div>
                        <label for="start-time" class="block text-sm font-medium text-gray-700">Start Time (seconds)</label>
                        <input type="number" id="start-time" step="0.1" min="0" placeholder="0" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="end-time" class="block text-sm font-medium text-gray-700">End Time (seconds)</label>
                        <input type="number" id="end-time" step="0.1" placeholder="10" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <button type="button" id="trim-video" class="mt-4 px-4 py-2 bg-indigo-500 text-white rounded-md shadow-sm hover:bg-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Trim Video</button>
                <p id="trim-status" class="mt-2 text-sm text-gray-500"></p>
            </div>

            <button type="submit" name="save_video" class="w-full px-4 py-2 bg-indigo-500 text-white rounded-md shadow-sm hover:bg-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Save</button>
        </form>
    </div>

    <script>
        const videoInput = document.getElementById('video');
        const videoElement = document.getElementById('uploaded-video');
        const videoEditor = document.getElementById('video-editor');
        const trimButton = document.getElementById('trim-video');
        const trimStatus = document.getElementById('trim-status');

        let ffmpeg;

        videoInput.addEventListener('change', async (event) => {
            const file = event.target.files[0];
            if (file) {
                const url = URL.createObjectURL(file);
                videoElement.src = url;
                videoEditor.classList.remove('hidden');

                if (!ffmpeg) {
                    trimStatus.textContent = "Loading FFmpeg...";
                    ffmpeg = await FFmpeg.createFFmpeg({ log: true });
                    await ffmpeg.load();
                    trimStatus.textContent = "FFmpeg loaded.";
                }
            }
        });

        trimButton.addEventListener('click', async () => {
            const startTime = parseFloat(document.getElementById('start-time').value);
            const endTime = parseFloat(document.getElementById('end-time').value);

            if (isNaN(startTime) || isNaN(endTime) || startTime >= endTime) {
                trimStatus.textContent = "Invalid start or end time.";
                return;
            }
            
        });
    </script>
</body>
</html>