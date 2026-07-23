<?php
require_once 'auth.php';
checkAuth();

// Fetch Testimonials
$stmt = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order ASC");
$testimonials = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_testimonial'])) {
    $id = $_POST['testimonial_id'];
    $name = $_POST['name'];
    $pos = $_POST['position'];
    $content = $_POST['content'];
    
    // Default to existing image
    $image = $_POST['existing_image'] ?? '';

    // Handle File Upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $filename = $_FILES['image_file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Ensure directory exists
            if (!is_dir('../uploads/testimonials')) {
                mkdir('../uploads/testimonials', 0755, true);
            }
            
            $new_name = 'testimonial_' . $id . '_' . time() . '.' . $ext;
            $upload_path = '../uploads/testimonials/' . $new_name;
            $db_path = 'uploads/testimonials/' . $new_name;
            
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_path)) {
                // Delete old local image if exists
                if (!empty($image) && strpos($image, 'http') !== 0 && file_exists('../' . $image)) {
                    @unlink('../' . $image);
                }
                $image = $db_path;
            }
        }
    }

    $updateStmt = $pdo->prepare("UPDATE testimonials SET name = ?, position = ?, content = ?, image = ? WHERE id = ?");
    $updateStmt->execute([$name, $pos, $content, $image, $id]);
    
    header('Location: edit_testimonials.php?msg=updated');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Testimonials - Phoenix Precision Products Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-custom.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <?php 
            $page_title = "Manage Client Feedback";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">Testimonial updated successfully!</div>
                <?php endif; ?>

                <div class="row g-4">
                    <?php foreach ($testimonials as $testimonial): ?>
                    <div class="col-md-6">
                        <div class="card p-4 h-100">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="testimonial_id" value="<?php echo $testimonial['id']; ?>">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Client Name</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($testimonial['name']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Position / Company</label>
                                    <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($testimonial['position']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Client Photo</label>
                                    <?php if (!empty($testimonial['image'])): ?>
                                        <div class="mb-2">
                                            <?php 
                                            $img_src = (strpos($testimonial['image'], 'http') === 0) ? $testimonial['image'] : '../' . $testimonial['image'];
                                            ?>
                                            <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Current Photo" style="max-height: 80px; object-fit: cover;" class="img-thumbnail d-block">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="image_file" class="form-control" accept="image/*">
                                    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($testimonial['image']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Feedback Content</label>
                                    <textarea name="content" class="form-control" rows="4"><?php echo htmlspecialchars($testimonial['content']); ?></textarea>
                                </div>
                                <button type="submit" name="save_testimonial" class="btn btn-primary w-100 mt-2">Update Testimonial</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="js/admin-custom.js"></script>
</body>
</html>
