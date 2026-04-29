<?php
require_once 'auth.php';
checkAuth();

// Fetch Submissions
$stmt = $pdo->query("SELECT * FROM contact_submissions ORDER BY submitted_at DESC");
$submissions = $stmt->fetchAll();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delStmt = $pdo->prepare("DELETE FROM contact_submissions WHERE id = ?");
    $delStmt->execute([$id]);
    header('Location: view_quotes.php?msg=deleted');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote Requests - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-custom.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php 
            $page_title = "Quote Requests Submissions";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">Submission deleted successfully.</div>
                <?php endif; ?>

                <div class="card table-responsive p-3">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Company</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $sub): ?>
                            <tr>
                                <td class="small text-muted"><?php echo date('M d, Y H:i', strtotime($sub['submitted_at'])); ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($sub['full_name']); ?></td>
                                <td>
                                    <div class="small"><?php echo htmlspecialchars($sub['email']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($sub['phone']); ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark"><?php echo htmlspecialchars($sub['company'] ?: 'N/A'); ?></span></td>
                                <td class="small" style="max-width: 200px;"><?php echo nl2br(htmlspecialchars(substr($sub['description'], 0, 100))); ?>...</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $sub['id']; ?>"><i class="bi bi-eye"></i></button>
                                    <a href="?delete=<?php echo $sub['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>

                            <!-- View Modal -->
                            <div class="modal fade" id="viewModal<?php echo $sub['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Request Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6 border-end">
                                                    <h6>Contact Person</h6>
                                                    <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($sub['full_name']); ?></p>
                                                    <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($sub['email']); ?></p>
                                                    <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($sub['phone']); ?></p>
                                                    <p class="mb-1"><strong>Company:</strong> <?php echo htmlspecialchars($sub['company'] ?: 'None'); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Submission Details</h6>
                                                    <p class="mb-1"><strong>Submitted:</strong> <?php echo $sub['submitted_at']; ?></p>
                                                    <p class="mb-3"><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($sub['address'] ?: 'Not provided')); ?></p>
                                                </div>
                                                <div class="col-12 mt-3 p-3 bg-light rounded">
                                                    <h6>Project Description</h6>
                                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($sub['description'])); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($submissions)): ?>
                            <tr><td colspan="6" class="text-center py-4">No submissions found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin-custom.js"></script>
</body>
</html>
