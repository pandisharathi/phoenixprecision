<?php
// Fetch SEO Settings
$seoStmt = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
$siteSeo = $seoStmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteSeo['site_title'] ?? 'Phoenix Precision | Honing Business Services'); ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="favicon.png">
    
    <!-- Meta Tags for SEO -->
    <meta name="description" content="<?php echo htmlspecialchars($siteSeo['meta_description'] ?? ''); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($siteSeo['meta_keywords'] ?? ''); ?>">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
