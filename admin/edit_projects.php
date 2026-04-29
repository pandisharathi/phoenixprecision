<?php
require_once 'auth.php';
checkAuth();

// Fetch Projects
$stmt = $pdo->query("SELECT * FROM projects ORDER BY sort_order ASC");
$projects = $stmt->fetchAll();

// Fetch Categories
$catStmt = $pdo->query("SELECT * FROM project_categories ORDER BY name ASC");
$all_categories = $catStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_project'])) {
    $id = $_POST['project_id'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $desc = $_POST['description'];
    $date = $_POST['date_label'];
    $image = $_POST['image'];

    $updateStmt = $pdo->prepare("UPDATE projects SET title = ?, category = ?, description = ?, date_label = ?, image = ? WHERE id = ?");
    $updateStmt->execute([$title, $category, $desc, $date, $image, $id]);
    
    header('Location: edit_projects.php?msg=updated');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Projects - Phoenix Precision Products Admin</title>
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
            $page_title = "Manage Latest Projects";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">Project updated successfully!</div>
                <?php endif; ?>

                <div class="row g-4">
                    <?php foreach ($projects as $project): ?>
                    <div class="col-md-6">
                        <div class="card p-4 h-100">
                            <form method="POST">
                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Project Title</label>
                                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($project['title']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Category</label>
                                    <select name="category" class="form-select">
                                        <?php foreach ($all_categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo $project['category'] == $cat['name'] ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($cat['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Image URL</label>
                                    <input type="text" name="image" class="form-control" value="<?php echo $project['image']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Description</label>
                                    <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($project['description']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Date | Design</label>
                                    <input type="text" name="date_label" class="form-control" value="<?php echo htmlspecialchars($project['date_label']); ?>">
                                </div>
                                <button type="submit" name="save_project" class="btn btn-primary w-100 mt-2">Update Project</button>
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
