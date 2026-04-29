    <!-- Hero Slider -->
    <?php
$stmt = $pdo->query("SELECT * FROM hero_slides WHERE status = 'show' ORDER BY sort_order ASC");
$slides = $stmt->fetchAll();
?>
    <section id="home" class="p-0">
        <div id="heroCarousel" class="carousel slide hero-slider" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($slides as $index => $slide): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                     style="background-image: url('<?php echo $slide['bg_image']; ?>'); 
                            background-size: 100% 100% !important; 
                            background-position: center !important; 
                            background-repeat: no-repeat !important;">

                    <div class="hero-content container">
                        <!-- <div class="hero-card">
                            <h1><?php echo htmlspecialchars($slide['title']); ?></h1>
                            <p><?php echo htmlspecialchars($slide['description']); ?></p>
                            <?php if ($slide['btn_text']): ?>
                            <a href="<?php echo $slide['btn_link']; ?>" class="btn btn-primary btn-lg" 
                               <?php echo strpos($slide['btn_link'], '#quoteModal') !== false ? 'data-bs-toggle="modal" data-bs-target="#quoteModal"' : ''; ?>>
                               <?php echo htmlspecialchars($slide['btn_text']); ?>
                            </a>
                            <?php
    endif; ?>
                        </div> -->
                    </div>
                </div>
                <?php
endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </section>
