<!-- Features Section -->
<?php
$stmt = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC");
$services = $stmt->fetchAll();
?>
<section id="services" class="bg-light">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <h2 class="fw-bold">Our Core Expertise</h2>
            <div class="mx-auto bg-primary" style="width: 60px; height: 3px; border-radius: 3px;"></div>
            <p class="text-muted mt-3">lies in delivering high-precision honing and super-finishing solutions that meet
                the most demanding quality standards. With strong technical know-how, advanced machinery, and a skilled
                workforce, we support customers across industries by improving component accuracy, surface finish, and
                performance. Every solution is driven by precision, consistency, and a deep understanding of customer
                requirements.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($services as $service): ?>
                <div class="col-lg-4 col-md-6 reveal">
                    <div class="feature-card">
                        <div class="feature-img-wrapper">
                            <img src="<?php echo $service['image']; ?>"
                                alt="<?php echo htmlspecialchars($service['title']); ?>">
                        </div>
                        <div class="feature-body text-center">
                            <h4><?php echo htmlspecialchars($service['title']); ?></h4>
                            <p class="text-muted"><?php echo htmlspecialchars($service['description']); ?></p>
                            <a href="#" class="btn btn-outline-primary rounded-pill px-4">Learn More</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>