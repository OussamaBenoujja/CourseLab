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


$category = new Category($db);
$categories = $category->getAllCategories();

$tag = new Tag($db);
$tags = $tag->getAllTags();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("Processing form submission");
    
    
    $courseName = $_POST['course-name'];
    $courseDescription = $_POST['course-description'];
    $category_id = $_POST['category'];
    $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
    $content = $_POST['editor'];
    $content = preg_replace('/<figure[^>]*class="media"[^>]*><oembed[^>]*url="https:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})"[^>]*><\/oembed><\/figure>/i', '<iframe width="560" height="315" src="https://www.youtube.com/embed/$1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $content);
    
    if (empty($courseName) || empty($courseDescription) || empty($category_id) || empty($content)) {
        die('All fields are required.');
    }

    try {
        $db->beginTransaction();

        
        $banner_path = '';
        if (isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['banner']['name'];
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed)) {
                throw new Exception('Invalid image format. Allowed formats: ' . implode(', ', $allowed));
            }

            
            $upload_dir = "../up/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            
            $banner_filename = time() . '_banner_' . preg_replace("/[^a-zA-Z0-9]/", "_", $courseName) . '.' . $file_ext;
            $banner_path = $upload_dir . $banner_filename;

            if (!move_uploaded_file($_FILES['banner']['tmp_name'], $banner_path)) {
                throw new Exception('Failed to upload banner image.');
            }
        }

        
        $course_dir = "../up/text_course/";
        if (!file_exists($course_dir)) {
            mkdir($course_dir, 0777, true);
        }

        
        $timestamp = time();
        $sanitized_name = preg_replace("/[^a-zA-Z0-9]/", "_", $courseName);
        $filename = $timestamp . "_" . $sanitized_name . ".html";
        $filepath = $course_dir . $filename;

        
        $html_content = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>" . htmlspecialchars($courseName) . "</title>
    <link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>
</head>
<body class='bg-gray-50 p-8'>
    <div class='max-w-4xl mx-auto bg-white p-6 rounded-lg shadow'>
        " . $content . "
    </div>
</body>
</html>";

        // Save the HTML file
        if (file_put_contents($filepath, $html_content) === false) {
            throw new Exception("Failed to save course content file.");
        }

        
        $textCourse = new TextContent($db);
        $textCourse->setTitle($courseName);
        $textCourse->setDescription($courseDescription);
        $textCourse->setTeacherId($_SESSION['user_id']);
        $textCourse->setCategory($category_id);
        $textCourse->setContent($filepath); 
        $textCourse->setBannerImage($banner_path); 

        
        $courseId = $textCourse->saveContent();
        $textCourse->id = $courseId;

        
        if (!empty($tags)) {
            foreach ($tags as $tagId) {
                $tag = new Tag($db, $tagId);
                $textCourse->addTag($tag);
            }
        }

        $db->commit();
        header("Location: teacher_dashboard.php");
        exit();
    } catch (Exception $e) {

        $db->rollBack();
        if (isset($filepath) && file_exists($filepath)) {
            unlink($filepath);
        }
        if (isset($banner_path) && file_exists($banner_path)) {
            unlink($banner_path);
        }
        error_log("Error saving course: " . $e->getMessage());
        die("Error saving course: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Chapter</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.1/decoupled-document/ckeditor.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<?php include '../includes/navbar.php'; ?>  
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Write New Chapter</h1>

        <form id="course-form" method="post" action="" enctype="multipart/form-data" class="space-y-6">
            <!-- Course Name -->
            <div class="space-y-2">
                <label for="course-name" class="block text-sm font-medium text-gray-700">Course Name</label>
                <input type="text" 
                       id="course-name" 
                       name="course-name" 
                       placeholder="Enter course name" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       required>
            </div>

            <!-- Banner Image -->
            <div class="space-y-2">
                <label for="banner" class="block text-sm font-medium text-gray-700">Banner Image</label>
                <input type="file" 
                       id="banner" 
                       name="banner" 
                       accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-sm text-gray-500">Supported formats: JPG, JPEG, PNG, GIF</p>
            </div>

            <!-- Course Description -->
            <div class="space-y-2">
                <label for="course-description" class="block text-sm font-medium text-gray-700">Course Description</label>
                <textarea name="course-description" 
                          id="course-description" 
                          placeholder="Enter course description" 
                          rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          required></textarea>
            </div>

            <!-- Category and Tags Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div class="space-y-2">
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" 
                            id="category" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['category_id']) ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tags -->
                <div class="space-y-2">
                    <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                    <select name="tags[]" 
                            id="tags" 
                            multiple 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 min-h-[120px]">
                        <?php foreach ($tags as $tag): ?>
                            <option value="<?= htmlspecialchars($tag['tag_id']) ?>"><?= htmlspecialchars($tag['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- CKEditor Container -->
            <div class="border border-gray-300 rounded-md shadow-sm overflow-hidden">
                <div id="toolbar-container" class="bg-gray-50 border-b border-gray-300 p-2"></div>
                <div id="editor" class="bg-white p-4 min-h-[400px]"></div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        id="save-btn" 
                        class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Save Course
                </button>
            </div>
        </form>
    </div>

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
                }
            })
            .then(editor => {
                const toolbarContainer = document.querySelector('#toolbar-container');
                toolbarContainer.appendChild(editor.ui.view.toolbar.element);
                
                document.querySelector('#course-form').addEventListener('submit', function(e) {
                    const editorContent = editor.getData();
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'editor';
                    hiddenInput.value = editorContent;
                    this.appendChild(hiddenInput);
                });
            })
            .catch(error => {
                console.error(error);
            });
    </script>
</body>
</html>