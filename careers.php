<?php
require_once 'config/db.php';

// ── Fetch SEO settings ──────────────────────────────────────────────────────
$seo = $pdo->query("SELECT * FROM site_settings WHERE id = 1")->fetch();

// ── Fetch active vacancies ──────────────────────────────────────────────────
$tableExists = $pdo->query("SHOW TABLES LIKE 'job_vacancies'")->rowCount() > 0;
$vacancies   = [];
$featured    = [];
$departments = [];
$job_types   = [];

if ($tableExists) {
    $vacancies   = $pdo->query("SELECT * FROM job_vacancies WHERE status = 'active' ORDER BY is_featured DESC, sort_order ASC, created_at DESC")->fetchAll();
    $featured    = array_filter($vacancies, fn($v) => $v['is_featured']);
    $departments = array_unique(array_filter(array_column($vacancies, 'department')));
    $job_types   = array_unique(array_filter(array_column($vacancies, 'job_type')));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers — <?= htmlspecialchars($seo['site_title'] ?? 'Phoenix Precision') ?></title>
    <meta name="description" content="Explore exciting career opportunities at Phoenix Precision Products. Join our growing team of engineers and machinists.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --brand: #d62828;
            --brand-dark: #a01f1f;
            --brand-light: rgba(214,40,40,0.08);
            --dark: #0f1117;
            --dark2: #1a1d2e;
            --surface: #ffffff;
            --muted: #6b7280;
            --border: #e5e7eb;
        }
        body { font-family: 'Inter', sans-serif; }

        /* ── Hero ── */
        .careers-hero {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark2) 60%, #1e1230 100%);
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }
        .careers-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 60% 70% at 70% 50%, rgba(214,40,40,.18) 0%, transparent 70%);
        }
        .careers-hero::after {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 420px; height: 420px;
            border-radius: 50%;
            background: rgba(214,40,40,.07);
            filter: blur(60px);
        }
        .careers-hero .badge-pill {
            display: inline-block;
            background: rgba(214,40,40,.18);
            color: #ff8080;
            border: 1px solid rgba(214,40,40,.3);
            border-radius: 50px;
            padding: 6px 18px;
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: 1.2rem;
        }
        .careers-hero h1 { font-size: clamp(2.2rem, 5vw, 3.6rem); font-weight: 800; line-height: 1.15; color: #fff; }
        .careers-hero h1 span { color: var(--brand); }
        .careers-hero p { color: #9ca3af; font-size: 1.1rem; max-width: 560px; }
        .hero-stats { display: flex; gap: 2.5rem; margin-top: 2rem; }
        .hero-stats .stat-num { font-size: 2rem; font-weight: 800; color: #fff; }
        .hero-stats .stat-label { font-size: .8rem; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; }

        /* ── Filter bar ── */
        .filter-bar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 16px rgba(0,0,0,.05);
        }
        .filter-bar select, .filter-bar input {
            border-radius: 50px;
            border: 1px solid var(--border);
            padding: .45rem 1rem;
            font-size: .88rem;
        }
        .filter-bar select:focus, .filter-bar input:focus { box-shadow: 0 0 0 3px rgba(214,40,40,.15); border-color: var(--brand); outline: none; }

        /* ── Cards ── */
        .section-label { font-size: .72rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--brand); }
        .job-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.6rem;
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s;
            position: relative;
            overflow: hidden;
        }
        .job-card:hover { transform: translateY(-4px); box-shadow: 0 20px 50px rgba(0,0,0,.1); border-color: var(--brand); }
        .job-card.featured-card { border-color: var(--brand); }
        .featured-ribbon {
            position: absolute; top: 14px; right: -28px;
            background: var(--brand); color: #fff;
            font-size: .65rem; font-weight: 700; letter-spacing: .06em;
            text-transform: uppercase; padding: 4px 40px;
            transform: rotate(45deg); transform-origin: top right;
        }
        .dept-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--brand-light); color: var(--brand);
            border-radius: 50px; padding: 4px 12px; font-size: .76rem; font-weight: 600;
        }
        .type-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: #eef2ff; color: #4338ca;
            border-radius: 50px; padding: 4px 12px; font-size: .76rem; font-weight: 600;
        }
        .job-meta { display: flex; flex-wrap: wrap; gap: .7rem; margin: 1rem 0; }
        .meta-item { display: flex; align-items: center; gap: 5px; font-size: .82rem; color: var(--muted); }
        .job-card h5 { font-size: 1.12rem; font-weight: 700; color: var(--dark); margin: .6rem 0 0; }
        .job-card .desc-text { font-size: .88rem; color: #4b5563; line-height: 1.65; }
        .btn-apply {
            background: var(--brand); color: #fff; border: none;
            border-radius: 50px; padding: .5rem 1.4rem; font-size: .88rem; font-weight: 600;
            transition: background .18s, transform .15s;
        }
        .btn-apply:hover { background: var(--brand-dark); color: #fff; transform: scale(1.03); }
        .btn-details {
            background: transparent; color: var(--dark); border: 1px solid var(--border);
            border-radius: 50px; padding: .5rem 1.2rem; font-size: .88rem; font-weight: 500;
            transition: border-color .18s, color .18s;
        }
        .btn-details:hover { border-color: var(--brand); color: var(--brand); }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 80px 20px; }
        .empty-state .empty-icon { font-size: 4rem; color: #e5e7eb; margin-bottom: 1rem; }

        /* ── Detail modal ── */
        .modal-detail .modal-header { background: linear-gradient(135deg, var(--dark) 0%, var(--dark2) 100%); }
        .modal-detail .badge-type { font-size: .75rem; padding: .35em .75em; border-radius: 50px; }
        .detail-section h6 { font-weight: 700; font-size: .9rem; margin-bottom: .5rem; color: var(--dark); }
        .detail-section ul { padding-left: 1.2rem; }
        .detail-section ul li { margin-bottom: .3rem; font-size: .9rem; color: #4b5563; }
        .modal-content { border-radius: 20px; border: none; }

        /* ── Apply modal ── */
        .apply-modal .modal-header { background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%); }
        .apply-modal .form-control { border-radius: 10px; font-size: .9rem; }
        .apply-modal .form-control:focus { box-shadow: 0 0 0 3px rgba(214,40,40,.15); border-color: var(--brand); }

        /* ── Why us ── */
        .why-card {
            background: #fff; border-radius: 16px; padding: 2rem 1.5rem;
            border: 1px solid var(--border); text-align: center;
            transition: transform .2s, box-shadow .2s;
        }
        .why-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,.08); }
        .why-icon { width: 60px; height: 60px; border-radius: 16px; background: var(--brand-light); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.6rem; color: var(--brand); }

        /* ── CTA banner ── */
        .cta-banner {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            border-radius: 20px; padding: 3rem 2rem; color: #fff; text-align: center;
        }
        .cta-banner h3 { font-weight: 800; }
        .btn-cta-light { background: #fff; color: var(--brand); border: none; border-radius: 50px; padding: .65rem 1.8rem; font-weight: 700; transition: transform .18s; }
        .btn-cta-light:hover { transform: scale(1.04); color: var(--brand-dark); }

        /* Animations */
        @keyframes fadeUp { from { opacity:0; transform: translateY(20px); } to { opacity:1; transform: translateY(0); } }
        .fade-up { animation: fadeUp .5s ease both; }
        .job-card { animation: fadeUp .4s ease both; }
        .job-card:nth-child(1) { animation-delay: .05s; }
        .job-card:nth-child(2) { animation-delay: .1s; }
        .job-card:nth-child(3) { animation-delay: .15s; }
        .job-card:nth-child(4) { animation-delay: .2s; }
        .job-card:nth-child(5) { animation-delay: .25s; }
        .job-card:nth-child(6) { animation-delay: .3s; }
    </style>
</head>
<body>

<?php include 'includes/topheader.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!-- ═══════════ HERO ════════════════════════════════════════════════════════ -->
<section class="careers-hero">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="badge-pill"><i class="bi bi-stars me-1"></i> We're Hiring</div>
                <h1>Build Your Career<br>With <span>Phoenix Precision</span></h1>
                <p class="mt-3">Join a team of driven engineers and manufacturers passionate about precision excellence. Explore opportunities that challenge, inspire, and grow with you.</p>
                <a href="#vacancies" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold mt-3 me-2" style="background:var(--brand);border:none;">
                    <i class="bi bi-briefcase me-2"></i>Browse Openings
                </a>
                <a href="#why-us" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold mt-3">
                    Why Join Us?
                </a>
                <?php if (count($vacancies)): ?>
                <div class="hero-stats">
                    <div>
                        <div class="stat-num"><?= count($vacancies) ?>+</div>
                        <div class="stat-label">Open Positions</div>
                    </div>
                    <div>
                        <div class="stat-num"><?= count($departments) ?>+</div>
                        <div class="stat-label">Departments</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                <div style="font-size:14rem;opacity:.06;line-height:1;user-select:none;color:#fff">
                    <i class="bi bi-briefcase-fill"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════ FILTER BAR ════════════════════════════════════════════════════ -->
<?php if (count($vacancies) > 0): ?>
<div class="filter-bar">
    <div class="container">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="🔍  Search by title or keyword…">
            </div>
            <div class="col-md-3">
                <select id="deptFilter" class="form-select">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select id="typeFilter" class="form-select">
                    <option value="">All Types</option>
                    <?php foreach ($job_types as $t): ?>
                        <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button id="clearFilters" class="btn btn-outline-secondary rounded-pill w-100" style="font-size:.85rem">
                    <i class="bi bi-x-circle me-1"></i> Clear
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ═══════════ JOB LISTINGS ══════════════════════════════════════════════════ -->
<section id="vacancies" class="py-5" style="background:#f8f9fc">
    <div class="container">

        <?php if (!$tableExists): ?>
        <div class="alert alert-warning text-center">
            The careers table hasn't been set up yet.
            <a href="sql/migration_careers.php?action=up" class="fw-bold">Run Migration</a>
        </div>
        <?php elseif (empty($vacancies)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-briefcase"></i></div>
            <h4 class="fw-bold text-dark">No Open Positions Right Now</h4>
            <p class="text-muted">We're not actively hiring at the moment, but great talent is always welcome. Send us your resume!</p>
            <a href="mailto:<?= htmlspecialchars($seo['meta_description'] ?? 'careers@phoenixprecision.in') ?>" class="btn btn-outline-danger rounded-pill px-4 mt-2">
                <i class="bi bi-envelope me-2"></i>Send Your Resume
            </a>
        </div>

        <?php else: ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <div class="section-label">Current Openings</div>
                <h2 class="fw-bold mb-0 mt-1" style="font-size:1.7rem">Explore Opportunities</h2>
            </div>
            <div class="text-muted small" id="resultCount"><?= count($vacancies) ?> position<?= count($vacancies) !== 1 ? 's' : '' ?> found</div>
        </div>

        <div id="jobGrid" class="row g-4">
            <?php foreach ($vacancies as $v): ?>
            <div class="col-lg-6 job-col"
                 data-title="<?= strtolower(htmlspecialchars($v['title'] . ' ' . $v['department'] . ' ' . $v['location'])) ?>"
                 data-dept="<?= htmlspecialchars($v['department'] ?? '') ?>"
                 data-type="<?= htmlspecialchars($v['job_type'] ?? '') ?>">
                <div class="job-card <?= $v['is_featured'] ? 'featured-card' : '' ?> h-100">
                    <?php if ($v['is_featured']): ?>
                    <div class="featured-ribbon">Featured</div>
                    <?php endif; ?>

                    <div class="d-flex gap-2 flex-wrap">
                        <?php if ($v['department']): ?>
                        <span class="dept-badge"><i class="bi bi-building"></i><?= htmlspecialchars($v['department']) ?></span>
                        <?php endif; ?>
                        <?php if ($v['job_type']): ?>
                        <span class="type-badge"><i class="bi bi-clock"></i><?= htmlspecialchars($v['job_type']) ?></span>
                        <?php endif; ?>
                    </div>

                    <h5 class="mt-2"><?= htmlspecialchars($v['title']) ?></h5>

                    <div class="job-meta">
                        <?php if ($v['location']): ?>
                        <span class="meta-item"><i class="bi bi-geo-alt-fill text-danger"></i><?= htmlspecialchars($v['location']) ?></span>
                        <?php endif; ?>
                        <?php if ($v['experience']): ?>
                        <span class="meta-item"><i class="bi bi-briefcase-fill text-primary"></i><?= htmlspecialchars($v['experience']) ?></span>
                        <?php endif; ?>
                        <?php if ($v['salary_range']): ?>
                        <span class="meta-item"><i class="bi bi-currency-rupee text-success"></i><?= htmlspecialchars($v['salary_range']) ?></span>
                        <?php endif; ?>
                        <?php if ($v['last_date']): ?>
                        <span class="meta-item"><i class="bi bi-calendar-event text-warning"></i>Apply by <?= date('d M Y', strtotime($v['last_date'])) ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($v['image'])): ?>
                    <div class="mt-3 mb-3 text-center" style="background:#f8f9fa; border-radius:8px; padding:10px;">
                        <img src="<?= htmlspecialchars($v['image']) ?>" alt="Flyer" style="max-height: 140px; object-fit: contain; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    </div>
                    <?php elseif ($v['description']): ?>
                    <p class="desc-text"><?= nl2br(htmlspecialchars(substr($v['description'], 0, 140))) ?>…</p>
                    <?php endif; ?>

                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <button class="btn-apply btn"
                            data-bs-toggle="modal" data-bs-target="#applyModal"
                            data-job-id="<?= $v['id'] ?>"
                            data-job-title="<?= htmlspecialchars($v['title']) ?>">
                            <i class="bi bi-send me-1"></i> Apply Now
                        </button>
                        <button class="btn-details btn"
                            data-bs-toggle="modal" data-bs-target="#detailModal<?= $v['id'] ?>">
                            <i class="bi bi-eye me-1"></i> View Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Detail Modal -->
            <div class="modal fade modal-detail" id="detailModal<?= $v['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title text-white fw-bold mb-1"><?= htmlspecialchars($v['title']) ?></h5>
                                <div class="d-flex gap-2 flex-wrap">
                                    <?php if ($v['department']): ?>
                                    <span class="badge bg-danger badge-type"><?= htmlspecialchars($v['department']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($v['job_type']): ?>
                                    <span class="badge bg-primary badge-type"><?= htmlspecialchars($v['job_type']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($v['location']): ?>
                                    <span class="badge bg-secondary badge-type"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($v['location']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <?php if (!empty($v['image'])): ?>
                            <div class="text-center mb-4">
                                <img src="<?= htmlspecialchars($v['image']) ?>" alt="Vacancy Flyer" class="img-fluid rounded shadow-sm" style="max-height: 700px; width: 100%; object-fit: contain; background: #f8f9fa; padding: 10px;">
                            </div>
                            <?php endif; ?>

                            <div class="row g-3 mb-3">
                                <?php $metas = [
                                    ['icon'=>'bi-briefcase-fill text-primary','label'=>'Experience','val'=>$v['experience']],
                                    ['icon'=>'bi-mortarboard-fill text-info','label'=>'Qualification','val'=>$v['qualification']],
                                    ['icon'=>'bi-currency-rupee text-success','label'=>'Salary','val'=>$v['salary_range']],
                                    ['icon'=>'bi-calendar-event text-warning','label'=>'Last Date','val'=>$v['last_date'] ? date('d M Y', strtotime($v['last_date'])) : null],
                                ];
                                foreach ($metas as $m): if ($m['val']): ?>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 rounded-3 border text-center h-100" style="background:#fafafa">
                                        <i class="bi <?= $m['icon'] ?> fs-4 d-block mb-1"></i>
                                        <div class="fw-semibold" style="font-size:.82rem"><?= $m['val'] ?></div>
                                        <div class="text-muted" style="font-size:.72rem"><?= $m['label'] ?></div>
                                    </div>
                                </div>
                                <?php endif; endforeach; ?>
                            </div>

                            <?php if ($v['description']): ?>
                            <div class="detail-section mb-3">
                                <h6><i class="bi bi-info-circle text-danger me-2"></i>About the Role</h6>
                                <p style="font-size:.9rem;color:#4b5563"><?= nl2br(htmlspecialchars($v['description'])) ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if ($v['responsibilities']): ?>
                            <div class="detail-section mb-3">
                                <h6><i class="bi bi-check2-circle text-success me-2"></i>Key Responsibilities</h6>
                                <ul>
                                    <?php foreach (array_filter(array_map('trim', explode("\n", $v['responsibilities']))) as $r): ?>
                                    <li><?= htmlspecialchars($r) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <?php if ($v['requirements']): ?>
                            <div class="detail-section">
                                <h6><i class="bi bi-star-fill text-warning me-2"></i>Requirements</h6>
                                <ul>
                                    <?php foreach (array_filter(array_map('trim', explode("\n", $v['requirements']))) as $r): ?>
                                    <li><?= htmlspecialchars($r) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-danger rounded-pill px-4"
                                data-bs-toggle="modal" data-bs-target="#applyModal"
                                data-bs-dismiss="modal"
                                data-job-id="<?= $v['id'] ?>"
                                data-job-title="<?= htmlspecialchars($v['title']) ?>">
                                <i class="bi bi-send me-1"></i> Apply for this Position
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="noResults" class="empty-state d-none">
            <div class="empty-icon"><i class="bi bi-search"></i></div>
            <h5 class="fw-bold">No matching positions found</h5>
            <p class="text-muted">Try adjusting your search or filters.</p>
            <button id="clearFilters2" class="btn btn-outline-danger rounded-pill px-4">Clear Filters</button>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ═══════════ WHY JOIN US ═══════════════════════════════════════════════════ -->
<section id="why-us" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">Why Choose Us</div>
            <h2 class="fw-bold mt-1" style="font-size:1.9rem">Build More Than Just a Career</h2>
            <p class="text-muted mx-auto" style="max-width:500px">At Phoenix Precision, your growth is our mission. We invest in talent and create an environment that drives innovation.</p>
        </div>
        <div class="row g-4">
            <?php $perks = [
                ['bi-graph-up-arrow', 'Career Growth', 'Structured career paths and mentorship from industry veterans to help you level up.'],
                ['bi-tools', 'Modern Equipment', 'Work with state-of-the-art CNC and precision machinery in a world-class facility.'],
                ['bi-shield-check', 'Stable Environment', 'Join a stable, growing company with a strong order book and long-term vision.'],
                ['bi-people-fill', 'Collaborative Culture', 'A team that supports each other — cross-functional learning and mutual respect.'],
                ['bi-lightbulb-fill', 'Innovation First', 'We encourage creative ideas and process improvements at every level.'],
                ['bi-heart-fill', 'Employee Wellbeing', 'Health benefits, ESI/PF coverage, and a safe working environment for all.'],
            ]; foreach ($perks as $p): ?>
            <div class="col-md-4">
                <div class="why-card">
                    <div class="why-icon"><i class="bi <?= $p[0] ?>"></i></div>
                    <h6 class="fw-bold mb-2"><?= $p[1] ?></h6>
                    <p class="text-muted small mb-0"><?= $p[2] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════ CTA ════════════════════════════════════════════════════════════ -->
<section class="py-5" style="background:#f8f9fc">
    <div class="container">
        <div class="cta-banner">
            <h3>Don't See the Right Role?</h3>
            <p class="mb-4 opacity-75">We're always looking for great talent. Send us your resume and we'll reach out when the right opportunity comes up.</p>
            <button class="btn btn-cta-light rounded-pill px-4 py-2"
                data-bs-toggle="modal" data-bs-target="#applyModal"
                data-job-id="" data-job-title="General Application">
                <i class="bi bi-envelope-arrow-up me-2"></i>Send Your Resume
            </button>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- ═══════════ APPLY MODAL ═══════════════════════════════════════════════════ -->
<div class="modal fade apply-modal" id="applyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title text-white fw-bold mb-0">Apply for a Position</h5>
                    <small class="text-white opacity-75" id="applyJobTitle"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="applyForm" action="process_application.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="job_id" id="applyJobId">
                <input type="hidden" name="job_title_hidden" id="applyJobTitleHidden">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="applicant_name" class="form-control" placeholder="Your full name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Email <span class="text-danger">*</span></label>
                            <input type="email" name="applicant_email" class="form-control" placeholder="you@email.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="applicant_phone" class="form-control" placeholder="+91 XXXXXXXXXX" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Current Role / Experience</label>
                            <input type="text" name="applicant_experience" class="form-control" placeholder="e.g. CNC Operator with 3 years experience">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Upload Resume <span class="text-danger">*</span></label>
                            <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx" required>
                            <small class="text-muted">PDF, DOC, DOCX — max 5 MB</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Cover Note</label>
                            <textarea name="cover_note" class="form-control" rows="3" placeholder="Briefly tell us why you're a great fit…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold" style="background:var(--brand);border:none">
                        <i class="bi bi-send me-1"></i> Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'includes/scripts.php'; ?>
<script>
// ── Apply modal: populate job details ──────────────────────────────────
const applyModal = document.getElementById('applyModal');
applyModal.addEventListener('show.bs.modal', function (e) {
    const trigger = e.relatedTarget;
    const id    = trigger.getAttribute('data-job-id') || '';
    const title = trigger.getAttribute('data-job-title') || 'General Application';
    document.getElementById('applyJobId').value          = id;
    document.getElementById('applyJobTitleHidden').value = title;
    document.getElementById('applyJobTitle').textContent = title;
});

// ── Client-side filtering ──────────────────────────────────────────────
const cols     = document.querySelectorAll('.job-col');
const noRes    = document.getElementById('noResults');
const resCount = document.getElementById('resultCount');

function applyFilters() {
    const search = (document.getElementById('searchInput')?.value || '').toLowerCase();
    const dept   = (document.getElementById('deptFilter')?.value || '').toLowerCase();
    const type   = (document.getElementById('typeFilter')?.value || '').toLowerCase();
    let visible  = 0;
    cols.forEach(c => {
        const t = c.dataset.title || '';
        const d = (c.dataset.dept || '').toLowerCase();
        const p = (c.dataset.type || '').toLowerCase();
        const show = (!search || t.includes(search)) && (!dept || d === dept) && (!type || p === type);
        c.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    if (noRes)    noRes.classList.toggle('d-none', visible > 0);
    if (resCount) resCount.textContent = visible + ' position' + (visible !== 1 ? 's' : '') + ' found';
}

function clearAll() {
    if (document.getElementById('searchInput')) document.getElementById('searchInput').value = '';
    if (document.getElementById('deptFilter'))  document.getElementById('deptFilter').value  = '';
    if (document.getElementById('typeFilter'))  document.getElementById('typeFilter').value  = '';
    applyFilters();
}

document.getElementById('searchInput')?.addEventListener('input', applyFilters);
document.getElementById('deptFilter')?.addEventListener('change', applyFilters);
document.getElementById('typeFilter')?.addEventListener('change', applyFilters);
document.getElementById('clearFilters')?.addEventListener('click', clearAll);
document.getElementById('clearFilters2')?.addEventListener('click', clearAll);
</script>
</body>
</html>
