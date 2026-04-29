<?php
require_once 'auth.php';
checkAuth();

$message = '';
$error = '';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') $message = 'Hero slide added successfully!';
    if ($_GET['msg'] == 'updated') $message = 'Hero slide updated successfully!';
    if ($_GET['msg'] == 'deleted') $message = 'Hero slide deleted successfully!';
}

// Handle Actions (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_slide'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $btn_text = $_POST['btn_text'];
        $btn_link = $_POST['btn_link'];
        $status = $_POST['status'];
        $sort_order = $_POST['sort_order'] ?: 0;
        $image = '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                if (!is_dir('../uploads/hero')) {
                    mkdir('../uploads/hero', 0777, true);
                }
                $new_name = 'hero_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/hero/' . $new_name)) {
                    $image = 'uploads/hero/' . $new_name;
                }
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO hero_slides (title, description, bg_image, btn_text, btn_link, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $image, $btn_text, $btn_link, $status, $sort_order]);
        header('Location: edit_hero.php?msg=added');
        exit();
    }
    
    if (isset($_POST['edit_slide'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $btn_text = $_POST['btn_text'];
        $btn_link = $_POST['btn_link'];
        $status = $_POST['status'];
        $sort_order = $_POST['sort_order'] ?: 0;
        $image = $_POST['current_image'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = 'hero_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/hero/' . $new_name)) {
                    $image = 'uploads/hero/' . $new_name;
                }
            }
        }
        
        $stmt = $pdo->prepare("UPDATE hero_slides SET title = ?, description = ?, bg_image = ?, btn_text = ?, btn_link = ?, status = ?, sort_order = ? WHERE id = ?");
        $stmt->execute([$title, $description, $image, $btn_text, $btn_link, $status, $sort_order, $id]);
        header('Location: edit_hero.php?msg=updated');
        exit();
    }
    
    if (isset($_POST['delete_slide'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: edit_hero.php?msg=deleted');
        exit();
    }
}

$slides = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hero Slides - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/admin-custom.css">
    <style>
        .hero-img-preview {
            width: 150px; /* "Showing little big side" */
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.4em 0.8em;
        }
        .modal-hero-preview {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 2px solid #eee;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php 
            $page_title = "Manage Hero Slider";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card p-4 mb-4 border-0 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Hero Slides List</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSlideModal">
                            <i class="bi bi-plus-circle me-2"></i> Add New Slide
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="heroTable" class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Preview</th>
                                    <th>Content</th>
                                    <th>Buttons</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($slides as $slide): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo (strpos($slide['bg_image'], 'http') === 0) ? $slide['bg_image'] : '../' . $slide['bg_image']; ?>" class="hero-img-preview">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($slide['title']); ?></div>
                                        <small class="text-muted d-block" style="max-width: 250px;"><?php echo htmlspecialchars($slide['description']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($slide['btn_text'] ?: 'No Btn'); ?></span>
                                    </td>
                                    <td><?php echo $slide['sort_order']; ?></td>
                                    <td>
                                        <span class="badge status-badge <?php echo $slide['status'] == 'show' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ucfirst($slide['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $slide['id']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this hero slide?');">
                                                <input type="hidden" name="id" value="<?php echo $slide['id']; ?>">
                                                <button type="submit" name="delete_slide" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?php echo $slide['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content border-0 shadow">
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">Edit Hero Slide</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="id" value="<?php echo $slide['id']; ?>">
                                                    <input type="hidden" name="current_image" value="<?php echo $slide['bg_image']; ?>">
                                                    
                                                    <div class="row g-3">
                                                        <div class="col-md-12 text-center">
                                                            <label class="form-label fw-bold d-block">Current Slide Image</label>
                                                            <img src="<?php echo (strpos($slide['bg_image'], 'http') === 0) ? $slide['bg_image'] : '../' . $slide['bg_image']; ?>" class="modal-hero-preview">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Slide Title</label>
                                                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($slide['title']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Description</label>
                                                                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($slide['description']); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Change Image</label>
                                                                <input type="file" name="image" class="form-control">
                                                            </div>
                                                            <div class="row g-2">
                                                                <div class="col-6">
                                                                    <label class="form-label fw-bold">Status</label>
                                                                    <select name="status" class="form-select">
                                                                        <option value="show" <?php echo $slide['status'] == 'show' ? 'selected' : ''; ?>>Show</option>
                                                                        <option value="hide" <?php echo $slide['status'] == 'hide' ? 'selected' : ''; ?>>Hide</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label fw-bold">Sort Order</label>
                                                                    <input type="number" name="sort_order" class="form-control" value="<?php echo $slide['sort_order']; ?>">
                                                                </div>
                                                            </div>
                                                            <div class="row g-2 mt-2">
                                                                <div class="col-6">
                                                                    <label class="form-label fw-bold">Button Text</label>
                                                                    <input type="text" name="btn_text" class="form-control" value="<?php echo htmlspecialchars($slide['btn_text']); ?>">
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label fw-bold">Button Link</label>
                                                                    <input type="text" name="btn_link" class="form-control" value="<?php echo htmlspecialchars($slide['btn_link']); ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="edit_slide" class="btn btn-primary px-4">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <!-- Add Slide Modal -->
    <div class="modal fade" id="addSlideModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Add New Hero Slide</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Slide Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="Enter headline title" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Description</label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Enter sub-headline text"></textarea>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Slide Image</label>
                                    <input type="file" name="image" class="form-control" required>
                                    <small class="text-muted">High resolution images recommended (1920x1080)</small>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label fw-bold">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="show">Show</option>
                                            <option value="hide">Hide</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold">Sort Order</label>
                                        <input type="number" name="sort_order" class="form-control" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Button Text</label>
                                <input type="text" name="btn_text" class="form-control" placeholder="e.g. Explore Now">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Button Link</label>
                                <input type="text" name="btn_link" class="form-control" placeholder="e.g. #contact">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_slide" class="btn btn-success px-4">Add Slide</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#heroTable').DataTable({
                "order": [[ 3, "asc" ]],
                "pageLength": 10,
                "language": {
                    "search": "Search Slides:",
                    "lengthMenu": "Show _MENU_ entries"
                }
            });
        });
    </script>
</body>
</html>
