<!-- Footer -->
<?php
$stmt = $pdo->query("SELECT * FROM top_header_info WHERE id = 1");
$footerInfo = $stmt->fetch();
$stmtLinks = $pdo->query("SELECT * FROM navbar_links WHERE is_active = 1 ORDER BY sort_order ASC");
$footerLinks = $stmtLinks->fetchAll();
?>
<footer id="contact">
    <div class="container">
        <div class="row g-4 border-bottom border-secondary pb-5">
            <div class="col-lg-4">
                <a class="navbar-brand d-flex align-items-center mb-3" href="#" style="color: #fff !important;">
                    <img src="logo_11.png" alt="Phoenix Precision Logo" class="img-fluid me-2 rounded"
                        style="max-height: 80px;">
                    <!-- <span>Phoenix <span class="text-primary">Precision</span></span> -->
                </a>
                <p class="text-muted pe-lg-4">Looking for advanced precision honing solutions? Reach out to us to discuss your component requirements and experience how our honing services deliver superior accuracy and value.</p>
                <div class="footer-socials">
                    <?php if ($footerInfo['facebook_url']): ?><a href="<?php echo $footerInfo['facebook_url']; ?>"><i
                                class="bi bi-facebook"></i></a><?php
                    endif; ?>
                    <?php if ($footerInfo['instagram_url']): ?><a href="<?php echo $footerInfo['instagram_url']; ?>"><i
                                class="bi bi-instagram"></i></a><?php
                    endif; ?>
                    <?php if ($footerInfo['twitter_url']): ?><a href="<?php echo $footerInfo['twitter_url']; ?>"><i
                                class="bi bi-twitter-x"></i></a><?php
                    endif; ?>
                    <?php if ($footerInfo['whatsapp_url']): ?><a href="<?php echo $footerInfo['whatsapp_url']; ?>"><i
                                class="bi bi-whatsapp"></i></a><?php
                    endif; ?>
                    <?php if ($footerInfo['youtube_url']): ?><a href="<?php echo $footerInfo['youtube_url']; ?>"><i
                                class="bi bi-youtube"></i></a><?php
                    endif; ?>
                </div>
            </div>

            <div class="col-lg-2">
                <h5>Support</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#">Help Center</a></li>
                    <li class="mb-2"><a href="#">Privacy Policy</a></li>
                    <li class="mb-2"><a href="#">Terms of Use</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h5>Contact Us</h5>
                <?php if ($footerInfo['address']): ?>
                    <p class="text-muted mb-2"><i class="bi bi-geo-alt-fill text-primary me-2"></i>
                        <?php echo htmlspecialchars($footerInfo['address']); ?></p>
                    <?php
                endif; ?>
                <?php if ($footerInfo['phone']): ?>
                    <p class="text-muted mb-2"><i class="bi bi-telephone-fill text-primary me-2"></i>
                        <?php echo htmlspecialchars($footerInfo['phone']); ?></p>
                    <?php
                endif; ?>
                <?php if ($footerInfo['email']): ?>
                    <p class="text-muted mb-2"><i class="bi bi-envelope-at-fill text-primary me-2"></i>
                        <?php echo htmlspecialchars($footerInfo['email']); ?></p>
                    <?php
                endif; ?>
                <?php if (!empty($footerInfo['secondary_email'])): ?>
                    <p class="text-muted mb-2"><i class="bi bi-envelope-plus-fill text-primary me-2"></i>
                        <?php echo htmlspecialchars($footerInfo['secondary_email']); ?></p>
                    <?php
                endif; ?>
            </div>
        </div>
        <div class="text-center pt-4">
            <p class="text-muted small">&copy; <?php echo date('Y'); ?> <a href="https://phoenixprecision.in/" style="text-decoration: none; color: inherit;">Phoenix Precision</a>. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Floating WhatsApp Button -->
<?php if (!empty($footerInfo['whatsapp_url'])): ?>
    <a href="<?php echo htmlspecialchars($footerInfo['whatsapp_url']); ?>" class="floating-whatsapp" target="_blank"
        rel="noopener noreferrer">
        <i class="bi bi-whatsapp"></i>
    </a>
    <style>
        .floating-whatsapp {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 30px;
            right: 30px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 35px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .floating-whatsapp:hover {
            background-color: #128C7E;
            color: #FFF;
            transform: scale(1.1);
        }
    </style>
<?php endif; ?>