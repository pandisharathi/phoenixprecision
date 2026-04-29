<?php
require_once 'config/db.php';
include 'includes/header_meta.php';
// include 'includes/topheader.php'; // Top header is hidden globally
include 'includes/navbar.php';

// Fetch gallery images
$stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
$galleryItems = $stmt->fetchAll();
?>

<!-- GLightbox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

<!-- Gallery Hero Section -->
<section class="gallery-hero bg-primary text-white py-5 text-center" style="background: var(--primary-gradient) !important;">
    <div class="container py-4">
        <h1 class="fw-bold animate__animated animate__fadeInDown">Photo Gallery</h1>
        <p class="lead opacity-75">Glimpses of our precision, process, and products.</p>
    </div>
</section>

<!-- Gallery Grid Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($galleryItems as $item): ?>
            <div class="col-sm-6 col-md-4 col-lg-3 reveal">
                <a href="<?php echo htmlspecialchars($item['image']); ?>" class="glightbox gallery-link" data-gallery="gallery1" data-title="<?php echo htmlspecialchars($item['title']); ?>" data-description="<?php echo htmlspecialchars($item['description']); ?>">
                    <div class="gallery-card">
                        <div class="gallery-img-wrapper">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="img-fluid">
                            <div class="gallery-overlay">
                                <div class="gallery-info text-white p-3 text-center">
                                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($item['title']); ?></h5>
                                    <?php if ($item['description']): ?>
                                    <p class="small mb-0 opacity-75"><?php echo htmlspecialchars($item['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>

            <?php if (count($galleryItems) == 0): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">No images found in the gallery.</p>
                <a href="index.php" class="btn btn-primary rounded-pill px-4">Back to Home</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php 
include 'includes/footer.php';
include 'includes/modal.php';
include 'includes/scripts.php';
?>

<!-- GLightbox JS -->
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
<script>
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
    });
</script>
