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
    $image = $_POST['image'];

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
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Section Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($about['title']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Image URL</label>
                            <input type="text" name="image" class="form-control" value="<?php echo $about['image']; ?>">
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
