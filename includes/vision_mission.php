<!-- Vision, Mission & Quality Policy Section -->
<?php
$stmt = $pdo->query("SELECT * FROM vision_mission");
$vm_data = [];
while ($row = $stmt->fetch()) {
    $vm_data[$row['type']] = $row;
}
?>
<section id="vision-mission" class="py-5 bg-light">
    <div class="container py-lg-4">
        <div class="row g-4 justify-content-center">
            <?php 
            $display_order = ['vision', 'mission', 'quality'];
            $icons = [
                'vision' => 'bi-eye',
                'mission' => 'bi-rocket',
                'quality' => 'bi-shield-check'
            ];
            
            foreach ($display_order as $type): 
                if (isset($vm_data[$type])):
                    $item = $vm_data[$type];
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift position-relative">
                    <?php if (!empty($item['image'])): ?>
                        <div class="position-relative" style="height: 200px; overflow: hidden bg-secondary">
                            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <!-- Adding an overlay to make the text pop -->
                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.3;"></div>
                        </div>
                    <?php else: ?>
                        <!-- Fallback colored header if no image -->
                        <div class="bg-primary text-white d-flex align-items-center justify-content-center" style="height: 120px;">
                            <i class="bi <?php echo $icons[$type]; ?>" style="font-size: 3rem;"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body p-4 p-lg-5 position-relative">
                        <?php if (!empty($item['image'])): ?>
                            <div class="icon-circle bg-primary text-white position-absolute top-0 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 70px; height: 70px; z-index: 2;">
                                <i class="bi <?php echo $icons[$type]; ?>" style="font-size: 2rem;"></i>
                            </div>
                            <div class="mt-4 pt-2">
                        <?php else: ?>
                            <div>
                        <?php endif; ?>
                            <h3 class="h4 fw-bold text-dark text-start mb-3"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p class="text-muted mb-0 text-start"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
    </div>
</section>

<style>
/* Custom styles for vision/mission cards */
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-10px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.15)!important;
}
.icon-circle {
    border: 4px solid #fff;
}
</style>
