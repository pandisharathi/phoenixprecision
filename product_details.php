<?php
require_once 'config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header("Location: products.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ? AND status = 'active'");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}

include 'includes/header_meta.php';
include 'includes/navbar.php';
?>

<!-- Product Details Section -->
<section class="py-5 bg-light" style="min-height: 80vh;">
    <div class="container py-5">
        <div class="mb-4 reveal">
            <a href="products.php" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-2"></i> Back to Products
            </a>
        </div>
        
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden reveal">
            <div class="row g-0">
                <div class="col-lg-6 position-relative overflow-hidden d-flex align-items-center justify-content-center bg-light p-0">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="img-fluid product-img-bg w-100 h-100" style="object-fit: contain; object-position: center; max-height: 500px; padding: 1rem; transition: transform 0.5s ease;">
                </div>
                <div class="col-lg-6 d-flex align-items-center bg-white">
                    <div class="card-body p-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary rounded-pill px-3 py-2 me-3 fs-6">
                                <?php echo htmlspecialchars(ucfirst($product['category'])); ?>
                            </span>
                            <span class="text-muted"><i class="bi bi-tag-fill me-2"></i> <?php echo htmlspecialchars($product['date_label']); ?></span>
                        </div>
                        
                        <h2 class="card-title fw-bold mb-4 animate__animated animate__fadeInUp" style="color: var(--primary-color); font-size: 2.5rem;">
                            <?php echo htmlspecialchars($product['title']); ?>
                        </h2>
                        
                        <div class="card-text text-muted mb-5 animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 1.1rem; line-height: 1.8;">
                            <?php 
                                if (strip_tags($product['description']) === $product['description']) {
                                    echo nl2br(htmlspecialchars($product['description']));
                                } else {
                                    echo $product['description'];
                                }
                            ?>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start animate__animated animate__fadeInUp animate__delay-2s">
                            <button type="button" class="btn btn-primary btn-lg rounded-pill px-5" data-bs-toggle="modal" data-bs-target="#quoteModal">
                                Request Quote <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .product-img-bg {
        transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .card:hover .product-img-bg {
        transform: scale(1.05);
    }
</style>

<?php 
include 'includes/footer.php';
include 'includes/modal.php';
include 'includes/scripts.php';
?>
