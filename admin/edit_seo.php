<?php
require_once 'auth.php';
checkAuth();

// Fetch SEO Settings
$stmt = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
$seo = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_seo'])) {
    $title = $_POST['site_title'];
    $desc = $_POST['meta_description'];
    $keywords = $_POST['meta_keywords'];

    $updateStmt = $pdo->prepare("UPDATE site_settings SET site_title = ?, meta_description = ?, meta_keywords = ? WHERE id = 1");
    $updateStmt->execute([$title, $desc, $keywords]);
    
    header('Location: edit_seo.php?msg=updated');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Settings - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-custom.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php 
            $page_title = "Manage SEO Settings";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success shadow-sm">SEO settings updated successfully!</div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase">Default Site Title</label>
                                <input type="text" name="site_title" class="form-control form-control-lg" value="<?php echo htmlspecialchars($seo['site_title']); ?>" placeholder="e.g. Phoenix Precision Products">
                                <div class="form-text mt-2">This title appears in the browser tab if not overridden by page-specific titles.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="4" placeholder="Describe your website for search engines..."><?php echo htmlspecialchars($seo['meta_description']); ?></textarea>
                                <div class="form-text mt-2">Recommended length: 150-160 characters.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase">Meta Keywords</label>
                                <textarea name="meta_keywords" class="form-control" rows="3" placeholder="honing, machines, oil..."><?php echo htmlspecialchars($seo['meta_keywords']); ?></textarea>
                                <div class="form-text mt-2">Separate keywords with commas. Avoid over-stuffing.</div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" name="save_seo" class="btn btn-primary btn-lg px-5 py-3 fw-bold">
                                    <i class="bi bi-save me-2"></i> Save SEO Configuration
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded border">
                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-1"></i> SEO Tip</h6>
                    <p class="small mb-0 text-muted">A compelling description can improve your Click-Through Rate (CTR) from Google search results. Make sure to include your primary keywords naturally.</p>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="js/admin-custom.js"></script>
</body>
</html>
