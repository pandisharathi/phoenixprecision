<?php
require_once 'auth.php';
checkAuth();

$message = '';
$error = '';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') $message = 'Subcategory added successfully!';
    if ($_GET['msg'] == 'updated') $message = 'Subcategory updated successfully!';
    if ($_GET['msg'] == 'deleted') $message = 'Subcategory deleted successfully!';
}

// Fetch all categories for the dropdown
$parent_categories = $pdo->query("SELECT name FROM project_categories ORDER BY name ASC")->fetchAll();

// Handle Actions (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_subcategory'])) {
        $category_name = $_POST['category_name'];
        $name = $_POST['name'];
        $image = '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = 'subcat_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (!is_dir('../uploads/subcategories')) {
                    mkdir('../uploads/subcategories', 0777, true);
                }
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/subcategories/' . $new_name)) {
                    $image = 'uploads/subcategories/' . $new_name;
                }
            }
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO project_subcategories (category_name, name, image) VALUES (?, ?, ?)");
            $stmt->execute([$category_name, $name, $image]);
            header('Location: manage_subcategories.php?msg=added');
            exit();
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['edit_subcategory'])) {
        $id = $_POST['id'];
        $category_name = $_POST['category_name'];
        $name = $_POST['name'];
        $status = $_POST['status'];
        $image = $_POST['current_image'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = 'subcat_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (!is_dir('../uploads/subcategories')) {
                    mkdir('../uploads/subcategories', 0777, true);
                }
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/subcategories/' . $new_name)) {
                    $image = 'uploads/subcategories/' . $new_name;
                }
            }
        }
        
        $stmt = $pdo->prepare("UPDATE project_subcategories SET category_name = ?, name = ?, image = ?, status = ? WHERE id = ?");
        $stmt->execute([$category_name, $name, $image, $status, $id]);
        header('Location: manage_subcategories.php?msg=updated');
        exit();
    }
    
    if (isset($_POST['delete_subcategory'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM project_subcategories WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: manage_subcategories.php?msg=deleted');
        exit();
    }
}

$subcategories = $pdo->query("SELECT * FROM project_subcategories ORDER BY category_name ASC, name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subcategories - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-custom.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php 
            $page_title = "Project Subcategories";
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
                    <h5 class="fw-bold mb-3">Add New Subcategory</h5>
                    <form method="POST" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-3">
                            <select name="category_name" class="form-select" required>
                                <option value="">Select Parent Category</option>
                                <?php foreach ($parent_categories as $pcat): ?>
                                    <option value="<?php echo htmlspecialchars($pcat['name']); ?>"><?php echo htmlspecialchars($pcat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="name" class="form-control" placeholder="Subcategory Name" required>
                        </div>
                        <div class="col-md-4">
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="add_subcategory" class="btn btn-primary w-100">Add</button>
                        </div>
                    </form>
                </div>

                <div class="card p-4">
                    <h5 class="fw-bold mb-3">Existing Subcategories</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Parent Category</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subcategories as $subcat): ?>
                                <tr>
                                    <td>
                                        <div style="width: 50px; height: 50px; overflow: hidden; border-radius: 5px;">
                                            <img src="<?php echo $subcat['image'] ? '../'.$subcat['image'] : 'https://via.placeholder.com/50'; ?>" class="w-100 h-100" style="object-fit: cover;">
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($subcat['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($subcat['name']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $subcat['status'] == 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ucfirst($subcat['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $subcat['id']; ?>">Edit</button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this subcategory?');">
                                            <input type="hidden" name="id" value="<?php echo $subcat['id']; ?>">
                                            <button type="submit" name="delete_subcategory" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?php echo $subcat['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Subcategory</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?php echo $subcat['id']; ?>">
                                                    <input type="hidden" name="current_image" value="<?php echo $subcat['image']; ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Parent Category</label>
                                                        <select name="category_name" class="form-select" required>
                                                            <option value="">Select Parent Category</option>
                                                            <?php foreach ($parent_categories as $pcat): ?>
                                                                <option value="<?php echo htmlspecialchars($pcat['name']); ?>" <?php echo $subcat['category_name'] == $pcat['name'] ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($pcat['name']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($subcat['name']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" <?php echo $subcat['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                                            <option value="inactive" <?php echo $subcat['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Change Image</label>
                                                        <input type="file" name="image" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" name="edit_subcategory" class="btn btn-primary">Save Changes</button>
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
