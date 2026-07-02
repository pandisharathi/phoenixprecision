<?php
require_once 'config/db.php';
include 'includes/header_meta.php';
include 'includes/navbar.php';

// Fetch Active Categories
$catStmt = $pdo->query("SELECT * FROM project_categories WHERE status = 'active' ORDER BY name ASC");
$active_categories = $catStmt->fetchAll();

// Fetch Active Subcategories
$subcatStmt = $pdo->query("SELECT * FROM project_subcategories WHERE status = 'active' ORDER BY name ASC");
$active_subcategories = $subcatStmt->fetchAll();

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
        <div class="row mb-4 reveal" id="subcategoryRow" style="display: none;">
            <div class="col-12 text-center">
                <div class="d-flex flex-wrap justify-content-center gap-3" id="subcategoryContainer">
                    <!-- Subcategories will be injected here -->
                </div>
            </div>
        </div>
        <div class="row g-4 project-container reveal">
            <?php foreach ($projects as $project): ?>
                <div class="col-lg-4 col-md-6 project-item" data-category="<?php echo htmlspecialchars($project['category']); ?>" data-subcategory="<?php echo htmlspecialchars($project['subcategory'] ?? ''); ?>">
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

            <!-- Empty State Placeholder -->
            <div class="col-12 text-center empty-state <?php echo count($projects) > 0 ? 'd-none' : ''; ?> py-5">
                <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=400&q=80" 
                     alt="Upload Soon" 
                     class="img-fluid rounded-circle mb-4 shadow-sm" 
                     style="max-width: 200px; height: 200px; object-fit: cover; border: 4px solid #f8f9fa;">
                <h3 class="fw-bold text-muted mb-2">Upload soon..!</h3>
                <p class="text-muted">We are currently preparing amazing products for this category. Stay tuned!</p>
            </div>
        </div>
    </div>
</section>

<?php 
include 'includes/footer.php';
include 'includes/modal.php';
include 'includes/scripts.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subcategories = <?php echo json_encode($active_subcategories); ?>;
    const projectItems = document.querySelectorAll('.project-item');
    const emptyState = document.querySelector('.empty-state');
    const subcategoryRow = document.getElementById('subcategoryRow');
    const subcategoryContainer = document.getElementById('subcategoryContainer');
    
    let currentCategory = 'all';
    let currentSubcategory = 'all';

    // Override the click listener for main category buttons attached by scripts.php
    // We clone the buttons to remove the old event listeners from scripts.php
    const oldFilterBtns = document.querySelectorAll('.filter-btn');
    oldFilterBtns.forEach(btn => {
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        
        newBtn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            currentCategory = this.getAttribute('data-filter');
            currentSubcategory = 'all'; // reset subcategory on category change
            
            updateSubcategoryUI(currentCategory);
            applyFilters();
        });
    });

    function updateSubcategoryUI(categoryName) {
        subcategoryContainer.innerHTML = '';
        
        if (categoryName === 'all') {
            subcategoryRow.style.display = 'none';
            return;
        }

        const relatedSubcats = subcategories.filter(s => s.category_name === categoryName);
        
        if (relatedSubcats.length > 0) {
            subcategoryRow.style.display = 'block';
            
            // Add 'All' button for subcategories
            const allBtn = document.createElement('button');
            allBtn.className = 'btn btn-sm btn-outline-secondary sub-filter-btn active rounded-pill px-3';
            allBtn.setAttribute('data-subfilter', 'all');
            allBtn.innerText = 'All';
            allBtn.onclick = handleSubcategoryClick;
            subcategoryContainer.appendChild(allBtn);

            relatedSubcats.forEach(sub => {
                const btn = document.createElement('button');
                btn.className = 'btn btn-sm btn-outline-secondary sub-filter-btn rounded-pill px-3 d-flex align-items-center gap-2';
                btn.setAttribute('data-subfilter', sub.name);
                
                if (sub.image) {
                    btn.innerHTML = `<img src="${sub.image}" style="width: 20px; height: 20px; object-fit: cover; border-radius: 50%;"> ${sub.name}`;
                } else {
                    btn.innerText = sub.name;
                }
                
                btn.onclick = handleSubcategoryClick;
                subcategoryContainer.appendChild(btn);
            });
        } else {
            subcategoryRow.style.display = 'none';
        }
    }

    function handleSubcategoryClick(e) {
        document.querySelectorAll('.sub-filter-btn').forEach(b => b.classList.remove('active'));
        e.currentTarget.classList.add('active');
        currentSubcategory = e.currentTarget.getAttribute('data-subfilter');
        applyFilters();
    }

    function applyFilters() {
        let visibleCount = 0;
        
        projectItems.forEach(item => {
            const itemCat = item.getAttribute('data-category');
            const itemSubcat = item.getAttribute('data-subcategory');
            
            const matchCategory = (currentCategory === 'all' || itemCat === currentCategory);
            const matchSubcategory = (currentSubcategory === 'all' || itemSubcat === currentSubcategory);
            
            if (matchCategory && matchSubcategory) {
                item.classList.remove('d-none');
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'scale(1)';
                }, 50);
                visibleCount++;
            } else {
                item.style.opacity = '0';
                item.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    item.classList.add('d-none');
                }, 300);
            }
        });
        
        if (emptyState) {
            if (visibleCount === 0) {
                setTimeout(() => {
                    emptyState.classList.remove('d-none');
                }, 300);
            } else {
                emptyState.classList.add('d-none');
            }
        }
    }
});
</script>
