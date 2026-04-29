<?php
require_once 'auth.php';
checkAuth();

$message = '';
$error = '';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') $message = 'Blog post added successfully!';
    if ($_GET['msg'] == 'updated') $message = 'Blog post updated successfully!';
    if ($_GET['msg'] == 'deleted') $message = 'Blog post deleted successfully!';
}

// Handle Actions (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_blog'])) {
        $title = $_POST['title'];
        $category = $_POST['category'];
        $summary = $_POST['summary'];
        $content = $_POST['content'];
        $status = $_POST['status'];
        $image = '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'png'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                if (!is_dir('../uploads/blogs')) {
                    mkdir('../uploads/blogs', 0777, true);
                }
                $new_name = 'blog_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/blogs/' . $new_name)) {
                    $image = 'uploads/blogs/' . $new_name;
                }
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO blogs (title, category, summary, content, image, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $category, $summary, $content, $image, $status]);
        header('Location: edit_blogs.php?msg=added');
        exit();
    }
    
    if (isset($_POST['edit_blog'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $category = $_POST['category'];
        $summary = $_POST['summary'];
        $content = $_POST['content'];
        $status = $_POST['status'];
        $image = $_POST['current_image'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'png'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = 'blog_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/blogs/' . $new_name)) {
                    $image = 'uploads/blogs/' . $new_name;
                }
            }
        }
        
        $stmt = $pdo->prepare("UPDATE blogs SET title = ?, category = ?, summary = ?, content = ?, image = ?, status = ? WHERE id = ?");
        $stmt->execute([$title, $category, $summary, $content, $image, $status, $id]);
        header('Location: edit_blogs.php?msg=updated');
        exit();
    }
    
    if (isset($_POST['delete_blog'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: edit_blogs.php?msg=deleted');
        exit();
    }
}

$blogs = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blogs - Phoenix Precision Products Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/admin-custom.css">
    <style>
        .blog-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.4em 0.8em;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php 
            $page_title = "Manage Blog Insights";
            include 'includes/header.php'; 
            ?>

            <div class="content-body">
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card p-4 mb-4 border-0 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Blogs List</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBlogModal">
                            <i class="bi bi-plus-circle me-2"></i> Add New Blog
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="blogsTable" class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blogs as $blog): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo $blog['image'] ? '../'.$blog['image'] : 'https://via.placeholder.com/60'; ?>" class="blog-img">
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?php echo htmlspecialchars($blog['title']); ?></div>
                                        <small class="text-muted d-block" style="max-width: 300px;"><?php echo substr(htmlspecialchars($blog['summary']), 0, 80) . '...'; ?></small>
                                    </td>
                                    <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($blog['category']); ?></span></td>
                                    <td>
                                        <span class="badge status-badge <?php echo $blog['status'] == 'show' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ucfirst($blog['status']); ?>
                                        </span>
                                    </td>
                                    <td><small><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></small></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $blog['id']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this blog post?');">
                                                <input type="hidden" name="id" value="<?php echo $blog['id']; ?>">
                                                <button type="submit" name="delete_blog" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?php echo $blog['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content border-0 shadow">
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">Edit Blog Post</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="id" value="<?php echo $blog['id']; ?>">
                                                    <input type="hidden" name="current_image" value="<?php echo $blog['image']; ?>">
                                                    
                                                    <div class="row g-3">
                                                        <div class="col-md-8">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Title</label>
                                                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                                                            </div>
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-bold">Category</label>
                                                                    <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($blog['category']); ?>" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-bold">Status</label>
                                                                    <select name="status" class="form-select">
                                                                        <option value="show" <?php echo $blog['status'] == 'show' ? 'selected' : ''; ?>>Show</option>
                                                                        <option value="hide" <?php echo $blog['status'] == 'hide' ? 'selected' : ''; ?>>Hide</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 text-center">
                                                            <label class="form-label fw-bold d-block">Current Image</label>
                                                            <img src="<?php echo $blog['image'] ? '../'.$blog['image'] : 'https://via.placeholder.com/150'; ?>" class="img-thumbnail mb-2" style="max-height: 120px;">
                                                            <input type="file" name="image" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Summary</label>
                                                                <textarea name="summary" class="form-control" rows="2" required><?php echo htmlspecialchars($blog['summary']); ?></textarea>
                                                            </div>
                                                            <div class="mb-0">
                                                                <label class="form-label fw-bold">Full Content</label>
                                                                <textarea name="content" class="form-control" rows="6"><?php echo htmlspecialchars($blog['content']); ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="edit_blog" class="btn btn-primary px-4">Save Changes</button>
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

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <!-- Add Blog Modal -->
    <div class="modal fade" id="addBlogModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Add New Blog Post</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Post Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="Enter blog title" required>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Category</label>
                                        <input type="text" name="category" class="form-control" placeholder="e.g. Technology, Design" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="show">Show</option>
                                            <option value="hide">Hide</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Blog Image</label>
                                    <input type="file" name="image" class="form-control" required>
                                    <small class="text-muted">Allowed: JPG, PNG, WEBP</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Summary</label>
                                    <textarea name="summary" class="form-control" rows="2" placeholder="Brief summary of the post" required></textarea>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-bold">Full Content</label>
                                    <textarea name="content" class="form-control" rows="6" placeholder="Write your blog content here..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_blog" class="btn btn-success px-4">Publish Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#blogsTable').DataTable({
                "order": [[ 4, "desc" ]],
                "pageLength": 10,
                "language": {
                    "search": "Search Blogs:",
                    "lengthMenu": "Show _MENU_ entries"
                }
            });
        });
    </script>
</body>
</html>
