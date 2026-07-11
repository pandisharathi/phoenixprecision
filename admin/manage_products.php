<?php
require_once 'auth.php';
checkAuth();

$message = '';
$error = '';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') $message = 'Product added successfully!';
    if ($_GET['msg'] == 'updated') $message = 'Product updated successfully!';
    if ($_GET['msg'] == 'deleted') $message = 'Product deleted successfully!';
}

// Handle Actions (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        $title = $_POST['title'];
        $category = $_POST['category'];
        $subcategory = $_POST['subcategory'] ?? null;
        $description = $_POST['description'];
        $description = str_replace('../uploads/projects/', 'uploads/projects/', $description);
        $date_label = $_POST['date_label'];
        $status = $_POST['status'];
        $image = '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                if (!is_dir('../uploads/projects')) {
                    mkdir('../uploads/projects', 0777, true);
                }
                $new_name = 'prod_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/projects/' . $new_name)) {
                    $image = 'uploads/projects/' . $new_name;
                }
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO projects (title, category, subcategory, description, date_label, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $category, $subcategory, $description, $date_label, $image, $status]);
        header('Location: manage_products.php?msg=added');
        exit();
    }
    
    if (isset($_POST['edit_product'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $category = $_POST['category'];
        $subcategory = $_POST['subcategory'] ?? null;
        $description = $_POST['description'];
        $description = str_replace('../uploads/projects/', 'uploads/projects/', $description);
        $date_label = $_POST['date_label'];
        $status = $_POST['status'];
        $image = $_POST['current_image'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = 'prod_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/projects/' . $new_name)) {
                    $image = 'uploads/projects/' . $new_name;
                }
            }
        }
        
        $stmt = $pdo->prepare("UPDATE projects SET title = ?, category = ?, subcategory = ?, description = ?, date_label = ?, image = ?, status = ? WHERE id = ?");
        $stmt->execute([$title, $category, $subcategory, $description, $date_label, $image, $status, $id]);
        header('Location: manage_products.php?msg=updated');
        exit();
    }
    
    if (isset($_POST['delete_product'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: manage_products.php?msg=deleted');
        exit();
    }
}

$products = $pdo->query("SELECT * FROM projects ORDER BY id DESC")->fetchAll();
$categories = $pdo->query("SELECT name FROM project_categories WHERE status = 'active' ORDER BY name ASC")->fetchAll();
$subcategories_all = $pdo->query("SELECT category_name, name FROM project_subcategories WHERE status = 'active' ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-custom.css">
    <style>
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.4em 0.8em;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php 
            $page_title = "Manage Products";
            include 'includes/header.php'; 
            ?>
            <div class="content-body">
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                <?php endif; ?>

                <div class="card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Products List</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="bi bi-plus-circle me-2"></i> Add New Product
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="productsTable" class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Subcategory</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $prod): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo $prod['image'] ? '../'.$prod['image'] : 'https://via.placeholder.com/60'; ?>" class="product-img">
                                    </td>
                                    <td><span class="fw-semibold"><?php echo htmlspecialchars($prod['title']); ?></span></td>
                                    <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($prod['category']); ?></span></td>
                                    <td><?php if(!empty($prod['subcategory'])) { echo '<span class="badge bg-secondary">'.htmlspecialchars($prod['subcategory']).'</span>'; } ?></td>
                                    <td><small class="text-muted"><?php 
                                        $desc_clean = str_replace('&nbsp;', ' ', strip_tags($prod['description']));
                                        echo htmlspecialchars(substr($desc_clean, 0, 50)) . '...'; 
                                    ?></small></td>
                                    <td>
                                        <span class="badge status-badge <?php echo $prod['status'] == 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ucfirst($prod['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $prod['id']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                                <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                                                <button type="submit" name="delete_product" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

<!-- ════════════════ EDIT PRODUCT MODALS ════════════════ -->
<?php foreach ($products as $prod): ?>
<!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?php echo $prod['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content border-0 shadow">
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">Edit Product</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                                                    <input type="hidden" name="current_image" value="<?php echo $prod['image']; ?>">
                                                    
                                                    <div class="row g-3">
                                                        <div class="col-md-8">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Title</label>
                                                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($prod['title']); ?>" required>
                                                            </div>
                                                            <div class="row g-3">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-bold">Category</label>
                                                                    <select name="category" class="form-select category-select" data-target="#subcatEdit<?php echo $prod['id']; ?>" required>
                                                                        <option value="">Select Category</option>
                                                                        <?php foreach ($categories as $cat): ?>
                                                                        <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo $prod['category'] == $cat['name'] ? 'selected' : ''; ?>>
                                                                            <?php echo ucfirst($cat['name']); ?>
                                                                        </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-bold">Subcategory</label>
                                                                    <select name="subcategory" id="subcatEdit<?php echo $prod['id']; ?>" class="form-select subcategory-select" data-selected="<?php echo htmlspecialchars($prod['subcategory'] ?? ''); ?>">
                                                                        <option value="">Select Subcategory</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-bold">Status</label>
                                                                    <select name="status" class="form-select">
                                                                        <option value="active" <?php echo $prod['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                                                        <option value="inactive" <?php echo $prod['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 text-center">
                                                            <label class="form-label fw-bold d-block">Current Image</label>
                                                            <img src="<?php echo $prod['image'] ? '../'.$prod['image'] : 'https://via.placeholder.com/150'; ?>" class="img-thumbnail mb-2" style="max-height: 120px;">
                                                            <input type="file" name="image" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Date Label | Details</label>
                                                                <input type="text" name="date_label" class="form-control" value="<?php echo htmlspecialchars($prod['date_label']); ?>" placeholder="e.g. March 2024 | Industrial">
                                                            </div>
                                                            <div class="mb-0">
                                                                <label class="form-label fw-bold">Description</label>
                                                                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars(str_replace('uploads/projects/', '../uploads/projects/', $prod['description'])); ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="edit_product" class="btn btn-primary px-4">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
<?php endforeach; ?>

<!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Add New Product</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Product Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="Enter product title" required>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Category</label>
                                        <select name="category" class="form-select category-select" data-target="#subcatAdd" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                                                <?php echo ucfirst($cat['name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Subcategory</label>
                                        <select name="subcategory" id="subcatAdd" class="form-select subcategory-select">
                                            <option value="">Select Subcategory</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Product Image</label>
                                    <input type="file" name="image" class="form-control" required>
                                    <small class="text-muted">Allowed: JPG, PNG, WEBP</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Date | Design</label>
                                    <input type="text" name="date_label" class="form-control" placeholder="e.g. March 2026 | Phoenix">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-0">
                                    <label class="form-label fw-bold">Description</label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Enter product description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_product" class="btn btn-success px-4">Add Product</button>
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
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#productsTable').DataTable({
                "order": [[ 1, "asc" ]],
                "pageLength": 10,
                "language": {
                    "search": "Search Products:",
                    "lengthMenu": "Show _MENU_ entries"
                }
            });

            $('textarea[name="description"]').summernote({
                placeholder: 'Enter product description...',
                tabsize: 2,
                height: 150,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onImageUpload: function(files) {
                        var editor = $(this);
                        var data = new FormData();
                        data.append("image", files[0]);
                        $.ajax({
                            data: data,
                            type: "POST",
                            url: "upload_image.php",
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function(url) {
                                editor.summernote('insertImage', url);
                            },
                            error: function(data) {
                                alert("Image upload failed!");
                                console.log(data);
                            }
                        });
                    }
                }
            });
            
            // Fix Summernote dropdowns in Bootstrap modals
            $('.modal').on('shown.bs.modal', function() {
                $(document).off('focusin.modal');
            });
            
            // Subcategory Dynamic Dropdown
            const subcategoriesData = <?php echo json_encode($subcategories_all); ?>;
            
            function updateSubcategories(categorySelect, targetSelectId, selectedSubcat = '') {
                const category = $(categorySelect).val();
                const $target = $(targetSelectId);
                $target.empty().append('<option value="">Select Subcategory</option>');
                
                if (category) {
                    const filtered = subcategoriesData.filter(s => s.category_name === category);
                    filtered.forEach(s => {
                        const selected = s.name === selectedSubcat ? 'selected' : '';
                        $target.append(`<option value="${s.name}" ${selected}>${s.name}</option>`);
                    });
                }
            }

            $('.category-select').on('change', function() {
                const target = $(this).data('target');
                updateSubcategories(this, target);
            });

            // Trigger on load for edit modals
            $('.category-select').each(function() {
                const target = $(this).data('target');
                const selectedSubcat = $(target).data('selected');
                updateSubcategories(this, target, selectedSubcat);
            });
        });
    </script>
</body>
</html>
