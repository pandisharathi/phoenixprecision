<?php
require_once 'auth.php';
checkAuth();

$message = '';
$err_message = '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add Notification
    if (isset($_POST['add_notification'])) {
        if (isset($_FILES['notif_image']) && $_FILES['notif_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['notif_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_name = 'notif_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $upload_path = '../uploads/notifications/' . $new_name;
                
                if (!is_dir('../uploads/notifications/')) {
                    mkdir('../uploads/notifications/', 0777, true);
                }

                if (move_uploaded_file($_FILES['notif_image']['tmp_name'], $upload_path)) {
                    $image_url = 'uploads/notifications/' . $new_name;
                    $stmt = $pdo->prepare("INSERT INTO notifications (image_path, status) VALUES (?, 0)");
                    $stmt->execute([$image_url]);
                    header('Location: manage_notifications.php?msg=added');
                    exit();
                } else {
                    $err_message = "Failed to upload image.";
                }
            } else {
                $err_message = "Invalid file format. Only JPG, PNG, GIF, and WEBP are allowed.";
            }
        } else {
            $err_message = "Please select a valid image.";
        }
    }

    // Toggle Status
    if (isset($_POST['toggle_status'])) {
        $id = $_POST['notif_id'];
        $new_status = $_POST['new_status'];

        if ($new_status == 1) {
            // Disable all others first
            $pdo->query("UPDATE notifications SET status = 0");
        }
        
        $stmt = $pdo->prepare("UPDATE notifications SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $id]);
        header('Location: manage_notifications.php?msg=status_updated');
        exit();
    }

    // Delete Notification
    if (isset($_POST['delete_notification'])) {
        $id = $_POST['notif_id'];
        $image_path = $_POST['image_path'];
        
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->execute([$id]);
        
        if (file_exists('../' . $image_path)) {
            unlink('../' . $image_path);
        }
        header('Location: manage_notifications.php?msg=deleted');
        exit();
    }
}

// Fetch Notifications
$notifications = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC")->fetchAll();

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') $message = "Notification added successfully!";
    if ($_GET['msg'] == 'status_updated') $message = "Status updated successfully!";
    if ($_GET['msg'] == 'deleted') $message = "Notification deleted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notifications - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-custom.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php 
            $page_title = "Manage Home Page Notifications";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($err_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $err_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Add Notification Form -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Add New Notification Image</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data" class="row g-3">
                            <div class="col-md-9">
                                <label class="form-label">Select Image (Popup Center)</label>
                                <input type="file" name="notif_image" class="form-control" required>
                                <div class="form-text">Recommended size: 600x600px (Aspect ratio 1:1 or as needed).</div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" name="add_notification" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle me-1"></i> Add Notification
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Existing Notifications</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 100px;">Preview</th>
                                        <th>Date Added</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($notifications)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No notifications found.</td>
                                        </tr>
                                    <?php endif; ?>
                                    
                                    <?php foreach ($notifications as $notif): ?>
                                        <tr>
                                            <td>
                                                <img src="../<?php echo $notif['image_path']; ?>" class="img-thumbnail" style="max-height: 60px;">
                                            </td>
                                            <td><?php echo date('d M Y, h:i A', strtotime($notif['created_at'])); ?></td>
                                            <td>
                                                <?php if ($notif['status'] == 1): ?>
                                                    <span class="badge bg-success">Enabled</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Disabled</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <form method="POST" action="" onsubmit="return confirm('Change status?');">
                                                        <input type="hidden" name="notif_id" value="<?php echo $notif['id']; ?>">
                                                        <input type="hidden" name="new_status" value="<?php echo $notif['status'] == 1 ? 0 : 1; ?>">
                                                        <button type="submit" name="toggle_status" class="btn btn-sm <?php echo $notif['status'] == 1 ? 'btn-warning' : 'btn-success'; ?>">
                                                            <?php echo $notif['status'] == 1 ? 'Disable' : 'Enable'; ?>
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this?');">
                                                        <input type="hidden" name="notif_id" value="<?php echo $notif['id']; ?>">
                                                        <input type="hidden" name="image_path" value="<?php echo $notif['image_path']; ?>">
                                                        <button type="submit" name="delete_notification" class="btn btn-sm btn-danger">
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
                
                <div class="alert alert-info mt-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Note:</strong> Only <strong>one</strong> notification can be enabled at a time. Enabling a new notification will automatically disable any previously active one.
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin-custom.js"></script>
</body>
</html>
