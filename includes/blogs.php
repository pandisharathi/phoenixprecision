<!-- Blogs Section -->
<?php
$stmt = $pdo->query("SELECT * FROM blogs WHERE status = 'show' ORDER BY created_at DESC LIMIT 3");
$blogs = $stmt->fetchAll();
?>
<section id="blogs" class="bg-white">
    <div class="container reveal">
        <h2 class="fw-bold text-center mb-5">Latest Blogs</h2>
        <div class="row g-4">
            <?php if (empty($blogs)): ?>
                <p class="text-center text-muted">No blog posts yet.</p>
            <?php else: ?>
                <?php foreach ($blogs as $blog): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="blog-card card h-100 border-0 shadow-sm">
                            <img src="<?php echo $blog['image']; ?>" class="card-img-top"
                                alt="<?php echo htmlspecialchars($blog['title']); ?>">
                            <div class="card-body">
                                <span
                                    class="text-primary small fw-bold"><?php echo htmlspecialchars($blog['category']); ?></span>
                                <h5 class="card-title mt-2"><?php echo htmlspecialchars($blog['title']); ?></h5>
                                <p class="card-text text-muted small"><?php echo htmlspecialchars($blog['summary']); ?></p>
                                <a href="blog_details.php?id=<?php echo $blog['id']; ?>" class="btn btn-link p-0 text-decoration-none fw-bold">Read More &rarr;</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>