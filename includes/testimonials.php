<!-- Testimonials Section -->
<?php
$stmt = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order ASC");
$testimonials = $stmt->fetchAll();
?>
<section id="testimonials" class="bg-light overflow-hidden">
    <div class="container text-center reveal">
        <h2 class="fw-bold mb-5">Client Feedback</h2>
    <div class="row g-4 justify-content-center">
        <?php if (empty($testimonials)): ?>
            <div class="col-12">
                <p class="text-muted">No testimonials yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-6 col-lg-4 reveal">
                    <div class="testimonial-card h-100 shadow-sm border-0">
                        <div class="quote-icon mb-3">
                            <i class="bi bi-quote text-primary fs-1 opacity-25"></i>
                        </div>
                        <p class="testimonial-text mb-4">"<?php echo htmlspecialchars($testimonial['content']); ?>"</p>
                        <div class="testimonial-author d-flex align-items-center mt-auto">
                            <img src="<?php echo $testimonial['image']; ?>"
                                alt="<?php echo htmlspecialchars($testimonial['name']); ?>" 
                                class="rounded-circle me-3" 
                                style="width: 50px; height: 50px; object-fit: cover;">
                            <div class="text-start">
                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($testimonial['name']); ?></h6>
                                <span class="small text-muted"><?php echo htmlspecialchars($testimonial['position']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</section>