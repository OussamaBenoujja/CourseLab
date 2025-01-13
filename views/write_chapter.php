<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TinyMCE Editor with Save and Load</title>
    <!-- TinyMCE CDN -->
    <script src="https://cdn.tiny.cloud/1/nj3p6ek26ggoqe62u7lm2s9mul8cky6x8gezc26h49hbxo7k/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
    <!-- TinyMCE Textarea -->
    <textarea id="editor"></textarea>

    <!-- Save and Load Buttons -->
    <button id="save-btn">Save</button>
    <button id="load-btn">Load</button>

    <!-- JavaScript for Save and Load Functionality -->
    <script>
        // Initialize TinyMCE with necessary plugins
        tinymce.init({
            selector: 'textarea#editor',
            plugins: 'image media code export imagetools',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | image media | code',
            imagetools_base64: true, // Embed images as base64
            // Other configurations as needed
        });

        // Save button functionality
        document.getElementById('save-btn').onclick = function() {
            var content = tinymce.activeEditor.getContent();
            var blob = new Blob([content], { type: 'text/html' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'document.html';
            a.click();
            URL.revokeObjectURL(url);
        };

        // Load button functionality
        document.getElementById('load-btn').onclick = function() {
            var input = document.createElement('input');
            input.type = 'file';
            input.accept = 'text/html';
            input.onchange = function(e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        tinymce.activeEditor.setContent(reader.result);
                    };
                    reader.readAsText(file);
                }
            };
            input.click();
        };
    </script>
</body>
</html>