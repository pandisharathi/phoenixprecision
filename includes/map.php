<?php
$stmt = $pdo->query("SELECT address FROM top_header_info WHERE id = 1");
$topInfo = $stmt->fetch();
$address = $topInfo['address'] ?? 'Phoenix Precision';
$lat = '13.04797';
$lng = '80.10281';
?>
<section class="map-section py-5 bg-light">
    <div class="container">
        <div class="card shadow border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0 position-relative">
                <!-- Address Overlay / Button -->
                <div class="position-absolute top-0 start-0 m-3 z-3 d-none d-md-block">
                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $lat . ',' . $lng; ?>" target="_blank" class="btn btn-white shadow-sm border rounded-pill px-4 fw-bold">
                        <i class="bi bi-geo-alt-fill text-primary me-2"></i> <?php echo htmlspecialchars($address); ?>
                    </a>
                </div>
                
                <!-- Google Map Iframe Wrapped in a Link for Clickability -->
                <div style="width: 100%; height: 500px; cursor: pointer;" onclick="window.open('https://www.google.com/maps/search/?api=1&query=<?php echo $lat . ',' . $lng; ?>', '_blank')">
                    <iframe src="https://maps.google.com/maps?q=<?php echo $lat . ',' . $lng; ?>(<?php echo urlencode($address); ?>)&hl=en&z=17&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                
                <!-- Mobile Click Overlay -->
                <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $lat . ',' . $lng; ?>" target="_blank" class="stretched-link d-md-none"></a>
            </div>
        </div>
    </div>
</section>
