<?php
/**
 * process_application.php
 * Handles career application form submissions from careers.php
 */
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: careers.php');
    exit;
}

$name       = trim($_POST['applicant_name']    ?? '');
$email      = trim($_POST['applicant_email']   ?? '');
$phone      = trim($_POST['applicant_phone']   ?? '');
$experience = trim($_POST['applicant_experience'] ?? '');
$cover      = trim($_POST['cover_note']        ?? '');
$job_id     = intval($_POST['job_id']          ?? 0);
$job_title  = trim($_POST['job_title_hidden']  ?? 'General Application');

// Basic validation
if (!$name || !$email || !$phone) {
    header('Location: careers.php?apply_error=missing_fields#applyModal');
    exit;
}

// Handle resume upload
$resume_path = '';
if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
    $allowed_ext  = ['pdf', 'doc', 'docx'];
    $max_size     = 5 * 1024 * 1024; // 5 MB
    $file_tmp     = $_FILES['resume']['tmp_name'];
    $file_name    = $_FILES['resume']['name'];
    $file_size    = $_FILES['resume']['size'];
    $file_ext     = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_ext)) {
        header('Location: careers.php?apply_error=invalid_file#vacancies');
        exit;
    }
    if ($file_size > $max_size) {
        header('Location: careers.php?apply_error=file_too_large#vacancies');
        exit;
    }

    $upload_dir = 'uploads/resumes/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $new_name    = 'resume_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
    $destination = $upload_dir . $new_name;

    if (move_uploaded_file($file_tmp, $destination)) {
        $resume_path = $destination;
    }
}

// Check if applications table exists; create if not
$tableExists = $pdo->query("SHOW TABLES LIKE 'job_applications'")->rowCount() > 0;
if (!$tableExists) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `job_applications` (
            `id`                 INT AUTO_INCREMENT PRIMARY KEY,
            `job_vacancy_id`     INT DEFAULT NULL,
            `job_title`          VARCHAR(255),
            `applicant_name`     VARCHAR(150) NOT NULL,
            `applicant_email`    VARCHAR(150) NOT NULL,
            `applicant_phone`    VARCHAR(30)  NOT NULL,
            `applicant_experience` VARCHAR(255),
            `cover_note`         TEXT,
            `resume_path`        VARCHAR(500),
            `status`             ENUM('new','shortlisted','rejected','hired') DEFAULT 'new',
            `created_at`         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}

// Insert application
$stmt = $pdo->prepare("
    INSERT INTO job_applications
        (job_vacancy_id, job_title, applicant_name, applicant_email, applicant_phone, applicant_experience, cover_note, resume_path, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new')
");
$stmt->execute([
    $job_id ?: null,
    $job_title,
    $name,
    $email,
    $phone,
    $experience,
    $cover,
    $resume_path,
]);

header('Location: careers.php?apply_success=1#vacancies');
exit;
