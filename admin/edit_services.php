<?php
require_once 'auth.php';
checkAuth();

// Fetch Services
$stmt = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC");
$services = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_service'])) {
    $id = $_POST['service_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    
    // Default to existing image
    $image = $_POST['existing_image'] ?? '';

    // Handle File Upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $filename = $_FILES['image_file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Ensure directory exists
            if (!is_dir('../uploads/services')) {
                mkdir('../uploads/services', 0755, true);
            }
            
            $new_name = 'service_' . $id . '_' . time() . '.' . $ext;
            $upload_path = '../uploads/services/' . $new_name;
            $db_path = 'uploads/services/' . $new_name;
            
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_path)) {
                // Delete old local image if exists
                if (!empty($image) && strpos($image, 'http') !== 0 && file_exists('../' . $image)) {
                    @unlink('../' . $image);
                }
                $image = $db_path;
            }
        }
    }

    $updateStmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, image = ? WHERE id = ?");
    $updateStmt->execute([$title, $desc, $image, $id]);
    
    header('Location: edit_services.php?msg=updated');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Services - Phoenix Precision Products Admin</title>
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
            $page_title = "Manage Core Expertise";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">Service updated successfully!</div>
                <?php endif; ?>

                <div class="row g-4">
                    <?php foreach ($services as $service): ?>
                    <div class="col-md-4">
                        <div class="card p-4 h-100">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Service Title</label>
                                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($service['title']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Service Image</label>
                                    <?php if (!empty($service['image'])): ?>
                                        <div class="mb-2">
                                            <?php 
                                            $img_src = (strpos($service['image'], 'http') === 0) ? $service['image'] : '../' . $service['image'];
                                            ?>
                                            <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Current Image" style="max-height: 80px; object-fit: cover;" class="img-thumbnail d-block">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="image_file" class="form-control" accept="image/*">
                                    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($service['image']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Description</label>
                                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($service['description']); ?></textarea>
                                </div>
                                <button type="submit" name="save_service" class="btn btn-primary w-100 mt-2">Update Service</button>
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
