<?php
/**
 * Migration: job_vacancies table
 * Run this file in browser: http://localhost/phoenixprecision/sql/migration_careers.php
 * 
 * Action: ?action=up   → Creates the table (safe, won't drop existing data)
 *         ?action=down → Drops the table
 *         ?action=status → Shows current status
 */

require_once dirname(__DIR__) . '/config/db.php';

$action = $_GET['action'] ?? 'status';
$messages = [];
$errors   = [];

// ─── UP ──────────────────────────────────────────────────────────────────────
if ($action === 'up') {
    $sql = "
        CREATE TABLE IF NOT EXISTS `job_vacancies` (
            `id`               INT AUTO_INCREMENT PRIMARY KEY,
            `title`            VARCHAR(255) NOT NULL,
            `department`       VARCHAR(100) DEFAULT NULL,
            `location`         VARCHAR(100) DEFAULT NULL,
            `job_type`         ENUM('Full-Time','Part-Time','Contract','Internship','Remote') DEFAULT 'Full-Time',
            `experience`       VARCHAR(100) DEFAULT NULL,
            `qualification`    VARCHAR(255) DEFAULT NULL,
            `salary_range`     VARCHAR(100) DEFAULT NULL,
            `last_date`        DATE DEFAULT NULL,
            `description`      TEXT DEFAULT NULL,
            `responsibilities` TEXT DEFAULT NULL,
            `requirements`     TEXT DEFAULT NULL,
            `is_featured`      TINYINT(1) DEFAULT 0,
            `status`           ENUM('active','inactive') DEFAULT 'active',
            `sort_order`       INT DEFAULT 0,
            `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    try {
        $pdo->exec($sql);
        $messages[] = '✅ Table <strong>job_vacancies</strong> created successfully (or already exists).';

        // Seed one sample vacancy if table is empty
        $count = $pdo->query("SELECT COUNT(*) FROM job_vacancies")->fetchColumn();
        if ($count == 0) {
            $pdo->exec("
                INSERT INTO job_vacancies
                    (title, department, location, job_type, experience, qualification, salary_range, last_date, description, responsibilities, requirements, is_featured, status, sort_order)
                VALUES
                    ('Precision Machinist', 'Production', 'Chennai, India', 'Full-Time', '2–4 Years', 'ITI / Diploma in Mechanical', '₹20,000 – ₹30,000 / month', DATE_ADD(CURDATE(), INTERVAL 30 DAY),
                     'We are looking for a skilled Precision Machinist to join our manufacturing team.',
                     'Operate CNC/VMC machines\nRead and interpret engineering drawings\nEnsure quality standards are met\nMaintain machine log books',
                     'ITI in Turner/Machinist or Diploma in Mechanical\nExperience with CNC programming preferred\nKnowledge of GD&T is an advantage',
                     1, 'active', 1),

                    ('Quality Inspector', 'Quality', 'Chennai, India', 'Full-Time', '1–3 Years', 'Diploma / B.E. in Mechanical', '₹18,000 – ₹25,000 / month', DATE_ADD(CURDATE(), INTERVAL 30 DAY),
                     'We need a detail-oriented Quality Inspector to ensure product quality at every stage.',
                     'Inspect finished components using gauges and CMM\nDocument inspection reports\nLiaise with production team on non-conformances',
                     'Knowledge of measurement tools (vernier, micrometer, etc.)\nFamiliarity with ISO 9001 standards\nBasic computer skills',
                     0, 'active', 2);
            ");
            $messages[] = '✅ Sample vacancies seeded.';
        }
    } catch (PDOException $e) {
        $errors[] = '❌ Error: ' . $e->getMessage();
    }
}

// ─── DOWN ─────────────────────────────────────────────────────────────────────
if ($action === 'down') {
    try {
        $pdo->exec("DROP TABLE IF EXISTS `job_vacancies`");
        $messages[] = '🗑️ Table <strong>job_vacancies</strong> dropped successfully.';
    } catch (PDOException $e) {
        $errors[] = '❌ Error: ' . $e->getMessage();
    }
}

// ─── STATUS ───────────────────────────────────────────────────────────────────
$tableExists = $pdo->query("SHOW TABLES LIKE 'job_vacancies'")->rowCount() > 0;
$rowCount    = $tableExists ? $pdo->query("SELECT COUNT(*) FROM job_vacancies")->fetchColumn() : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Careers Migration — Phoenix Precision</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:700px">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4 py-3">
            <h4 class="mb-0"><i class="bi bi-database-gear me-2"></i>Careers Migration — job_vacancies</h4>
        </div>
        <div class="card-body p-4">

            <?php foreach ($messages as $m): ?>
                <div class="alert alert-success"><?= $m ?></div>
            <?php endforeach; ?>
            <?php foreach ($errors as $e): ?>
                <div class="alert alert-danger"><?= $e ?></div>
            <?php endforeach; ?>

            <h6 class="fw-bold">Current Status</h6>
            <table class="table table-bordered table-sm mb-4">
                <tr>
                    <td>Table <code>job_vacancies</code></td>
                    <td><?= $tableExists ? '<span class="badge bg-success">Exists</span>' : '<span class="badge bg-secondary">Not Created</span>' ?></td>
                </tr>
                <tr>
                    <td>Total Vacancies</td>
                    <td><strong><?= $rowCount ?></strong></td>
                </tr>
            </table>

            <h6 class="fw-bold">Run Migration</h6>
            <div class="d-flex gap-3 flex-wrap">
                <a href="?action=up" class="btn btn-success">
                    <i class="bi bi-arrow-up-circle me-1"></i> Run UP (Create Table)
                </a>
                <a href="?action=down" class="btn btn-danger"
                   onclick="return confirm('This will permanently DROP the job_vacancies table and all data. Continue?')">
                    <i class="bi bi-arrow-down-circle me-1"></i> Run DOWN (Drop Table)
                </a>
                <a href="?action=status" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh Status
                </a>
            </div>

            <hr class="mt-4">
            <h6 class="fw-bold">Table Schema</h6>
            <pre class="bg-dark text-light p-3 rounded small">
CREATE TABLE job_vacancies (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  title            VARCHAR(255) NOT NULL,
  department       VARCHAR(100),
  location         VARCHAR(100),
  job_type         ENUM('Full-Time','Part-Time','Contract','Internship','Remote'),
  experience       VARCHAR(100),
  qualification    VARCHAR(255),
  salary_range     VARCHAR(100),
  last_date        DATE,
  description      TEXT,
  responsibilities TEXT,
  requirements     TEXT,
  is_featured      TINYINT(1) DEFAULT 0,
  status           ENUM('active','inactive') DEFAULT 'active',
  sort_order       INT DEFAULT 0,
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);</pre>

            <a href="../admin/manage_careers.php" class="btn btn-outline-primary me-2">
                <i class="bi bi-shield-lock me-1"></i> Go to Admin Panel
            </a>
            <a href="../careers.php" class="btn btn-outline-dark">
                <i class="bi bi-globe me-1"></i> View Public Careers Page
            </a>
        </div>
    </div>
</div>
</body>
</html>
