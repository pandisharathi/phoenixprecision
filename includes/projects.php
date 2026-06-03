<!-- Projects Section -->
<?php
// Fetch Active Categories
$catStmt = $pdo->query("SELECT * FROM project_categories WHERE status = 'active' ORDER BY name ASC");
$active_categories = $catStmt->fetchAll();

// Fetch Projects using names of active categories and status
$catNames = array_column($active_categories, 'name');
if (!empty($catNames)) {
    $placeholders = implode(',', array_fill(0, count($catNames), '?'));
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE category IN ($placeholders) AND status = 'active' ORDER BY sort_order ASC");
    $stmt->execute($catNames);
    $projects = $stmt->fetchAll();
} else {
    $projects = [];
}
?>
<section id="projects" class="bg-white">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <h2 class="fw-bold">PRODUCTS WE MANUFACTURE</h2>
            <div class="mx-auto bg-primary" style="width: 60px; height: 3px; border-radius: 3px;"></div>
            <p class="text-muted mt-3">Innovative solutions delivered with precision and modern design standards.
                </p>
        </div>

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
                    <div class="project-card">
                        <img src="<?php echo $project['image']; ?>"
                            alt="<?php echo htmlspecialchars($project['title']); ?>">
                        <div class="project-overlay">
                            <span class="project-date"><?php echo htmlspecialchars($project['date_label']); ?></span>
                            <h4 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h4>
                            <p class="small"><?php echo htmlspecialchars($project['description']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>