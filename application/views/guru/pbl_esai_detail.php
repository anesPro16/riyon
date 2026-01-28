<style>
    /* Custom Responsive Column Width */
    .aksi { width: 15%; }

    @media (max-width: 1051px) {
        .aksi { width: 22%; }
    }

    @media (max-width: 768px) {
        #questionTable thead th, #gradingTable thead th {
            white-space: nowrap;
        }
    }
    
    /* Scrollable Answer Box in Modal */
    .answer-box {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        min-height: 250px;
        max-height: 50vh;
        overflow-y: auto;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    /* Area Scrollable untuk Container Soal */
    #dynamicQuestionContainer {
        max-height: 60vh; /* Agar modal tidak terlalu panjang melebihi layar */
        overflow-y: auto;
        padding: 5px; /* Space untuk shadow agar tidak terpotong */
        scrollbar-width: thin;
    }

    /* Manipulasi Tampilan Baris Soal yang digenerate JS (Class .question-row) */
    #dynamicQuestionContainer .question-row {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        padding: 0;
        margin-bottom: 1rem !important; /* Override mb-2 dari JS agar jarak lebih lega */
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    #dynamicQuestionContainer .question-row:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
        border-color: #cbd5e1;
    }

    /* Label "Soal" di kiri */
    #dynamicQuestionContainer .input-group-text {
        background-color: #f8fafc;
        border: none;
        border-right: 1px solid #e2e8f0;
        border-radius: 8px 0 0 8px !important;
        color: #64748b;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Textarea Input */
    #dynamicQuestionContainer textarea.form-control {
        border: none;
        padding: 12px;
        resize: vertical;
        font-size: 1rem;
        color: #334155;
    }
    #dynamicQuestionContainer textarea.form-control:focus {
        box-shadow: none; /* Hilangkan glow default bootstrap */
        background-color: #fff;
    }

    /* Tombol Hapus (Trash) */
    #dynamicQuestionContainer .btn-outline-danger {
        border: none;
        border-left: 1px solid #e2e8f0;
        border-radius: 0 8px 8px 0 !important;
        color: #ef4444;
        display: flex;
        align-items: center;
        padding: 0 15px;
    }
    #dynamicQuestionContainer .btn-outline-danger:hover {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    /* Tombol Tambah Baris (Dashed) */
    .btn-dashed-add {
        border: 2px dashed #cbd5e1;
        background-color: #f8fafc;
        color: #64748b;
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-dashed-add:hover {
        border-color: #3b82f6;
        background-color: #eff6ff;
        color: #1d4ed8;
    }
</style>

<div class="container-fluid py-4">

    <input type="hidden" id="currentEssayId" value="<?= $essay->essay_id; ?>">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-lg-8">
                    <h5 class="fw-bold text-dark mb-2">
                        <i class="bi bi-journal-text me-2 text-primary"></i>Instruksi Esai
                    </h5>
                    <div class="text-muted fs-6">
                        <?= nl2br(htmlspecialchars($essay->subjects, ENT_QUOTES, 'UTF-8')); ?>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0 border-start-lg">
                    <a href="<?= base_url('guru/pbl/esai/' . $class_id) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-question-circle me-2"></i> Daftar Soal
                    </h6>
                    <button class="btn btn-primary btn-sm px-3" id="btnAddQuestion">
                        <i class="bi bi-plus-lg me-1"></i> Buat Soal
                    </button>
                </div>
                <div class="card-body p-0" id="questionTableContainer">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="questionTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-4 text-secondary text-uppercase small fw-bold border-bottom-0" width="10%">No</th>
                                    <th class="py-3 text-secondary text-uppercase small fw-bold border-bottom-0">Soal</th>
                                    <th class="py-3 px-4 text-secondary text-uppercase small fw-bold border-bottom-0 text-end aksi">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="bi bi-people-fill me-2"></i> Jawaban & Nilai Siswa
                    </h6>
                </div>
                <div class="card-body p-0" id="gradingTableContainer">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="gradingTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-4 text-secondary text-uppercase small fw-bold border-bottom-0" width="5%">No</th>
                                    <th class="py-3 text-secondary text-uppercase small fw-bold border-bottom-0">Nama Siswa</th>
                                    <th class="py-3 text-secondary text-uppercase small fw-bold border-bottom-0">Waktu Kirim</th>
                                    <th class="py-3 text-secondary text-uppercase small fw-bold border-bottom-0 text-center">Nilai</th>
                                    <th class="py-3 px-4 text-secondary text-uppercase small fw-bold border-bottom-0 text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="questionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            
            <form id="questionForm">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="questionModalLabel">
                            <i class="bi bi-pencil-square text-primary me-2"></i>Form Soal Esai
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Tambahkan atau edit pertanyaan esai di bawah ini.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 pt-4 pb-2">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id" id="questionId">
                    <input type="hidden" name="essay_id" value="<?= $essay->essay_id; ?>">
                    
                    <div id="dynamicQuestionContainer" class="mb-3 custom-scrollbar">
                        </div>

                    <div class="mt-2 mb-2" id="btnAddRowWrapper">
                        <button type="button" class="btn btn-dashed-add" id="btnAddRow">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Baris Soal Baru
                        </button>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4 pt-2 bg-white">
                    <button type="button" class="btn btn-light text-secondary px-4 fw-medium" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="gradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="gradeForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="gradeModalLabel">
                        <i class="bi bi-check2-circle me-2"></i>Input Penilaian
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body p-0">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="submission_id" id="submissionId">
                    
                    <div class="row g-0">
                        <div class="col-md-7 border-end bg-light p-4">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="bi bi-file-text me-1"></i> Hasil Jawaban
                            </h6>
                            <div class="card border shadow-sm">
                                <div class="card-body answer-scroll-area bg-white text-dark" 
                                     id="studentAnswerContent" 
                                     style="min-height: 300px; max-height: 450px; overflow-y: auto; line-height: 1.7; font-size: 0.95rem;">
                                     </div>
                            </div>
                        </div>

                        <div class="col-md-5 p-4 bg-white">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-sliders me-1"></i> Form Guru
                            </h6>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-secondary small text-uppercase">Nilai</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white border-primary fw-bold">Score</span>
                                    <input type="number" name="grade" id="gradeInput" class="form-control border-primary text-center fw-bold fs-5" min="0" max="100" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small text-uppercase">Catatan</label>
                                <textarea name="feedback" id="feedbackInput" class="form-control bg-light" rows="8" placeholder="Tuliskan masukan untuk siswa di sini..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
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
</script>
<script type="module" src="<?= base_url('assets/js/pbl_esai_detail.js'); ?>"></script>