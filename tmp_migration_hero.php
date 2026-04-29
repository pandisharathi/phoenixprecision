<?php
require_once 'config/db.php';

try {
    // Check if status column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM hero_slides LIKE 'status'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE hero_slides ADD COLUMN status VARCHAR(20) DEFAULT 'show' AFTER bg_image");
        // Migrate is_active values
        $pdo->exec("UPDATE hero_slides SET status = 'show' WHERE is_active = 1");
        $pdo->exec("UPDATE hero_slides SET status = 'hide' WHERE is_active = 0");
        echo "Column 'status' added and values migrated successfully for 'hero_slides' table.<br>";
    } else {
        echo "Column 'status' already exists in 'hero_slides' table.<br>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>