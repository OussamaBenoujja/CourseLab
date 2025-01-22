<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: err_pages/404.php");
    exit();
}


require_once '../data/db_config.php';
require_once '../control/Course.php';
require_once '../control/textcontent.php';
require_once '../control/Category.php';
require_once '../control/Tag.php';


function debug_log($message, $data = null) {
    error_log($message . ($data ? ': ' . print_r($data, true) : ''));
}



if (!isset($_GET['id'])) {
    die("Course ID is required.");
}
$course_id = $_GET['id'];


try {
   
    $course = new TextContent($db, $course_id);
    $course->loadTags();

} catch (Exception $e) {
    die($e->getMessage());
}


$category = new Category($db);
$categories = $category->getAllCategories();

$tag = new Tag($db);
$tags = $tag->getAllTags();


$contentPath = $course->getContent();
$editorContent = '';

debug_log("Content Path", $contentPath);

if (!empty($contentPath)) {
    
    $content_dir = dirname($contentPath);
    if (!file_exists($content_dir)) {
        if (!mkdir($content_dir, 0777, true)) {
            debug_log("Failed to create directory", $content_dir);
        }
    }

    if (file_exists($contentPath)) {
        $editorContent = file_get_contents($contentPath);
        if ($editorContent === false) {
            debug_log("Failed to read content from file", $contentPath);
            $editorContent = '';
        }
    } else {
        debug_log("Creating new content file", $contentPath);
        file_put_contents($contentPath, '');
    }
}

debug_log("Editor Content Length", strlen($editorContent));


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $db->beginTransaction();

        
        $courseName = trim($_POST['course-name']);
        $courseDescription = trim($_POST['course-description']);
        $category_id = $_POST['category'];
        $selectedTags = isset($_POST['tags']) ? $_POST['tags'] : [];
        $content = $_POST['editor'];
        $banner_path = $course->getBannerImage();

        debug_log("Received POST content length", strlen($content));


        $content = preg_replace('/<figure[^>]*class="media"[^>]*><oembed[^>]*url="https:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})"[^>]*><\/oembed><\/figure>/i', '<iframe width="560" height="315" src="https://www.youtube.com/embed/$1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $content);


       
        if (empty($courseName) || empty($courseDescription)) {
            throw new Exception('Course name and description are required.');
        }

        
        if (isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $file_ext = strtolower(pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed)) {
                throw new Exception('Invalid image format. Allowed formats: ' . implode(', ', $allowed));
            }

            $upload_dir = "../up/";
            if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
                throw new Exception('Upload directory is not writable.');
            }

            $banner_filename = time() . '_banner_' . preg_replace("/[^a-zA-Z0-9]/", "_", $courseName) . '.' . $file_ext;
            $new_banner_path = $upload_dir . $banner_filename;

            if (!move_uploaded_file($_FILES['banner']['tmp_name'], $new_banner_path)) {
                throw new Exception('Failed to upload banner image.');
            }

            if (!empty($banner_path) && file_exists($banner_path)) {
                unlink($banner_path);
            }
            $banner_path = $new_banner_path;
        }

        
        $content_dir = dirname($contentPath);
        if (!file_exists($content_dir)) {
            if (!mkdir($content_dir, 0777, true)) {
                throw new Exception("Failed to create content directory.");
            }
        }

        $fullContent = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>new retarded test to check if this works </title>
    <link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>
</head>
<body class='bg-gray-50 p-8'>
    <div class='max-w-4xl mx-auto bg-white p-6 rounded-lg shadow'>\n" . $content . "\n</body></html>";
        if (file_put_contents($contentPath, $fullContent) === false) {
            throw new Exception("Failed to save course content.");
        }

   
        $course->setTitle($courseName);
        $course->setDescription($courseDescription);
        $course->setCategory($category_id);
        $course->setBannerImage($banner_path);

        
        $course->saveTags($selectedTags);

        
        $course->saveCourse();

        $db->commit();
        header("Location: teacher_dashboard.php");
        exit();
    } catch (Exception $e) {
        $db->rollBack();
        $error_message = $e->getMessage();
        debug_log("Error updating course", $error_message);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Course</title>
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.1/decoupled-document/ckeditor.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], 
        textarea, 
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .document-editor {
            border: 1px solid #DDD;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .document-editor__toolbar {
            padding: 10px;
            border-bottom: 1px solid #DDD;
            background: #f5f5f5;
        }

        .document-editor__editable-container {
            padding: 20px;
            min-height: 300px;
            background: white;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #ffebee;
            border: 1px solid #ffcdd2;
            border-radius: 4px;
        }

        .preview-image {
            max-width: 200px;
            margin: 10px 0;
        }

        #editor {
            min-height: 300px;
        }
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>  
    <h1>Update Course</h1>

    <?php if (isset($error_message)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="course-name">Course Name:</label>
            <input type="text" id="course-name" name="course-name" 
                   value="<?php echo htmlspecialchars($course->getTitle()); ?>" required>
        </div>

        <div class="form-group">
            <label for="banner">Banner Image:</label>
            <?php if ($course->getBannerImage()): ?>
                <img src="<?php echo htmlspecialchars($course->getBannerImage()); ?>" 
                     alt="Current Banner" class="preview-image">
            <?php endif; ?>
            <input type="file" id="banner" name="banner" accept="image/*">
        </div>

        <div class="form-group">
            <label for="course-description">Description:</label>
            <textarea id="course-description" name="course-description" rows="4" required><?php echo htmlspecialchars($course->getDescription()); ?></textarea>
        </div>

        <div class="form-group">
            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['category_id']); ?>"
                            <?php echo ($course->getCategory() == $cat['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="tags">Tags:</label>
            <select id="tags" name="tags[]" multiple>
                <?php foreach ($tags as $tag): ?>
                    <option value="<?php echo htmlspecialchars($tag['tag_id']); ?>"
                            <?php echo in_array($tag['tag_id'], array_column($course->getTags(), 'tag_id')) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($tag['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="editor">Content:</label>
            <div class="document-editor">
                <div class="document-editor__toolbar"></div>
                <div class="document-editor__editable-container">
                    <div id="editor"><?php echo htmlspecialchars_decode($editorContent); ?></div>
                </div>
            </div>
        </div>

        <button type="submit">Update Course</button>
    </form>

    <script>
        DecoupledEditor
            .create(document.querySelector('#editor'), {
                toolbar: {
                    items: [
                        'undo', 'redo',
                        '|', 'heading',
                        '|', 'bold', 'italic',
                        '|', 'alignment',
                        '|', 'bulletedList', 'numberedList', 'outdent', 'indent',
                        '|', 'imageUpload', 'mediaEmbed', 'codeBlock'
                    ]
                },
                removePlugins: ['RestrictedEditingMode', 'StandardEditingMode']
            })
            .then(editor => {
                
                const toolbarContainer = document.querySelector('.document-editor__toolbar');
                toolbarContainer.appendChild(editor.ui.view.toolbar.element);

                
                console.log('Initial content:', editor.getData());

                
                document.querySelector('form').addEventListener('submit', function(e) {
                    const editorData = editor.getData();
                    console.log('Submitting content:', editorData);
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'editor';
                    hiddenInput.value = editorData;
                    this.appendChild(hiddenInput);
                });

                
                console.log('Editor initialized successfully');
            })
            .catch(error => {
                console.error('Editor initialization error:', error);
                const editorElement = document.querySelector('#editor');
                editorElement.innerHTML = '<p style="color: red;">Error loading editor. Please refresh the page or contact support if the problem persists.</p>';
            });
    </script>
</body>
</html>