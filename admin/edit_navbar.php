<?php
require_once 'auth.php';
checkAuth();

// Fetch Nav Links
$stmt = $pdo->query("SELECT * FROM navbar_links ORDER BY sort_order ASC");
$links = $stmt->fetchAll();

// Handle Add New Link
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_link'])) {
    $label = $_POST['label'];
    $url = $_POST['url'];
    $sort = $_POST['sort_order'];

    $addStmt = $pdo->prepare("INSERT INTO navbar_links (label, url, sort_order) VALUES (?, ?, ?)");
    $addStmt->execute([$label, $url, $sort]);
    header('Location: edit_navbar.php?msg=added');
    exit();
}

// Handle Update Link
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_link'])) {
    $id = $_POST['link_id'];
    $label = $_POST['label'];
    $url = $_POST['url'];
    $sort = $_POST['sort_order'];
    $active = isset($_POST['is_active']) ? 1 : 0;

    $updateStmt = $pdo->prepare("UPDATE navbar_links SET label = ?, url = ?, sort_order = ?, is_active = ? WHERE id = ?");
    $updateStmt->execute([$label, $url, $sort, $active, $id]);
    header('Location: edit_navbar.php?msg=updated');
    exit();
}

// Handle Delete Link
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delStmt = $pdo->prepare("DELETE FROM navbar_links WHERE id = ?");
    $delStmt->execute([$id]);
    header('Location: edit_navbar.php?msg=deleted');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Navbar - Admin</title>
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
            $page_title = "Manage Navbar Links";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">Action completed successfully!</div>
                <?php endif; ?>

                <div class="row g-4">
                    <?php foreach ($links as $link): ?>
                    <div class="col-md-6">
                        <div class="card p-4 h-100">
                            <form method="POST">
                                <input type="hidden" name="link_id" value="<?php echo $link['id']; ?>">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Label</label>
                                        <input type="text" name="label" class="form-control" value="<?php echo htmlspecialchars($link['label']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">URL / Anchor</label>
                                        <input type="text" name="url" class="form-control" value="<?php echo htmlspecialchars($link['url']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Sort Order</label>
                                        <input type="number" name="sort_order" class="form-control" value="<?php echo $link['sort_order']; ?>">
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_active" <?php echo $link['is_active'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label small fw-bold">Visible</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-3">
                                    <a href="?delete=<?php echo $link['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                    <button type="submit" name="save_link" class="btn btn-primary btn-sm px-4">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <!-- Add Link Modal -->
    <div class="modal fade" id="addLinkModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Navbar Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Label</label>
                        <input type="text" name="label" class="form-control" required placeholder="e.g. Services">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">URL / Anchor</label>
                        <input type="text" name="url" class="form-control" required placeholder="e.g. #services">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_link" class="btn btn-primary">Add Link</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin-custom.js"></script>
</body>
</html>
