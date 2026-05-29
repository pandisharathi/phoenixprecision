    <!-- Top Header -->
    <?php
    $stmt = $pdo->query("SELECT * FROM top_header_info WHERE id = 1");
    $topInfo = $stmt->fetch();
    
    // Ensure WhatsApp URL is set to the correct number
    if ($topInfo['whatsapp_url'] !== 'https://wa.me/918778859130') {
        $pdo->exec("UPDATE top_header_info SET whatsapp_url = 'https://wa.me/918778859130' WHERE id = 1");
        $topInfo['whatsapp_url'] = 'https://wa.me/918778859130';
    }

    // Hidden globally as requested by user
    if (false):
    ?>
    <header class="top-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="contact-info">
                <?php if ($topInfo['address']): ?>
                <a href="https://maps.google.com/?q=<?php echo urlencode($topInfo['address']); ?>" target="_blank" class="me-3">
                    <i class="bi bi-geo-alt-fill me-1"></i> <span><?php 
                        $addr = $topInfo['address'];
                        if (strlen($addr) > 30) {
                            echo htmlspecialchars(substr($addr, 0, 15) . '...' . substr($addr, -15));
                        } else {
                            echo htmlspecialchars($addr);
                        }
                    ?></span>
                </a>
                <?php endif; ?>
                <?php if ($topInfo['phone']): ?>
                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $topInfo['phone']); ?>" class="me-3">
                    <i class="bi bi-telephone-fill me-1"></i> <span><?php echo htmlspecialchars($topInfo['phone']); ?></span>
                </a>
                <?php endif; ?>
                <?php if ($topInfo['email']): ?>
                <a href="mailto:<?php echo $topInfo['email']; ?>" class="me-3">
                    <i class="bi bi-envelope-fill me-1"></i> <span><?php echo htmlspecialchars($topInfo['email']); ?></span>
                </a>
                <?php endif; ?>
                <?php if (!empty($topInfo['secondary_email'])): ?>
                <a href="mailto:<?php echo $topInfo['secondary_email']; ?>">
                    <i class="bi bi-envelope-plus-fill me-1"></i> <span><?php echo htmlspecialchars($topInfo['secondary_email']); ?></span>
                </a>
                <?php endif; ?>
            </div>
            <div class="social-icons d-none d-md-flex">
                <?php if ($topInfo['facebook_url']): ?><a href="<?php echo $topInfo['facebook_url']; ?>"><i class="bi bi-facebook"></i></a><?php endif; ?>
                <?php if ($topInfo['instagram_url']): ?><a href="<?php echo $topInfo['instagram_url']; ?>"><i class="bi bi-instagram"></i></a><?php endif; ?>
                <?php if ($topInfo['twitter_url']): ?><a href="<?php echo $topInfo['twitter_url']; ?>"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
                <?php if ($topInfo['whatsapp_url']): ?><a href="<?php echo $topInfo['whatsapp_url']; ?>"><i class="bi bi-whatsapp"></i></a><?php endif; ?>
                <?php if ($topInfo['youtube_url']): ?><a href="<?php echo $topInfo['youtube_url']; ?>"><i class="bi bi-youtube"></i></a><?php endif; ?>
            </div>
        </div>
    </header>
    <?php endif; ?>
