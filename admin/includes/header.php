<header class="content-header">
    <div class="d-flex align-items-center">
        <button class="btn btn-sm btn-outline-primary d-lg-none me-3" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>
        <h1 class="h4 fw-bold mb-0"><?php echo isset($page_title) ? $page_title : 'Administrator Dashboard'; ?></h1>
    </div>
    <div class="d-flex align-items-center">
        <div class="d-none d-md-block me-3 text-muted small">
            Welcome, <strong><?php echo $_SESSION['username']; ?></strong>
        </div>
        <div class="btn-group">
            <?php if (basename($_SERVER['PHP_SELF']) != 'index.php'): ?>
                <a href="index.php" class="btn btn-sm btn-outline-secondary">Dashboard</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>
    </div>
</header>
