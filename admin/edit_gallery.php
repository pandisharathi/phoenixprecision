<?php
require_once 'auth.php';
checkAuth();

$msg = '';
$error = '';

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_image'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $filename = $_FILES['gallery_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_name = 'gallery_' . time() . '.' . $ext;
            $upload_path = '../uploads/gallery/' . $new_name;
            $db_path = 'uploads/gallery/' . $new_name;
            
            if (move_uploaded_file($_FILES['gallery_image']['tmp_name'], $upload_path)) {
                $stmt = $pdo->prepare("INSERT INTO gallery (image, title, description) VALUES (?, ?, ?)");
                $stmt->execute([$db_path, $title, $description]);
                header('Location: edit_gallery.php?msg=uploaded');
                exit();
            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Invalid file type. Allowed: " . implode(', ', $allowed);
        }
    } else {
        $error = "Please select an image to upload.";
    }
}

// Handle Image Deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetch();
    
    if ($img) {
        $full_path = '../' . $img['image'];
        if (file_exists($full_path)) {
            unlink($full_path);
        }
        $delStmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        $delStmt->execute([$id]);
        header('Location: edit_gallery.php?msg=deleted');
        exit();
    }
}

// Fetch Gallery Images
$stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
$images = $stmt->fetchAll();

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'uploaded') $msg = "Image uploaded successfully!";
    if ($_GET['msg'] == 'deleted') $msg = "Image deleted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - Phoenix Precision Products Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-custom.css">
    <style>
        .gallery-item-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
        }
        .gallery-item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .gallery-img-preview {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .upload-section {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php 
            $page_title = "Manage Gallery";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if ($msg): ?>
                    <div class="alert alert-success mt-3"><?php echo $msg; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Upload Section -->
                <div class="upload-section">
                    <h5 class="mb-4 fw-bold"><i class="bi bi-cloud-upload me-2 text-primary"></i>Upload New Image</h5>
                    <form method="POST" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Select Image</label>
                            <input type="file" name="gallery_image" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Image Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter title (optional)">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" name="upload_image" class="btn btn-primary w-100">
                                <i class="bi bi-plus-lg me-1"></i> Upload Image
                            </button>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief description..."></textarea>
                        </div>
                    </form>
                </div>

                <hr class="my-5">

                <!-- Gallery List -->
                <h5 class="mb-4 fw-bold">Existing Images (<?php echo count($images); ?>)</h5>
                <div class="row g-4">
                    <?php foreach ($images as $img): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="gallery-item-card card border-0">
                            <img src="../<?php echo $img['image']; ?>" class="gallery-img-preview" alt="Gallery">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-truncate mb-1"><?php echo htmlspecialchars($img['title'] ?: 'Untitled'); ?></h6>
                                <p class="text-muted small mb-3 text-truncate" style="max-height: 40px;"><?php echo htmlspecialchars($img['description']); ?></p>
                                <a href="?delete=<?php echo $img['id']; ?>" class="btn btn-outline-danger btn-sm w-100" 
                                   onclick="return confirm('Are you sure you want to delete this image?')">
                                    <i class="bi bi-trash3 me-1"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (count($images) == 0): ?>
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No images in the gallery yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="js/admin-custom.js"></script>
</body>
</html>
