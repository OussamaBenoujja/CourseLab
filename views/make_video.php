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
$tag = new Tag($db);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_video'])) {
        $videoContent = new VideoContent($db);
        
        // Validate and save the uploaded video
        if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['video']['tmp_name'];
            $filename = basename($_FILES['video']['name']);
            $target_path = '../uploads/videos/' . $filename;

            if (move_uploaded_file($tmp_name, $target_path)) {
                $videoContent->setContent($target_path);
                $videoContent->saveContent();
                $success_message = "Video uploaded and saved successfully.";
            } else {
                $error_message = "Failed to save the uploaded video.";
            }
        } else {
            $error_message = "Please upload a valid video file.";
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ffmpeg.js/0.10.1/ffmpeg.min.js"></script>
</head>
<body>
    <h1>Upload and Edit Video</h1>

    <?php if (isset($success_message)) echo "<p style='color:green;'>$success_message</p>"; ?>
    <?php if (isset($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="video">Upload Video:</label>
        <input type="file" name="video" id="video" accept="video/*" required>

        <div id="video-editor" style="display:none;">
            <video id="uploaded-video" controls style="max-width:100%;"></video>
            <label for="start-time">Start Time (seconds):</label>
            <input type="number" id="start-time" step="0.1" min="0" placeholder="0">
            <label for="end-time">End Time (seconds):</label>
            <input type="number" id="end-time" step="0.1" placeholder="10">
            <button type="button" id="trim-video">Trim Video</button>
            <p id="trim-status"></p>
        </div>

        <button type="submit" name="save_video">Save</button>
    </form>

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
                videoEditor.style.display = 'block';

                // Load FFmpeg
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

            trimStatus.textContent = "Trimming video...";

            const file = videoInput.files[0];
            const fileName = file.name;

            ffmpeg.FS('writeFile', fileName, await fetchFile(file));

            await ffmpeg.run('-i', fileName, '-ss', startTime.toString(), '-to', endTime.toString(), '-c', 'copy', 'output.mp4');

            const trimmedData = ffmpeg.FS('readFile', 'output.mp4');
            const trimmedBlob = new Blob([trimmedData.buffer], { type: 'video/mp4' });
            const trimmedUrl = URL.createObjectURL(trimmedBlob);

            videoElement.src = trimmedUrl;
            trimStatus.textContent = "Video trimmed successfully. You can now save it.";
        });
    </script>
</body>
</html>