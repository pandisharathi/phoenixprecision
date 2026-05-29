<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar" id="sidebar">
    <div class="brand-header">
        <h4 class="fw-bold text-white text-center">Phoenix Precision <span class="text-primary">Admin</span></h4>
    </div>
    <nav class="nav flex-column px-3">
        <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a class="nav-link <?php echo $current_page == 'view_quotes.php' ? 'active' : ''; ?>" href="view_quotes.php">
            <i class="bi bi-envelope-paper me-2"></i> Quote Requests
        </a>
        <a class="nav-link <?php echo $current_page == 'edit_hero.php' ? 'active' : ''; ?>" href="edit_hero.php">
            <i class="bi bi-images me-2"></i> Hero
        </a>
        <a class="nav-link <?php echo $current_page == 'edit_about.php' ? 'active' : ''; ?>" href="edit_about.php">
            <i class="bi bi-info-circle me-2"></i> About
        </a>
        <a class="nav-link <?php echo $current_page == 'edit_vision_mission.php' ? 'active' : ''; ?>" href="edit_vision_mission.php">
            <i class="bi bi-bullseye me-2"></i> Vision & Mission
        </a>
        <a class="nav-link <?php echo $current_page == 'edit_services.php' ? 'active' : ''; ?>" href="edit_services.php">
            <i class="bi bi-briefcase me-2"></i> Services
        </a>
        <a class="nav-link <?php echo $current_page == 'manage_products.php' ? 'active' : ''; ?>" href="manage_products.php">
            <i class="bi bi-grid-3x3-gap me-2"></i> Manage Products
        </a>
        <a class="nav-link <?php echo $current_page == 'manage_categories.php' ? 'active' : ''; ?>" href="manage_categories.php">
            <i class="bi bi-tags me-2"></i> Categories
        </a>
        <a class="nav-link <?php echo $current_page == 'edit_testimonials.php' ? 'active' : ''; ?>" href="edit_testimonials.php">
            <i class="bi bi-chat-quote me-2"></i> Testimonials
        </a>
        <a class="nav-link <?php echo $current_page == 'edit_blogs.php' ? 'active' : ''; ?>" href="edit_blogs.php">
            <i class="bi bi-journal-text me-2"></i> Blogs
        </a>
        <a class="nav-link <?php echo $current_page == 'edit_gallery.php' ? 'active' : ''; ?>" href="edit_gallery.php">
            <i class="bi bi-images me-2"></i> Gallery
        </a>
        <a class="nav-link <?php echo $current_page == 'edit_clients.php' ? 'active' : ''; ?>" href="edit_clients.php">
            <i class="bi bi-people me-2"></i> Clients
        </a>
        <a class="nav-link <?php echo $current_page == 'manage_careers.php' ? 'active' : ''; ?>" href="manage_careers.php">
            <i class="bi bi-person-badge me-2"></i> Careers
        </a>
        <a class="nav-link <?php echo $current_page == 'manage_notifications.php' ? 'active' : ''; ?>" href="manage_notifications.php">
            <i class="bi bi-bell me-2"></i> Notifications
        </a>
        <a class="nav-link <?php echo $current_page == 'edit_topheader.php' ? 'active' : ''; ?>" href="edit_topheader.php">
            <i class="bi bi-info-square me-2"></i> Top Header
        </a>
        <a class="nav-link <?php echo $current_page == 'edit_navbar.php' ? 'active' : ''; ?>" href="edit_navbar.php">
            <i class="bi bi-list me-2"></i> Navbar
        </a>
        <a class="nav-link <?php echo $current_page == 'change_password.php' ? 'active' : ''; ?>" href="change_password.php">
            <i class="bi bi-shield-lock me-2"></i> Security
        </a>
        <a class="nav-link <?php echo $current_page == 'edit_seo.php' ? 'active' : ''; ?>" href="edit_seo.php">
            <i class="bi bi-search me-2"></i> SEO Settings
        </a>
        <hr class="border-secondary">
        <a class="nav-link text-danger mt-auto" href="logout.php">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
    </nav>
</div>
