<?php
require_once 'auth.php';
checkAuth();

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowed)) {
        if (!is_dir('../uploads/projects')) {
            mkdir('../uploads/projects', 0777, true);
        }
        $new_name = 'summernote_' . time() . '_' . rand(100, 999) . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/projects/' . $new_name)) {
            // Return the path relative to the admin/ folder (which is where the editor is)
            echo '../uploads/projects/' . $new_name;
            exit();
        }
    }
}
header('HTTP/1.1 400 Bad Request');
echo "Upload failed";
