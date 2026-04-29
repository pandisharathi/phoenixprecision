<?php
require_once 'auth.php';
checkAuth();

// Fetch Top Header Info
$stmt = $pdo->query("SELECT * FROM top_header_info WHERE id = 1");
$topInfo = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_topheader'])) {
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $fb = $_POST['facebook_url'];
    $ig = $_POST['instagram_url'];
    $tw = $_POST['twitter_url'];
    $wa = $_POST['whatsapp_url'];
    $tg = $_POST['telegram_url'];
    $secondary_email = $_POST['secondary_email'];

    $updateStmt = $pdo->prepare("UPDATE top_header_info SET address = ?, phone = ?, email = ?, secondary_email = ?, facebook_url = ?, instagram_url = ?, twitter_url = ?, whatsapp_url = ?, telegram_url = ? WHERE id = 1");
    $updateStmt->execute([$address, $phone, $email, $secondary_email, $fb, $ig, $tw, $wa, $tg]);
    
    header('Location: edit_topheader.php?msg=updated');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Top Header - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-custom.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php 
            $page_title = "Manage Top Header Info";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">Top header info updated successfully!</div>
                <?php endif; ?>

                <div class="card p-4">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Address</label>
                                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($topInfo['address']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($topInfo['phone']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Primary Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($topInfo['email']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Secondary Email (Optional)</label>
                                <input type="email" name="secondary_email" class="form-control" value="<?php echo htmlspecialchars($topInfo['secondary_email'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Facebook URL</label>
                                <input type="text" name="facebook_url" class="form-control" value="<?php echo htmlspecialchars($topInfo['facebook_url']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Instagram URL</label>
                                <input type="text" name="instagram_url" class="form-control" value="<?php echo htmlspecialchars($topInfo['instagram_url']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Twitter URL</label>
                                <input type="text" name="twitter_url" class="form-control" value="<?php echo htmlspecialchars($topInfo['twitter_url']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">WhatsApp URL</label>
                                <input type="text" name="whatsapp_url" class="form-control" value="<?php echo htmlspecialchars($topInfo['whatsapp_url']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">YouTube URL</label>
                                <input type="text" name="youtube_url" class="form-control" value="<?php echo htmlspecialchars($topInfo['youtube_url']); ?>">
                            </div>
                        </div>
                        <button type="submit" name="save_topheader" class="btn btn-primary mt-4 px-5">Save Changes</button>
                    </form>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="js/admin-custom.js"></script>
</body>
</html>
