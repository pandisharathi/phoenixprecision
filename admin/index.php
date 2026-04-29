<?php
require_once 'auth.php';
checkAuth();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Phoenix Precision Products Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-custom.css">
</head>

<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php
            $page_title = "Administrator Dashboard";
            include 'includes/header.php';
            ?>

            <div class="content-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card p-4 text-center h-100 border-start border-4 border-danger">
                            <i class="bi bi-envelope-paper h1 text-danger mb-3"></i>
                            <h5>Quote Requests</h5>
                            <p class="small text-muted">View and manage contact form submissions.</p>
                            <a href="view_quotes.php" class="btn btn-outline-danger btn-sm mt-auto">Manage</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 text-center h-100">
                            <i class="bi bi-images h1 text-primary mb-3"></i>
                            <h5>Hero Slides</h5>
                            <p class="small text-muted">Update the main website slider content.</p>
                            <a href="edit_hero.php" class="btn btn-outline-primary btn-sm mt-auto">Manage</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 text-center h-100">
                            <i class="bi bi-briefcase h1 text-primary mb-3"></i>
                            <h5>Services</h5>
                            <p class="small text-muted">Update your core expertise and business offerings.</p>
                            <a href="edit_services.php" class="btn btn-outline-primary btn-sm mt-auto">Manage</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 text-center h-100">
                            <i class="bi bi-grid-3x3-gap h1 text-primary mb-3"></i>
                            <h5>Projects</h5>
                            <p class="small text-muted">Add or edit your latest work and case studies.</p>
                            <a href="edit_projects.php" class="btn btn-outline-primary btn-sm mt-auto">Manage</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 text-center h-100">
                            <i class="bi bi-chat-quote h1 text-primary mb-3"></i>
                            <h5>Testimonials</h5>
                            <p class="small text-muted">Manage what clients are saying about you.</p>
                            <a href="edit_testimonials.php" class="btn btn-outline-primary btn-sm mt-auto">Manage</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 text-center h-100">
                            <i class="bi bi-info-circle h1 text-primary mb-3"></i>
                            <h5>About Us</h5>
                            <p class="small text-muted">Manage the main company description and story.</p>
                            <a href="edit_about.php" class="btn btn-outline-primary btn-sm mt-auto">Manage</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 text-center h-100">
                            <i class="bi bi-journal-text h1 text-primary mb-3"></i>
                            <h5>Blogs</h5>
                            <p class="small text-muted">Manage the latest insights and blog posts.</p>
                            <a href="edit_blogs.php" class="btn btn-outline-primary btn-sm mt-auto">Manage</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 text-center h-100 border-start border-4 border-warning">
                            <i class="bi bi-info-square h1 text-warning mb-3"></i>
                            <h5>Top Header</h5>
                            <p class="small text-muted">Update contact info and social links.</p>
                            <a href="edit_topheader.php" class="btn btn-outline-warning btn-sm mt-auto">Manage</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 text-center h-100 border-start border-4 border-dark">
                            <i class="bi bi-list h1 text-dark mb-3"></i>
                            <h5>Navbar Menu</h5>
                            <p class="small text-muted">Manage site navigation links and order.</p>
                            <a href="edit_navbar.php" class="btn btn-outline-dark btn-sm mt-auto">Manage</a>
                        </div>
                    </div>
                    <!-- Security Card -->
                    <div class="col-md-4">
                        <div class="card p-4 text-center h-100 border-start border-4 border-secondary">
                            <i class="bi bi-shield-lock h1 text-secondary mb-3"></i>
                            <h5>Security</h5>
                            <p class="small text-muted">Change your administrative password.</p>
                            <a href="change_password.php" class="btn btn-outline-secondary btn-sm mt-auto">Manage</a>
                        </div>
                    </div>
                </div>

                <!-- <div class="alert alert-info mt-5 border-0 rounded-4 p-4 shadow-sm">
                    <h5 class="fw-bold"><i class="bi bi-info-circle me-2"></i> Database Configuration</h5>
                    <p class="mb-0">Please ensure you have imported the <strong>sql/setup.sql</strong> file into your MySQL database (phoenix_precision_db) using phpMyAdmin to see the live data.</p>
                </div> -->
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="js/admin-custom.js"></script>
</body>

</html>