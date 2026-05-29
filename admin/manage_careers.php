<?php
require_once 'auth.php';
checkAuth();
require_once '../config/db.php';

// ── Ensure tables exist ──────────────────────────────────────────────────────
$pdo->exec("
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS `job_applications` (
        `id`                   INT AUTO_INCREMENT PRIMARY KEY,
        `job_vacancy_id`       INT DEFAULT NULL,
        `job_title`            VARCHAR(255),
        `applicant_name`       VARCHAR(150) NOT NULL,
        `applicant_email`      VARCHAR(150) NOT NULL,
        `applicant_phone`      VARCHAR(30)  NOT NULL,
        `applicant_experience` VARCHAR(255),
        `cover_note`           TEXT,
        `resume_path`          VARCHAR(500),
        `status`               ENUM('new','shortlisted','rejected','hired') DEFAULT 'new',
        `created_at`           TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

$message = ''; $error = '';
$tab = $_GET['tab'] ?? 'vacancies'; // 'vacancies' | 'applications'

// ── VACANCY CRUD ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_vacancy'])) {
        $f = $_POST;
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                if (!is_dir('../uploads/vacancies')) mkdir('../uploads/vacancies', 0777, true);
                $new_name = 'job_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/vacancies/' . $new_name)) {
                    $image_path = 'uploads/vacancies/' . $new_name;
                }
            }
        }
        $stmt = $pdo->prepare("INSERT INTO job_vacancies
            (title,image,department,location,job_type,experience,qualification,salary_range,last_date,description,responsibilities,requirements,is_featured,status,sort_order)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $f['title'], $image_path, $f['department'], $f['location'], $f['job_type'],
            $f['experience'], $f['qualification'], $f['salary_range'],
            $f['last_date'] ?: null,
            $f['description'], $f['responsibilities'], $f['requirements'],
            isset($f['is_featured']) ? 1 : 0,
            $f['status'], intval($f['sort_order'])
        ]);
        header('Location: manage_careers.php?tab=vacancies&msg=added');
        exit;
    }

    // EDIT
    if (isset($_POST['edit_vacancy'])) {
        $f = $_POST;
        $image_path = $f['current_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                if (!is_dir('../uploads/vacancies')) mkdir('../uploads/vacancies', 0777, true);
                $new_name = 'job_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/vacancies/' . $new_name)) {
                    $image_path = 'uploads/vacancies/' . $new_name;
                }
            }
        }
        $stmt = $pdo->prepare("UPDATE job_vacancies SET
            title=?,image=?,department=?,location=?,job_type=?,experience=?,qualification=?,
            salary_range=?,last_date=?,description=?,responsibilities=?,requirements=?,
            is_featured=?,status=?,sort_order=? WHERE id=?");
        $stmt->execute([
            $f['title'], $image_path, $f['department'], $f['location'], $f['job_type'],
            $f['experience'], $f['qualification'], $f['salary_range'],
            $f['last_date'] ?: null,
            $f['description'], $f['responsibilities'], $f['requirements'],
            isset($f['is_featured']) ? 1 : 0,
            $f['status'], intval($f['sort_order']), intval($f['id'])
        ]);
        header('Location: manage_careers.php?tab=vacancies&msg=updated');
        exit;
    }

    // DELETE VACANCY
    if (isset($_POST['delete_vacancy'])) {
        $pdo->prepare("DELETE FROM job_vacancies WHERE id=?")->execute([intval($_POST['id'])]);
        header('Location: manage_careers.php?tab=vacancies&msg=deleted');
        exit;
    }

    // UPDATE APPLICATION STATUS
    if (isset($_POST['update_app_status'])) {
        $allowed = ['new','shortlisted','rejected','hired'];
        $status  = in_array($_POST['app_status'], $allowed) ? $_POST['app_status'] : 'new';
        $pdo->prepare("UPDATE job_applications SET status=? WHERE id=?")->execute([$status, intval($_POST['app_id'])]);
        header('Location: manage_careers.php?tab=applications&msg=app_updated');
        exit;
    }

    // DELETE APPLICATION
    if (isset($_POST['delete_application'])) {
        $pdo->prepare("DELETE FROM job_applications WHERE id=?")->execute([intval($_POST['id'])]);
        header('Location: manage_careers.php?tab=applications&msg=app_deleted');
        exit;
    }
}

// Messages
$msgs = [
    'added'       => ['success', 'Job vacancy added successfully!'],
    'updated'     => ['success', 'Job vacancy updated successfully!'],
    'deleted'     => ['success', 'Job vacancy deleted successfully!'],
    'app_updated' => ['success', 'Application status updated!'],
    'app_deleted' => ['success', 'Application deleted!'],
];
if (isset($_GET['msg']) && isset($msgs[$_GET['msg']])) {
    [$msgType, $msgText] = $msgs[$_GET['msg']];
}

// Data
$vacancies    = $pdo->query("SELECT * FROM job_vacancies ORDER BY sort_order ASC, created_at DESC")->fetchAll();
$applications = $pdo->query("SELECT a.*, v.title AS vacancy_title FROM job_applications a LEFT JOIN job_vacancies v ON a.job_vacancy_id = v.id ORDER BY a.created_at DESC")->fetchAll();

$totalVac     = count($vacancies);
$activeVac    = count(array_filter($vacancies, fn($v) => $v['status'] === 'active'));
$totalApps    = count($applications);
$newApps      = count(array_filter($applications, fn($a) => $a['status'] === 'new'));

$job_types = ['Full-Time','Part-Time','Contract','Internship','Remote'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Careers — Phoenix Precision Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="css/admin-custom.css">
<style>
:root { --brand:#d62828; --brand-light:rgba(214,40,40,.08); }
.stat-chip {
    border-radius: 14px; padding: 1.2rem 1.4rem;
    border: 1px solid #e5e7eb; background: #fff;
    display: flex; align-items: center; gap: 1rem;
}
.stat-chip .icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem; }
.stat-chip .num  { font-size:1.6rem;font-weight:800;line-height:1; }
.stat-chip .lbl  { font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;color:#6b7280; }

.nav-pills-custom .nav-link { border-radius:50px;padding:.45rem 1.2rem;font-size:.88rem;font-weight:500;color:#374151;border:1px solid #e5e7eb;margin-right:.4rem; }
.nav-pills-custom .nav-link.active { background:var(--brand);color:#fff;border-color:var(--brand); }

.job-row td { vertical-align:middle; }
.badge-type { font-size:.72rem;padding:.3em .7em;border-radius:50px; }
.status-dot { width:8px;height:8px;border-radius:50%;display:inline-block;margin-right:5px; }

/* Modal tweaks */
.modal-form .form-label { font-size:.85rem;font-weight:600;margin-bottom:.25rem; }
.modal-form .form-control, .modal-form .form-select { border-radius:10px;font-size:.9rem; }
.modal-form .form-control:focus,.modal-form .form-select:focus { border-color:var(--brand);box-shadow:0 0 0 3px rgba(214,40,40,.12); }
.modal-form .section-head { font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;color:var(--brand);font-weight:700;margin-bottom:.5rem;margin-top:1.2rem;border-bottom:1px solid #f0f0f0;padding-bottom:.3rem; }

.app-card { border-radius:14px;border:1px solid #e5e7eb;padding:1.2rem 1.4rem;background:#fff;transition:box-shadow .2s; }
.app-card:hover { box-shadow:0 8px 24px rgba(0,0,0,.08); }
.app-status-new        { background:#fef3c7;color:#92400e; }
.app-status-shortlisted{ background:#d1fae5;color:#065f46; }
.app-status-rejected   { background:#fee2e2;color:#991b1b; }
.app-status-hired      { background:#dbeafe;color:#1e40af; }

.featured-check:checked + label { color:var(--brand); }
</style>
</head>
<body>
<div class="admin-wrapper">
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php $page_title = "Careers Management"; include 'includes/header.php'; ?>
        <div class="content-body">

            <?php if (!empty($msgText)): ?>
            <div class="alert alert-<?= $msgType === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i><?= $msgText ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- ── STAT CHIPS ── -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-chip">
                        <div class="icon" style="background:#fef3c7;color:#d97706"><i class="bi bi-briefcase-fill"></i></div>
                        <div><div class="num"><?= $totalVac ?></div><div class="lbl">Total Vacancies</div></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-chip">
                        <div class="icon" style="background:#d1fae5;color:#059669"><i class="bi bi-check-circle-fill"></i></div>
                        <div><div class="num"><?= $activeVac ?></div><div class="lbl">Active</div></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-chip">
                        <div class="icon" style="background:#dbeafe;color:#3b82f6"><i class="bi bi-people-fill"></i></div>
                        <div><div class="num"><?= $totalApps ?></div><div class="lbl">Applications</div></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-chip">
                        <div class="icon" style="background:var(--brand-light);color:var(--brand)"><i class="bi bi-envelope-open-fill"></i></div>
                        <div><div class="num"><?= $newApps ?></div><div class="lbl">New / Unread</div></div>
                    </div>
                </div>
            </div>

            <!-- ── TAB NAV ── -->
            <ul class="nav nav-pills-custom mb-4">
                <li class="nav-item">
                    <a class="nav-link <?= $tab === 'vacancies' ? 'active' : '' ?>" href="?tab=vacancies">
                        <i class="bi bi-briefcase me-1"></i> Vacancies <?= $totalVac ? "<span class='badge bg-secondary ms-1'>$totalVac</span>" : '' ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $tab === 'applications' ? 'active' : '' ?>" href="?tab=applications">
                        <i class="bi bi-inbox me-1"></i> Applications <?= $newApps ? "<span class='badge bg-danger ms-1'>$newApps</span>" : "<span class='badge bg-secondary ms-1'>$totalApps</span>" ?>
                    </a>
                </li>
            </ul>

            <!-- ════════════════ VACANCIES TAB ════════════════ -->
            <?php if ($tab === 'vacancies'): ?>
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h5 class="fw-bold mb-0"><i class="bi bi-briefcase me-2 text-danger"></i>Job Vacancies</h5>
                    <div class="d-flex gap-2">
                        <a href="../careers.php" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill">
                            <i class="bi bi-eye me-1"></i> Preview Page
                        </a>
                        <button class="btn btn-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addVacancyModal" style="background:var(--brand);border-color:var(--brand)">
                            <i class="bi bi-plus-circle me-1"></i> Add Vacancy
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="vacancyTable" class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Department</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Last Date</th>
                                <th>Featured</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($vacancies as $i => $v): ?>
                            <tr class="job-row">
                                <td class="text-muted small"><?= $i + 1 ?></td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($v['title']) ?></div>
                                    <?php if ($v['salary_range']): ?>
                                    <small class="text-muted"><i class="bi bi-currency-rupee"></i><?= htmlspecialchars($v['salary_range']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($v['department'] ?? '—') ?></td>
                                <td>
                                    <span class="badge badge-type bg-primary"><?= htmlspecialchars($v['job_type'] ?? '—') ?></span>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($v['location'] ?? '—') ?></td>
                                <td class="small">
                                    <?php if ($v['last_date']): ?>
                                        <?php $ld = new DateTime($v['last_date']); $now = new DateTime; ?>
                                        <span class="<?= $ld < $now ? 'text-danger' : 'text-muted' ?>">
                                            <?= $ld->format('d M Y') ?>
                                            <?= $ld < $now ? '<span class="badge bg-danger ms-1" style="font-size:.6rem">Expired</span>' : '' ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($v['is_featured']): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Yes</span>
                                    <?php else: ?>
                                    <span class="text-muted small">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $v['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ucfirst($v['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill" title="Edit"
                                            data-bs-toggle="modal" data-bs-target="#editModal<?= $v['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this vacancy? Applications won\'t be deleted.')">
                                            <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                            <button type="submit" name="delete_vacancy" class="btn btn-sm btn-outline-danger rounded-pill" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- ── EDIT MODAL ── -->
                            <div class="modal fade modal-form" id="editModal<?= $v['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                    <form method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius:18px">
                                        <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                        <input type="hidden" name="current_image" value="<?= htmlspecialchars($v['image'] ?? '') ?>">
                                        <div class="modal-header" style="background:linear-gradient(135deg,#0f1117,#1a1d2e)">
                                            <h5 class="modal-title text-white fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit: <?= htmlspecialchars($v['title']) ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <?php include '_career_form_fields.php'; // reuse: handled inline below ?>
                                            <div class="section-head">Basic Information</div>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Job Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($v['title']) ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Vacancy Image/Flyer (Optional)</label>
                                                    <input type="file" name="image" class="form-control">
                                                    <?php if(!empty($v['image'])): ?>
                                                        <small class="text-success"><i class="bi bi-image"></i> Current Image Uploaded</small>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Sort Order</label>
                                                    <input type="number" name="sort_order" class="form-control" value="<?= intval($v['sort_order']) ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Department</label>
                                                    <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($v['department'] ?? '') ?>" placeholder="e.g. Production">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Location</label>
                                                    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($v['location'] ?? '') ?>" placeholder="e.g. Chennai">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Job Type</label>
                                                    <select name="job_type" class="form-select">
                                                        <?php foreach ($job_types as $jt): ?>
                                                        <option value="<?= $jt ?>" <?= $v['job_type'] === $jt ? 'selected' : '' ?>><?= $jt ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Experience</label>
                                                    <input type="text" name="experience" class="form-control" value="<?= htmlspecialchars($v['experience'] ?? '') ?>" placeholder="e.g. 2–4 Years">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Qualification</label>
                                                    <input type="text" name="qualification" class="form-control" value="<?= htmlspecialchars($v['qualification'] ?? '') ?>" placeholder="e.g. Diploma / B.E.">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Salary Range</label>
                                                    <input type="text" name="salary_range" class="form-control" value="<?= htmlspecialchars($v['salary_range'] ?? '') ?>" placeholder="e.g. ₹20,000–30,000">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Last Application Date</label>
                                                    <input type="date" name="last_date" class="form-control" value="<?= htmlspecialchars($v['last_date'] ?? '') ?>">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center gap-2 pt-3">
                                                    <input type="checkbox" class="form-check-input featured-check" name="is_featured" id="feat_e<?= $v['id'] ?>" <?= $v['is_featured'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label fw-semibold" for="feat_e<?= $v['id'] ?>"><i class="bi bi-star-fill text-warning"></i> Featured</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" <?= $v['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                        <option value="inactive" <?= $v['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="section-head mt-3">Content</div>
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label">Job Description</label>
                                                    <textarea name="description" class="form-control" rows="3" placeholder="Overview of the role…"><?= htmlspecialchars($v['description'] ?? '') ?></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Key Responsibilities <small class="text-muted">(one per line)</small></label>
                                                    <textarea name="responsibilities" class="form-control" rows="5" placeholder="Operate CNC machines&#10;Read engineering drawings&#10;…"><?= htmlspecialchars($v['responsibilities'] ?? '') ?></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Requirements <small class="text-muted">(one per line)</small></label>
                                                    <textarea name="requirements" class="form-control" rows="5" placeholder="ITI Mechanical&#10;CNC programming experience&#10;…"><?= htmlspecialchars($v['requirements'] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light" style="position: sticky; bottom: 0; z-index: 10;">
                                            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="edit_vacancy" class="btn btn-danger rounded-pill px-4 fw-semibold" style="background:var(--brand);border-color:var(--brand)">
                                                <i class="bi bi-check2-circle me-1"></i> Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; /* end vacancies tab */ ?>


            <!-- ════════════════ APPLICATIONS TAB ════════════════ -->
            <?php if ($tab === 'applications'): ?>
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h5 class="fw-bold mb-0"><i class="bi bi-inbox me-2 text-danger"></i>Job Applications</h5>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <?php $statColors=['new'=>'warning','shortlisted'=>'success','rejected'=>'danger','hired'=>'primary']; ?>
                        <?php foreach (['new','shortlisted','rejected','hired'] as $s): ?>
                        <?php $cnt = count(array_filter($applications, fn($a) => $a['status'] === $s)); ?>
                        <span class="badge bg-<?= $statColors[$s] ?> rounded-pill"><?= ucfirst($s) ?>: <?= $cnt ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="appsTable" class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Applicant</th>
                                <th>Position</th>
                                <th>Contact</th>
                                <th>Experience</th>
                                <th>Resume</th>
                                <th>Status</th>
                                <th>Applied</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($applications as $i => $a): ?>
                            <tr>
                                <td class="text-muted small"><?= $i + 1 ?></td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($a['applicant_name']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($a['applicant_email']) ?></small>
                                </td>
                                <td>
                                    <div class="small fw-medium"><?= htmlspecialchars($a['job_title'] ?? 'General') ?></div>
                                    <?php if ($a['vacancy_title'] && $a['vacancy_title'] !== $a['job_title']): ?>
                                    <small class="text-muted"><?= htmlspecialchars($a['vacancy_title']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <div><?= htmlspecialchars($a['applicant_phone']) ?></div>
                                    <a href="mailto:<?= htmlspecialchars($a['applicant_email']) ?>" class="text-decoration-none text-primary">
                                        <i class="bi bi-envelope-fill"></i>
                                    </a>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($a['applicant_experience'] ?: '—') ?></td>
                                <td>
                                    <?php if ($a['resume_path']): ?>
                                    <a href="../<?= htmlspecialchars($a['resume_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-file-earmark-arrow-down"></i> View
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge rounded-pill app-status-<?= $a['status'] ?>">
                                        <?= ucfirst($a['status']) ?>
                                    </span>
                                </td>
                                <td class="small text-muted"><?= date('d M Y', strtotime($a['created_at'])) ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill" title="Update Status"
                                            data-bs-toggle="modal" data-bs-target="#statusModal<?= $a['id'] ?>">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                        <?php if ($a['cover_note']): ?>
                                        <button class="btn btn-sm btn-outline-info rounded-pill" title="View Note"
                                            data-bs-toggle="modal" data-bs-target="#noteModal<?= $a['id'] ?>">
                                            <i class="bi bi-chat-text"></i>
                                        </button>
                                        <?php endif; ?>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this application?')">
                                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                            <button type="submit" name="delete_application" class="btn btn-sm btn-outline-danger rounded-pill">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Status Modal -->
                            <div class="modal fade" id="statusModal<?= $a['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content border-0 shadow rounded-4">
                                        <form method="POST">
                                            <input type="hidden" name="app_id" value="<?= $a['id'] ?>">
                                            <div class="modal-header border-0 pb-0">
                                                <h6 class="modal-title fw-bold">Update Status</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                <p class="small text-muted mb-2"><strong><?= htmlspecialchars($a['applicant_name']) ?></strong></p>
                                                <select name="app_status" class="form-select form-select-sm rounded-pill">
                                                    <?php foreach (['new','shortlisted','rejected','hired'] as $s): ?>
                                                    <option value="<?= $s ?>" <?= $a['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="update_app_status" class="btn btn-sm btn-danger rounded-pill px-3" style="background:var(--brand);border-color:var(--brand)">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Note Modal -->
                            <?php if ($a['cover_note']): ?>
                            <div class="modal fade" id="noteModal<?= $a['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-4">
                                        <div class="modal-header border-0">
                                            <h6 class="modal-title fw-bold">Cover Note — <?= htmlspecialchars($a['applicant_name']) ?></h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-muted" style="font-size:.9rem;white-space:pre-line"><?= htmlspecialchars($a['cover_note']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; /* end applications tab */ ?>

        </div><!-- /content-body -->
        <?php include 'includes/footer.php'; ?>
    </div><!-- /main-content -->
</div><!-- /admin-wrapper -->


<!-- ════════════════ ADD VACANCY MODAL ════════════════ -->
<div class="modal fade modal-form" id="addVacancyModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius:18px">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--brand),#a01f1f)">
                <h5 class="modal-title text-white fw-bold"><i class="bi bi-plus-circle me-2"></i>Add New Job Vacancy</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="section-head">Basic Information</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Job Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Precision Machinist" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Vacancy Image/Flyer (Optional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-control" placeholder="e.g. Production">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. Chennai, India">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Job Type</label>
                        <select name="job_type" class="form-select">
                            <?php foreach ($job_types as $jt): ?>
                            <option value="<?= $jt ?>"><?= $jt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Experience Required</label>
                        <input type="text" name="experience" class="form-control" placeholder="e.g. 2–4 Years">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Qualification</label>
                        <input type="text" name="qualification" class="form-control" placeholder="e.g. ITI / Diploma">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Salary Range</label>
                        <input type="text" name="salary_range" class="form-control" placeholder="e.g. ₹20,000–30,000/month">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Last Application Date</label>
                        <input type="date" name="last_date" class="form-control">
                    </div>
                    <div class="col-md-2 d-flex align-items-center gap-2 pt-3">
                        <input type="checkbox" class="form-check-input" name="is_featured" id="featAdd">
                        <label class="form-check-label fw-semibold" for="featAdd"><i class="bi bi-star-fill text-warning"></i> Featured</label>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="section-head mt-3">Content</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Job Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Provide an overview of the role and what the candidate will be doing…"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Key Responsibilities <small class="text-muted">(one per line)</small></label>
                        <textarea name="responsibilities" class="form-control" rows="6" placeholder="Operate CNC/VMC machines&#10;Read and interpret engineering drawings&#10;Maintain machine log books"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Requirements / Skills <small class="text-muted">(one per line)</small></label>
                        <textarea name="requirements" class="form-control" rows="6" placeholder="ITI in Turner / Machinist&#10;Experience in CNC programming preferred&#10;Knowledge of GD&T is an advantage"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light" style="position: sticky; bottom: 0; z-index: 10;">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_vacancy" class="btn btn-danger rounded-pill px-4 fw-semibold" style="background:var(--brand);border-color:var(--brand)">
                    <i class="bi bi-plus-circle me-1"></i> Publish Vacancy
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
    $('#vacancyTable').DataTable({
        order: [[0,'asc']],
        pageLength: 10,
        language: { search: 'Search:', lengthMenu: 'Show _MENU_' }
    });
    $('#appsTable').DataTable({
        order: [[7,'desc']],
        pageLength: 15,
        language: { search: 'Search:', lengthMenu: 'Show _MENU_' }
    });
});
</script>
<script src="js/admin-custom.js"></script>
</body>
</html>
