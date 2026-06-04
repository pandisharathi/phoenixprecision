<?php
require_once 'config/db.php';
include 'includes/header_meta.php';
include 'includes/navbar.php';

// Fetch Active Categories
$catStmt = $pdo->query("SELECT * FROM project_categories WHERE status = 'active' ORDER BY name ASC");
$active_categories = $catStmt->fetchAll();

// Fetch all active products
$stmt = $pdo->query("SELECT * FROM projects WHERE status = 'active' ORDER BY sort_order ASC");
$projects = $stmt->fetchAll();
?>

<!-- Products Hero Section -->
<section class="gallery-hero bg-primary text-white py-5 text-center" style="background: var(--primary-gradient) !important;">
    <div class="container py-4">
        <h1 class="fw-bold animate__animated animate__fadeInDown">Our Products</h1>
        <p class="lead opacity-75">Explore our comprehensive range of precision manufactured products.</p>
    </div>
</section>

<!-- Products Section -->
<section id="projects" class="bg-light py-5">
    <div class="container">
        <div class="row mb-5 reveal">
            <div class="col-12 text-center">
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <button class="btn btn-outline-primary filter-btn active rounded-pill px-4"
                        data-filter="all">All</button>
                    <?php foreach ($active_categories as $cat): ?>
                        <button class="btn btn-outline-primary filter-btn rounded-pill px-4 d-flex align-items-center gap-2"
                            data-filter="<?php echo htmlspecialchars($cat['name']); ?>">
                            <?php if ($cat['image']): ?>
                                <img src="<?php echo $cat['image']; ?>"
                                    style="width: 24px; height: 24px; object-fit: cover; border-radius: 50%;">
                            <?php endif; ?>
                            <?php echo ucfirst(htmlspecialchars($cat['name'])); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row g-4 project-container reveal">
            <?php foreach ($projects as $project): ?>
                <div class="col-lg-4 col-md-6 project-item" data-category="<?php echo $project['category']; ?>">
                    <a href="product_details.php?id=<?php echo $project['id']; ?>" class="text-decoration-none">
                        <div class="project-card">
                            <img src="<?php echo $project['image']; ?>"
                                alt="<?php echo htmlspecialchars($project['title']); ?>" loading="lazy">
                            <div class="project-overlay">
                                <span class="project-date"><?php echo htmlspecialchars($project['date_label']); ?></span>
                                <h4 class="project-title text-white"><?php echo htmlspecialchars($project['title']); ?></h4>
                                <p class="small text-light"><?php 
                                    $desc = strip_tags($project['description']);
                                    echo htmlspecialchars(strlen($desc) > 80 ? substr($desc, 0, 80) . '...' : $desc); 
                                ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>

            <?php if (count($projects) == 0): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">No products found.</p>
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
