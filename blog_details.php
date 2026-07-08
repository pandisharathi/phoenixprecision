<?php 
require_once 'config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: index.php#blogs');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ? AND status = 'show'");
$stmt->execute([$id]);
$blog = $stmt->fetch();

if (!$blog) {
    header('Location: index.php#blogs');
    exit;
}

// Fetch recent blogs for the right sidebar
$recentStmt = $pdo->prepare("SELECT id, title, image, created_at FROM blogs WHERE status = 'show' AND id != ? ORDER BY created_at DESC LIMIT 3");
$recentStmt->execute([$id]);
$recentBlogs = $recentStmt->fetchAll();

// Fetch related products (e.g., latest 4 active projects)
$prodStmt = $pdo->query("SELECT * FROM projects WHERE status = 'active' ORDER BY id DESC LIMIT 4");
$products = $prodStmt->fetchAll();

// Setup variables that header_meta.php might use
$page_title = htmlspecialchars($blog['title']) . " - Phoenix Precision Products";

include 'includes/header_meta.php';
include 'includes/navbar.php';
?>

<style>
    .blog-details-header {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .blog-details-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
    }
    .blog-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #4a5568;
    }
    
    .sidebar-widget {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }
    .recent-post-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
        transition: transform 0.2s;
    }
    .recent-post-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    .recent-post-item:hover {
        transform: translateX(5px);
    }
    .recent-post-img {
        width: 80px;
        height: 65px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .related-product-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .related-product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.12);
    }
    .related-product-img {
        height: 200px;
        object-fit: contain;
        padding: 1rem;
        background-color: #f8f9fa;
    }
    
    .badge-custom {
        background: linear-gradient(135deg, #0d6efd, #0dcaf0);
        color: white;
        padding: 0.5em 1em;
        border-radius: 50px;
        font-weight: 500;
    }
    
    @media (min-width: 992px) {
        .sticky-sidebar {
            position: -webkit-sticky;
            position: sticky;
            top: 100px;
            z-index: 10;
        }
    }
</style>

<main class="mt-5 bg-light">
    <div class="container pt-3 pb-5">
        
        <!-- Back Button & Breadcrumb -->
        <div class="d-flex flex-column flex-md-row align-items-md-center mb-4 gap-3">
            <a href="index.php#blogs" class="btn btn-outline-primary rounded-pill px-4 py-2 shadow-sm fw-bold" style="transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; width: fit-content;">
                <i class="bi bi-arrow-left"></i> Back to Blogs
            </a>
            <nav aria-label="breadcrumb" class="m-0 ms-md-3">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php#blogs" class="text-decoration-none">Blogs</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($blog['title']); ?></li>
                </ol>
            </nav>
        </div>

        <div class="row g-5">
            <!-- Left Side: Main Blog Content -->
            <div class="col-lg-8">
                <article class="bg-white p-4 p-md-5 rounded-4 shadow-sm mb-5">
                    
                    <div class="mb-4">
                        <span class="badge-custom mb-3 d-inline-block"><?php echo htmlspecialchars($blog['category']); ?></span>
                        <h1 class="fw-bold text-dark mb-3" style="font-size: 2.5rem;"><?php echo htmlspecialchars($blog['title']); ?></h1>
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bi bi-calendar-event me-2"></i> 
                            <?php echo date('F j, Y', strtotime($blog['created_at'])); ?>
                            <span class="mx-3">|</span>
                            <i class="bi bi-person-circle me-2"></i> Admin
                        </div>
                    </div>
                    
                    <div class="blog-details-header">
                        <img src="<?php echo $blog['image'] ? $blog['image'] : 'https://via.placeholder.com/800x400'; ?>" 
                             alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                             class="blog-details-image">
                    </div>
                    
                    <div class="blog-content">
                        <!-- Summary -->
                        <p class="lead fw-normal text-dark mb-4">
                            <em><?php echo nl2br(htmlspecialchars($blog['summary'])); ?></em>
                        </p>
                        
                        <!-- Full Content -->
                        <div>
                            <?php echo nl2br(htmlspecialchars($blog['content'])); ?>
                        </div>
                    </div>
                    
                    <!-- Social Share Buttons -->
                    <div class="mt-5 pt-4 border-top">
                        <h6 class="fw-bold mb-3">Share this post:</h6>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-outline-primary rounded-circle" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="btn btn-outline-info rounded-circle" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="btn btn-outline-danger rounded-circle" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-pinterest"></i></a>
                            <a href="#" class="btn btn-outline-secondary rounded-circle" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-envelope"></i></a>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Right Side: Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <!-- About Us Widget -->
                    <div class="sidebar-widget mb-4">
                    <h5 class="fw-bold mb-3 pb-2 border-bottom">About Us</h5>
                    <p class="text-muted small mb-0">Phoenix Precision Products is a trusted leader in providing top-tier industrial solutions and products. We focus on innovation, quality, and guaranteed satisfaction.</p>
                </div>
                
                <!-- Recent Blogs Widget -->
                <div class="sidebar-widget">
                    <h5 class="fw-bold mb-4 pb-2 border-bottom">Recent Blogs</h5>
                    
                    <?php if(empty($recentBlogs)): ?>
                        <p class="text-muted small">No other recent posts found.</p>
                    <?php else: ?>
                        <div class="recent-posts-container">
                            <?php foreach($recentBlogs as $rb): ?>
                                <a href="blog_details.php?id=<?php echo $rb['id']; ?>" class="text-decoration-none text-dark">
                                    <div class="recent-post-item">
                                        <img src="<?php echo $rb['image'] ? $rb['image'] : 'https://via.placeholder.com/80x65'; ?>" 
                                             class="recent-post-img" alt="<?php echo htmlspecialchars($rb['title']); ?>">
                                        <div>
                                            <h6 class="mb-1 fw-semibold" style="font-size: 0.95rem; line-height: 1.3;"><?php echo htmlspecialchars($rb['title']); ?></h6>
                                            <small class="text-muted"><i class="bi bi-clock me-1"></i><?php echo date('M d, Y', strtotime($rb['created_at'])); ?></small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Categories Widget (Static example) -->
                <div class="sidebar-widget mt-4">
                    <h5 class="fw-bold mb-3 pb-2 border-bottom">Popular Tags</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark border py-2 px-3 rounded-pill">Industrial</span>
                        <span class="badge bg-light text-dark border py-2 px-3 rounded-pill">Innovation</span>
                        <span class="badge bg-light text-dark border py-2 px-3 rounded-pill">Machinery</span>
                        <span class="badge bg-light text-dark border py-2 px-3 rounded-pill">Updates</span>
                    </div>
                </div>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Related Products Section (Bottom) -->
    <?php if(!empty($products)): ?>
    <div class="bg-white py-5 mt-4">
        <div class="container">
            <h3 class="fw-bold mb-4 text-center">Our Related Products</h3>
            <p class="text-center text-muted mb-5">Explore some of our latest and greatest precision products tailored for you.</p>
            
            <div class="row g-4">
                <?php foreach($products as $prod): ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="card related-product-card h-100">
                            <img src="<?php echo $prod['image'] ? trim($prod['image'], '/') : 'https://via.placeholder.com/400x300'; ?>" 
                                 class="card-img-top related-product-img" 
                                 alt="<?php echo htmlspecialchars($prod['title']); ?>">
                            <div class="card-body">
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-2"><?php echo htmlspecialchars($prod['category']); ?></span>
                                <h5 class="card-title fw-bold" style="font-size: 1.1rem;"><?php echo htmlspecialchars($prod['title']); ?></h5>
                                <p class="card-text text-muted small mb-0">
                                    <?php 
                                        $plainDesc = strip_tags($prod['description']);
                                        $plainDesc = str_replace('&nbsp;', ' ', $plainDesc);
                                        echo htmlspecialchars(mb_strlen($plainDesc) > 70 ? mb_substr($plainDesc, 0, 70) . '...' : $plainDesc);
                                    ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 pb-3">
                                <a href="index.php#projects" class="btn btn-outline-dark btn-sm w-100 fw-medium">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-5">
                <a href="index.php#projects" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm">View All Products <i class="bi bi-arrow-right ms-2"></i></a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
</main>

<?php
include 'includes/modal.php';
include 'includes/footer.php';
include 'includes/scripts.php';
?>
