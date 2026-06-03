<?php
// Database Configuration
// $host = 'localhost';
// $user = 'jeyaadharsh_phoenixprecision';
// $pass = '1vT7ueBT8)';
// $db   = 'jeyaadharsh_phoenixprecision';

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'jeyaadharsh_phoenixprecision';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db`");

    // Check if critical tables exist, if not, attempt to run setup.sql
    $tables = ['admin_users', 'hero_slides', 'services', 'projects', 'testimonials', 'blogs', 'about_content', 'top_header_info', 'navbar_links', 'contact_submissions'];
    $needsSetup = false;
    foreach ($tables as $table) {
        if ($pdo->query("SHOW TABLES LIKE '$table'")->rowCount() == 0) {
            $needsSetup = true;
            break;
        }
    }

    if ($needsSetup) {
        $sqlFile = dirname(__DIR__) . '/sql/setup.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            // Simple split by ; - works for this specific setup.sql
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $stmt) {
                if (!empty($stmt)) {
                    $pdo->exec($stmt);
                }
            }
        }
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>