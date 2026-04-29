    <!-- Navbar -->
    <?php
$stmt = $pdo->query("SELECT * FROM navbar_links WHERE is_active = 1 ORDER BY sort_order ASC");
$navLinks = $stmt->fetchAll();
?>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="phoenix_logo.png" alt="Phoenix Precision Logo" class="img-fluid me-2" style="max-height: 60px;">
                <!-- <span>Phoenix <span class="text-primary">Precision</span></span> -->
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php foreach ($navLinks as $link): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $link['url']; ?>"><?php echo htmlspecialchars($link['label']); ?></a>
                    </li>
                    <?php
endforeach; ?>
                </ul>
                <div class="ms-lg-4">
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quoteModal">Get a Quote</a>
                </div>
            </div>
        </div>
    </nav>
