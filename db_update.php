<?php
require_once 'config/db.php';

try {
    // Add subcategory column to projects if it doesn't exist
    $pdo->exec("ALTER TABLE projects ADD COLUMN subcategory VARCHAR(50) DEFAULT NULL");
    echo "Added subcategory column to projects table.\n";
} catch (PDOException $e) {
    // Ignore if column already exists
    echo "Column subcategory already exists or error: " . $e->getMessage() . "\n";
}

try {
    // Create project_subcategories table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS project_subcategories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category_name VARCHAR(50) NOT NULL,
            name VARCHAR(50) NOT NULL,
            image VARCHAR(255),
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Created project_subcategories table.\n";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
