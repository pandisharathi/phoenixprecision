<?php
require_once 'auth.php';
checkAuth();

// Fetch About Content
$stmt = $pdo->query("SELECT * FROM about_content WHERE id = 1");
$about = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_about'])) {
    $title = $_POST['title'];
    $lead = $_POST['lead_text'];
    $main = $_POST['main_text'];
    
    // Default to existing image
    $image = $_POST['existing_image'] ?? '';

    // Handle File Upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $filename = $_FILES['image_file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Ensure directory exists
            if (!is_dir('../uploads/about')) {
                mkdir('../uploads/about', 0755, true);
            }
            
            $new_name = 'about_' . time() . '.' . $ext;
            $upload_path = '../uploads/about/' . $new_name;
            $db_path = 'uploads/about/' . $new_name;
            
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_path)) {
                // Delete old local image if exists
                if (!empty($image) && strpos($image, 'http') !== 0 && file_exists('../' . $image)) {
                    @unlink('../' . $image);
                }
                $image = $db_path;
            }
        }
    }

    $updateStmt = $pdo->prepare("UPDATE about_content SET title = ?, lead_text = ?, main_text = ?, image = ? WHERE id = 1");
    $updateStmt->execute([$title, $lead, $main, $image]);
    
    header('Location: edit_about.php?msg=updated');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit About - Phoenix Precision Products Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-custom.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php 
            $page_title = "Manage About Section";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">About content updated successfully!</div>
                <?php endif; ?>

                <div class="card p-4 mx-auto" style="max-width: 800px;">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Section Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($about['title']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">About Section Image</label>
                            <?php if (!empty($about['image'])): ?>
                                <div class="mb-2">
                                    <?php 
                                    $img_src = (strpos($about['image'], 'http') === 0) ? $about['image'] : '../' . $about['image'];
                                    ?>
                                    <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Current Image" style="max-height: 120px; object-fit: cover;" class="img-thumbnail d-block">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($about['image']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Lead Text (Bold highlight)</label>
                            <textarea name="lead_text" class="form-control" rows="2"><?php echo htmlspecialchars($about['lead_text']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Main Content Text</label>
                            <textarea name="main_text" class="form-control" rows="5"><?php echo htmlspecialchars($about['main_text']); ?></textarea>
                        </div>
                        <button type="submit" name="save_about" class="btn btn-primary px-5 mt-3">Save Changes</button>
                    </form>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="js/admin-custom.js"></script>
</body>
</html>
