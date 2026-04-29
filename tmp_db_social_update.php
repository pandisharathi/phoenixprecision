<?php
include 'c:/xampp/htdocs/New_website/config/db.php';

try {
    // Add youtube_url column to top_header_info table
    $pdo->exec("ALTER TABLE top_header_info ADD COLUMN IF NOT EXISTS youtube_url VARCHAR(255) AFTER telegram_url");
    echo "youtube_url column added successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
