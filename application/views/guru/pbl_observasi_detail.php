<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-clipboard-data me-2 text-primary"></i>Detail Observasi
                    </h5>
                    <p class="text-muted small mb-0">
                        Kelola file yang diunggah murid dan berikan penilaian serta umpan balik.
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="<?= base_url('guru/pbl/observasi/' . $slot->class_id) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h6 class="m-0 fw-bold text-primary">
                <i class="bi bi-file-earmark-arrow-up me-2"></i>Daftar  Penilaian
            </h6>
        </div>
        <div class="card-body p-0" id="observasiTableContainer">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="uploadsTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary text-uppercase small fw-bold" width="5%">No</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">Nama Murid & File</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">Dokumen</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Nilai</th>
                            <th class="py-3 px-4 text-secondary text-uppercase small fw-bold text-end" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="gradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            <form id="gradeForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="gradeModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Input Penilaian
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    
                    <input type="hidden" name="id" id="gradeId">
                    <input type="hidden" name="observation_slot_id" value="<?= $slot->observation_id; ?>">
                    <input type="hidden" name="user_id" id="userIdInput">

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">
                            <i class="bi bi-person me-1"></i> Nama Murid
                        </label>
                        <input type="text" class="form-control bg-light" id="studentNameDisplay" readonly disabled>
                    </div>

                    <div class="mb-3">
                        <label for="scoreInput" class="form-label fw-semibold text-secondary">
                            <i class="bi bi-award me-1"></i> Nilai (0-100)
                        </label>
                        <input type="number" class="form-control" name="score" id="scoreInput" min="0" max="100" placeholder="Contoh: 85" required>
                    </div>

                    <div class="mb-3">
                        <label for="feedbackInput" class="form-label fw-semibold text-secondary">
                            <i class="bi bi-chat-left-text me-1"></i> Feedback / Masukan
                        </label>
                        <textarea class="form-control" name="feedback" id="feedbackInput" rows="3" placeholder="Berikan catatan konstruktif untuk murid..."></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Simpan Nilai
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    window.BASE_URL = "<?= base_url(); ?>";
    window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
    window.SLOT_ID = "<?= $slot->observation_id; ?>";
</script>

<script type="module" src="<?= base_url('assets/js/pbl_observasi_detail.js'); ?>"></script>