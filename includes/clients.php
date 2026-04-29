<?php
$stmt = $pdo->query("SELECT * FROM clients ORDER BY sort_order ASC, created_at DESC");
$clients = $stmt->fetchAll();

if (count($clients) > 0):
?>
<!-- Clients Section -->
<section class="clients-section py-5 bg-white border-bottom border-top overflow-hidden">
    <div class="container mb-4 text-center">
        <h4 class="fw-bold text-uppercase small text-muted letter-spacing-2">Our Trusted Clients</h4>
    </div>
    
    <div class="clients-marquee-wrapper">
        <div class="clients-marquee-track custom-marquee-track">
            <?php 
            // Triplicating the list ensures a seamless infinite loop effect
            for ($i = 0; $i < 3; $i++): 
                foreach ($clients as $client): 
            ?>
            <div class="client-logo-item">
                <img src="<?php echo htmlspecialchars($client['logo']); ?>" alt="<?php echo htmlspecialchars($client['name']); ?>" title="<?php echo htmlspecialchars($client['name']); ?>">
            </div>
            <?php 
                endforeach;
            endfor; 
            ?>
        </div>
    </div>
</section>

<style>
.letter-spacing-2 {
    letter-spacing: 2px;
}
</style>

<?php endif; ?>
