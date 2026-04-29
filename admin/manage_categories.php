<?php
require_once 'auth.php';
checkAuth();

$message = '';
$error = '';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') $message = 'Category added successfully!';
    if ($_GET['msg'] == 'updated') $message = 'Category updated successfully!';
    if ($_GET['msg'] == 'deleted') $message = 'Category deleted successfully!';
}

// Handle Actions (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_category'])) {
        $name = $_POST['name'];
        $image = '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = 'cat_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/categories/' . $new_name)) {
                    $image = 'uploads/categories/' . $new_name;
                }
            }
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO project_categories (name, image) VALUES (?, ?)");
            $stmt->execute([$name, $image]);
            header('Location: manage_categories.php?msg=added');
            exit();
        } catch (PDOException $e) {
            $error = "Error: Category name may already exist.";
        }
    }
    
    if (isset($_POST['edit_category'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $status = $_POST['status'];
        $image = $_POST['current_image'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = 'cat_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/categories/' . $new_name)) {
                    $image = 'uploads/categories/' . $new_name;
                }
            }
        }
        
        $stmt = $pdo->prepare("UPDATE project_categories SET name = ?, image = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $image, $status, $id]);
        header('Location: manage_categories.php?msg=updated');
        exit();
    }
    
    if (isset($_POST['delete_category'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM project_categories WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: manage_categories.php?msg=deleted');
        exit();
    }
}

$categories = $pdo->query("SELECT * FROM project_categories ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-custom.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php 
            $page_title = "Project Categories";
            include 'includes/header.php'; 
            ?>
            <div class="content-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card p-4 mb-4">
                    <h5 class="fw-bold mb-3">Add New Category</h5>
                    <form method="POST" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-5">
                            <input type="text" name="name" class="form-control" placeholder="Category Name" required>
                        </div>
                        <div class="col-md-5">
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="add_category" class="btn btn-primary w-100">Add</button>
                        </div>
                    </form>
                </div>

                <div class="card p-4">
                    <h5 class="fw-bold mb-3">Existing Categories</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td>
                                        <div style="width: 50px; height: 50px; overflow: hidden; border-radius: 5px;">
                                            <img src="<?php echo $cat['image'] ? '../'.$cat['image'] : 'https://via.placeholder.com/50'; ?>" class="w-100 h-100" style="object-fit: cover;">
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $cat['status'] == 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ucfirst($cat['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $cat['id']; ?>">Edit</button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                            <button type="submit" name="delete_category" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?php echo $cat['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Category</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                                    <input type="hidden" name="current_image" value="<?php echo $cat['image']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($cat['name']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" <?php echo $cat['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                                            <option value="inactive" <?php echo $cat['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Change Image</label>
                                                        <input type="file" name="image" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" name="edit_category" class="btn btn-primary">Save Changes</button>
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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
