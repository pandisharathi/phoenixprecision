<?php 
// Main Index File for Phoenix Precision Products
require_once 'config/db.php';

// Fetch active notification
$notifStmt = $pdo->query("SELECT image_path FROM notifications WHERE status = 1 LIMIT 1");
$activeNotif = $notifStmt->fetch();
$notifImage = $activeNotif ? $activeNotif['image_path'] : '';

include 'includes/header_meta.php';
// include 'includes/topheader.php';
include 'includes/navbar.php';
include 'includes/hero.php';
include 'includes/vision_mission.php';
include 'includes/clients.php';
include 'includes/services.php';
include 'includes/projects.php';
include 'includes/testimonials.php';
include 'includes/blogs.php';
include 'includes/about.php';
include 'includes/map.php';
include 'includes/footer.php';
include 'includes/modal.php';
include 'includes/scripts.php';
?>
