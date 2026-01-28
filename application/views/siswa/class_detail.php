<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-lg-8 p-4 d-flex flex-column justify-content-center border-end-lg">
                    <div class="mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                            <i class="bi bi-bookmark-fill me-1"></i> Kelas Aktif
                        </span>
                    </div>
                    <h2 class="display-6 fw-bold text-dark mb-1">
                        <?= htmlspecialchars($kelas->name, ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                    <p class="text-muted mb-4">
                        <i class="bi bi-person-video3 me-2 text-primary"></i>
                        Guru Pengampu: <span class="fw-semibold text-dark"><?= htmlspecialchars($kelas->teacher_name, ENT_QUOTES, 'UTF-8'); ?></span>
                    </p>

                    <div class="d-flex flex-wrap gap-2 mt-auto">
                        <?php if(isset($role_controller) && $role_controller == 'admin'): ?>
                            <a href="<?= base_url('admin/dashboard/classes') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('siswa/dashboard') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-1"></i> Dashboard
                            </a>
                            <a href="<?= base_url('siswa/pbl/index/' . $kelas->id); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-journal-album me-2"></i> Akses Materi
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-4 bg-light bg-opacity-50 p-4 d-flex flex-column justify-content-center align-items-center text-center">
                    <div class="card border-0 shadow-sm w-100 mb-3" style="max-width: 300px;">
                        <div class="card-body py-3">
                            <small class="text-uppercase text-muted fw-bold small ls-1">Kode Kelas</small>
                            <div class="d-flex align-items-center justify-content-center mt-1">
                                <span class="fs-2 fw-bold font-monospace text-primary letter-spacing-2">
                                    <?= htmlspecialchars($kelas->code, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                                <button class="btn btn-link btn-sm text-secondary ms-2" onclick="navigator.clipboard.writeText('<?= $kelas->code ?>'); alert('Kode disalin!');" title="Salin Kode">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white px-4 py-2 rounded-3 border shadow-sm">
                            <i class="bi bi-people-fill text-success fs-5 d-block mb-1"></i>
                            <span class="fw-bold text-dark fs-5" id="jumlah-siswa"><?= $kelas->student_count; ?></span>
                            <small class="d-block text-muted" style="font-size: 0.75rem;">Siswa</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
            <div class="d-flex align-items-center">
                <div class="icon-square bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h5 class="m-0 fw-bold text-dark">Daftar Siswa</h5>
                    <small class="text-muted">Data siswa yang terdaftar di kelas ini</small>
                </div>
            </div>

            <?php if (isset($can_manage_students) && $can_manage_students === true): ?>
                <button class="btn btn-success btn-sm px-3 rounded-pill shadow-sm" id="btnAddStudent" data-bs-toggle="modal" data-bs-target="#siswaModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa
                </button>
            <?php endif; ?>
        </div>

        <div class="card-body p-0" id="siswaTableContainer">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="siswaTable" style="width:100%">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary text-uppercase small fw-bold border-bottom-0" style="width: 5%;">No</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold border-bottom-0">Nama Lengkap</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold border-bottom-0">Username</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold border-bottom-0">Email</th>

                            <?php if (isset($can_manage_students) && $can_manage_students === true): ?>
                                <th class="py-3 px-4 text-secondary text-uppercase small fw-bold border-bottom-0 text-end" style="width: 15%;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php if (isset($can_manage_students) && $can_manage_students === true): ?>
    <div class="modal fade" id="siswaModal" tabindex="-1" aria-labelledby="siswaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">

                <form id="studentForm">
                    <div class="modal-header bg-primary text-white border-bottom-0">
                        <h5 class="modal-title fw-bold" id="siswaModalLabel">
                            <i class="bi bi-person-plus-fill me-2"></i>Tambah Siswa
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4 bg-light">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="class_id" id="classIdHidden" value="<?= $kelas->id; ?>">

                        <div class="card border-0 shadow-sm mb-2">
                            <div class="card-body">
                                <label for="studentSelect" class="form-label fw-bold text-dark">Pilih Siswa</label>
                                <select class="form-select form-select-lg border-secondary" id="studentSelect" name="student_id" required>
                                    <option value="">-- Cari Nama Siswa --</option>
                                    <?php if (!empty($siswa_list)): ?>
                                        <?php foreach($siswa_list as $s): ?>
                                            <option value="<?= $s->id; ?>">
                                                <?= htmlspecialchars($s->name, ENT_QUOTES, 'UTF-8'); ?> (<?= htmlspecialchars($s->username, ENT_QUOTES, 'UTF-8'); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text text-muted mt-2">
                                    <i class="bi bi-info-circle me-1"></i> Hanya menampilkan siswa yang belum masuk kelas manapun.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 bg-white">
                        <button type="button" class="btn btn-light text-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" id="btnSaveStudent">
                            <i class="bi bi-check-circle me-1"></i> Simpan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    window.BASE_URL = '<?= base_url() ?>';
    window.CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name(); ?>';

    // Konfigurasi Dinamis dari Controller
    window.CURRENT_CLASS_ID = '<?= $kelas->id; ?>';
    // Gunakan pengecekan PHP untuk variabel boolean JS
    window.IS_ADMIN_OR_GURU = <?= (isset($is_admin_or_guru) && $is_admin_or_guru === true) ? 'true' : 'false' ?>;
    window.ROLE_CONTROLLER = '<?= isset($role_controller) ? $role_controller : 'guru' ?>';
</script>

<script type="module" src="<?= base_url('assets/js/siswa/class_detail.js') ?>"></script>