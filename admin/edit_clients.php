<?php
require_once 'auth.php';
checkAuth();

$msg = '';
$error = '';

// Handle Logo Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_client'])) {
    $name = $_POST['name'];
    
    if (isset($_FILES['client_logo']) && $_FILES['client_logo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $filename = $_FILES['client_logo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_name = 'client_' . time() . '.' . $ext;
            $upload_path = '../uploads/clients/' . $new_name;
            $db_path = 'uploads/clients/' . $new_name;
            
            if (move_uploaded_file($_FILES['client_logo']['tmp_name'], $upload_path)) {
                $stmt = $pdo->prepare("INSERT INTO clients (logo, name) VALUES (?, ?)");
                $stmt->execute([$db_path, $name]);
                header('Location: edit_clients.php?msg=uploaded');
                exit();
            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Invalid file type. Allowed: " . implode(', ', $allowed);
        }
    } else {
        $error = "Please select a logo to upload.";
    }
}

// Handle Logo Deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT logo FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    $client = $stmt->fetch();
    
    if ($client) {
        $full_path = '../' . $client['logo'];
        if (file_exists($full_path)) {
            unlink($full_path);
        }
        $delStmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
        $delStmt->execute([$id]);
        header('Location: edit_clients.php?msg=deleted');
        exit();
    }
}

// Fetch Clients
$stmt = $pdo->query("SELECT * FROM clients ORDER BY sort_order ASC, created_at DESC");
$clients = $stmt->fetchAll();

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'uploaded') $msg = "Client logo uploaded successfully!";
    if ($_GET['msg'] == 'deleted') $msg = "Client deleted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients - Phoenix Precision Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-custom.css">
    <style>
        .client-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            background: #fff;
            text-align: center;
            padding: 20px;
        }
        .client-logo-preview {
            max-height: 80px;
            max-width: 100%;
            object-fit: contain;
            margin-bottom: 15px;
        }
        .upload-card {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php 
            $page_title = "Manage Our Clients";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if ($msg): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $msg; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 shadow-sm"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="upload-card">
                    <h5 class="fw-bold mb-4"><i class="bi bi-plus-circle me-2 text-primary"></i>Add New Client</h5>
                    <form method="POST" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Client Logo</label>
                            <input type="file" name="client_logo" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Client Name (Optional)</label>
                            <input type="text" name="name" class="form-control" placeholder="Company Name">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="upload_client" class="btn btn-primary w-100 fw-bold">Upload</button>
                        </div>
                    </form>
                </div>

                <h5 class="fw-bold mb-4">Our Clients (<?php echo count($clients); ?>)</h5>
                <div class="row g-4">
                    <?php foreach ($clients as $client): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="client-card">
                            <img src="../<?php echo $client['logo']; ?>" class="client-logo-preview" alt="Client Logo">
                            <p class="fw-bold text-truncate mb-2"><?php echo htmlspecialchars($client['name'] ?: 'Unnamed Client'); ?></p>
                            <a href="?delete=<?php echo $client['id']; ?>" class="btn btn-outline-danger btn-sm w-100" 
                               onclick="return confirm('Remove this client?')">
                                <i class="bi bi-trash me-1"></i> Remove
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (count($clients) == 0): ?>
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No clients added yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="js/admin-custom.js"></script>
</body>
</html>
