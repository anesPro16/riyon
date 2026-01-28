<div class="container-fluid py-4 bg-light">
    
    <div class="row mb-4">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm bg-primary text-white overflow-hidden position-relative rounded-4">
                <div class="card-body p-4 p-lg-5 position-relative z-1">
                    <h2 class="fw-bold display-6">Halo, <?= isset($user['name']) ? $user['name'] : 'User'; ?>! 👋</h2>
                    <p class="lead mb-0 opacity-75">Selamat datang di Dashboard <?= $role_label; ?>.</p>
                </div>
                <div class="position-absolute end-0 bottom-0 opacity-25 me-n5 mb-n5" style="transform: rotate(-15deg);">
                    <i class="bi bi-journal-bookmark-fill" style="font-size: 12rem; color: #fff;"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="icon-square bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                        <i class="bi bi-collection-fill fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase small fw-bold mb-0">Total Kelas</p>
                        <h4 class="fw-bold mb-0 text-dark"><?= $total_kelas; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="icon-square bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase small fw-bold mb-0">Status Akun</p>
                        <h5 class="fw-bold mb-0 text-success">Aktif</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark m-0">
            <i class="bi bi-grid-fill me-2 text-primary"></i>Daftar Kelas Saya
        </h4>
        </?php if($role_label == 'Pengajar'): ?>
            <a href="#" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Buat Kelas Baru
            </a>
        </?php endif; ?>
    </div> -->

    <div class="row g-4">
        <?php if (!empty($classes)): ?>
            <?php foreach ($classes as $class): ?>
                <div class="col-md-6 col-lg-4 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm card-hover rounded-4 overflow-hidden">
                        <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-normal">
                                <i class="bi bi-hash me-1"></i><?= htmlspecialchars($class->code); ?>
                            </span>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-info-circle me-2"></i>Detail Info</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark mb-3 text-truncate" title="<?= htmlspecialchars($class->class_name); ?>">
                                <?= htmlspecialchars($class->class_name); ?>
                            </h5>
                            
                            <div class="d-flex align-items-center p-2 bg-light rounded-3">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-video3 text-secondary"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="small text-muted mb-0" style="font-size: 0.75rem;">Pengajar</p>
                                    <p class="small fw-bold text-dark mb-0 text-truncate">
                                        <?= htmlspecialchars($class->teacher_name); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 pb-4 pt-0">
                            <a href="<?= base_url($url_name . '/dashboard/class_detail/' . $class->id) ?>" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold shadow-sm btn-masuk">
                                Masuk Kelas <i class="bi bi-arrow-right-short ms-1 fs-5 align-middle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                    <div class="card-body">
                        <div class="mb-3 text-muted opacity-25">
                            <i class="bi bi-clipboard-x" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="fw-bold text-secondary">Belum ada kelas</h4>
                        <p class="text-muted">
                            <?= ($role_label == 'Pengajar') 
                                ? 'Anda belum membuat kelas apapun.' 
                                : 'Anda belum terdaftar di kelas manapun.'; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .btn-masuk {
        transition: all 0.2s;
    }
    .btn-masuk:hover {
        transform: scale(1.02);
    }
</style>