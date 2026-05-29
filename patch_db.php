<?php
require_once __DIR__ . '/config/db.php';

try {
    // 1. Add image column to job_vacancies if it doesn't exist
    $pdo->exec("ALTER TABLE job_vacancies ADD COLUMN image VARCHAR(255) NULL AFTER title");
    echo "Column 'image' added to job_vacancies.\n";
} catch (Exception $e) {
    echo "Column might already exist or error: " . $e->getMessage() . "\n";
}

try {
    // 2. Add 'Careers' to navbar_links if it doesn't exist
    $stmt = $pdo->prepare("SELECT id FROM navbar_links WHERE label = 'Careers'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $pdo->exec("INSERT INTO navbar_links (label, url, is_active, sort_order) VALUES ('Careers', 'careers.php', 1, 6)");
        echo "Added 'Careers' to navbar_links.\n";
    } else {
        echo "'Careers' already exists in navbar_links.\n";
    }
} catch (Exception $e) {
    echo "Error inserting into navbar_links: " . $e->getMessage() . "\n";
}
