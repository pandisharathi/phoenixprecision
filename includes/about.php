    <!-- About Section -->
    <?php
$stmt = $pdo->query("SELECT * FROM about_content WHERE id = 1");
$about = $stmt->fetch();
?>
    <section id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0 reveal">
                    <img src="<?php echo $about['image']; ?>" class="img-fluid rounded shadow-lg" alt="About Us">
                </div>
                <div class="col-lg-6 reveal">
                    <h2 class="fw-bold mb-3"><?php echo htmlspecialchars($about['title']); ?></h2>
                    <p class="lead text-primary fw-medium"><?php echo htmlspecialchars($about['lead_text']); ?></p>
                    <p class="text-muted"><?php echo htmlspecialchars($about['main_text']); ?></p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Precision Honing Services</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Quality & Process Control</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Custom Honing Solutions</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Quick Turnaround Time</li>
                    </ul>
                    <a href="#" class="btn btn-primary">Read More</a>
                </div>
            </div>
        </div>
    </section>
