<?php
require_once 'auth.php';
checkAuth();

// Auto-create table and default data if not exists
$tableExists = $pdo->query("SHOW TABLES LIKE 'vision_mission'")->rowCount() > 0;
if (!$tableExists) {
    // Create table
    $sql = "CREATE TABLE `vision_mission` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `type` varchar(50) NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` text NOT NULL,
        `image` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `type` (`type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);
    
    // Insert defaults
    $pdo->exec("INSERT INTO `vision_mission` (`type`, `title`, `description`) VALUES
    ('vision', 'Our Vision', 'To be the leading provider of precision honing solutions globally.'),
    ('mission', 'Our Mission', 'To deliver unparalleled quality and service in the honing industry, exceeding customer expectations through innovation and operational excellence.'),
    ('quality', 'Quality Policy', 'We are committed to maintaining the highest standards of quality in all our processes and products.')");
}

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_vision_mission'])) {
    try {
        $types = ['vision', 'mission', 'quality'];
        
        foreach ($types as $type) {
            $title = htmlspecialchars($_POST["{$type}_title"]);
            $description = htmlspecialchars($_POST["{$type}_description"]);
            
            // Handle image upload
            $image_query_part = "";
            $params = [$title, $description, $type];
            
            if (isset($_FILES["{$type}_image"]) && $_FILES["{$type}_image"]['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = $_FILES["{$type}_image"]['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $new_filename = $type . '_' . time() . '.' . $ext;
                    $upload_path = '../uploads/' . $new_filename;
                    
                    if (move_uploaded_file($_FILES["{$type}_image"]['tmp_name'], $upload_path)) {
                        $image_query_part = ", image = ?";
                        $params = [$title, $description, $new_filename, $type];
                        
                        // Delete old image if it exists
                        $stmt = $pdo->prepare("SELECT image FROM vision_mission WHERE type = ?");
                        $stmt->execute([$type]);
                        $old_img = $stmt->fetchColumn();
                        if ($old_img && file_exists('../uploads/' . $old_img)) {
                            unlink('../uploads/' . $old_img);
                        }
                    }
                }
            }
            
            $stmt = $pdo->prepare("UPDATE vision_mission SET title = ?, description = ? {$image_query_part} WHERE type = ?");
            $stmt->execute($params);
        }
        
        $message = "Vision, Mission & Quality Policy updated successfully!";
        
    } catch (Exception $e) {
        $error = "Error updating records: " . $e->getMessage();
    }
}

// Fetch current data
$stmt = $pdo->query("SELECT * FROM vision_mission");
$data = [];
while ($row = $stmt->fetch()) {
    $data[$row['type']] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vision & Mission - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="css/admin-custom.css">
</head>
<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content flex-grow-1 bg-light">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>

            <!-- Page Content -->
            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h3 mb-0 text-gray-800">Edit Vision, Mission & Quality Policy</h2>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row g-4">
                        <?php 
                        $sections = [
                            'vision' => ['title' => 'Vision', 'icon' => 'bi-eye'],
                            'mission' => ['title' => 'Mission', 'icon' => 'bi-rocket'],
                            'quality' => ['title' => 'Quality Policy', 'icon' => 'bi-shield-check']
                        ];
                        foreach ($sections as $type => $info): 
                            $item = $data[$type] ?? ['title' => '', 'description' => '', 'image' => ''];
                        ?>
                        <div class="col-lg-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white py-3 border-bottom border-light">
                                    <h5 class="mb-0 text-primary"><i class="<?php echo $info['icon']; ?> me-2"></i><?php echo $info['title']; ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Title</label>
                                        <input type="text" class="form-control" name="<?php echo $type; ?>_title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Description</label>
                                        <textarea class="form-control" name="<?php echo $type; ?>_description" rows="4" required><?php echo htmlspecialchars($item['description']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Image (Opt)</label>
                                        <?php if ($item['image']): ?>
                                            <div class="mb-2">
                                                <img src="../uploads/<?php echo $item['image']; ?>" alt="<?php echo $info['title']; ?>" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" name="<?php echo $type; ?>_image" accept="image/*">
                                        <small class="text-muted">Recommended size: 400x300px</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-4 text-end">
                        <button type="submit" name="update_vision_mission" class="btn btn-primary px-5 py-2 fw-medium shadow-sm">
                            <i class="bi bi-save me-2"></i> Save All Changes
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
