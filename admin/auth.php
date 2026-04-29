<?php
session_start();
require_once '../config/db.php';

// Simple Auth Check
function checkAuth() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit();
    }
}
?>
