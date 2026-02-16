<style>
    /* === Custom Styles untuk Kuis === */
    
    /* Card Soal */
    .question-card {
        background: #fff;
        border: 1px solid #eef2f7;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        padding: 2rem;
        margin-bottom: 1.5rem;
        transition: box-shadow 0.2s;
        position: relative; /* Untuk positioning emote */
    }
    .question-card:hover {
        box-shadow: 0 8px 15px rgba(0,0,0,0.05);
    }

    /* Opsi Jawaban (Radio Button Style) */
    .option-hover {
        transition: all 0.2s;
        border: 2px solid #eef2f7 !important;
        border-radius: 8px !important;
    }
    .form-check-input:checked + .option-hover {
        border-color: #4e73df !important;
        background-color: #f8f9fc;
        color: #2e59d9;
    }
    .option-hover:hover {
        background-color: #f8f9fc;
        cursor: pointer;
    }

    /* Review Mode Styles */
    .review-correct {
        border-left: 5px solid #1cc88a !important;
        background-color: #fff;
    }
    .review-wrong {
        border-left: 5px solid #e74a3b !important;
        background-color: #fff;
    }
    
    /* Emote Badge di pojok kanan atas kartu */
    .emote-badge {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 1.5rem;
        opacity: 0.8;
    }

    /* List Group Item Custom untuk Review */
    .list-group-item {
        border: 1px solid #f1f3f5;
        margin-bottom: 5px;
        border-radius: 8px !important;
    }

    /* Sembunyikan Header Tabel CrudHandler */
    #questionsTable thead { display: none; }
    /* Reset padding tabel agar card menempel rapi */
    #questionsTable td { padding: 0; border: none; }
    
    /* Score Hero Section */
    .score-card {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    .score-circle {
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: bold;
        margin: 0 auto;
        border: 4px solid rgba(255,255,255,0.5);
    }
</style>

<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="fw-bold text-primary mb-1">
                        <i class="bi bi-pencil-square me-2"></i>Kuis Pembelajaran
                    </h5>
                    <h3 class="fw-bold text-dark mb-1">
                        <?= htmlspecialchars($quiz->description ?? 'Detail Kuis', ENT_QUOTES, 'UTF-8'); ?>
                    </h3>
                    <p class="text-muted mb-0 small">
                        Silakan kerjakan soal di bawah ini dengan teliti.
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="<?= base_url('siswa/pbl/kuis/' . $class_id) ?>" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if ($result): ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="score-card shadow text-center">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <h5 class="text-white-50 text-uppercase mb-2">Nilai Anda</h5>
                            <div class="score-circle">
                                <?= $result->score; ?>
                            </div>
                        </div>
                        <div class="col-md-8 text-md-start">
                            <h4 class="fw-bold mb-3">
                                <?= ($result->score >= 70) ? 'Selamat! Hasil yang memuaskan.' : 'Tetap Semangat! Tingkatkan belajarmu.'; ?>
                            </h4>
                            <div class="d-flex gap-3 justify-content-center justify-content-md-start">
                                <div class="bg-white bg-opacity-10 px-4 py-2 rounded-3">
                                    <span class="d-block small text-white-50">Jawaban Benar</span>
                                    <span class="fs-4 fw-bold"><i class="bi bi-check-circle-fill me-1"></i> <?= $result->total_correct; ?></span>
                                </div>
                                <div class="bg-white bg-opacity-10 px-4 py-2 rounded-3">
                                    <span class="d-block small text-white-50">Total Soal</span>
                                    <span class="fs-4 fw-bold"><i class="bi bi-justify me-1"></i> <?= $result->total_questions; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-light border-start border-4 border-info shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle-fill text-info fs-4 me-3"></i>
                <div>
                    <strong>Review Jawaban:</strong> Di bawah ini adalah detail jawaban Anda. Jawaban yang benar ditandai dengan warna hijau.
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form id="quizSubmissionForm">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        
        <div id="questionsTableContainer">
            <table class="table table-borderless w-100" id="questionsTable">
                <thead><tr><th>Data</th></tr></thead>
                <tbody>
                    </tbody>
            </table>
        </div>

        <?php if (!$result): ?>
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body p-4 text-center">
                    <p class="text-muted mb-3">Pastikan semua jawaban telah terisi sebelum mengirim.</p>
                    <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm" id="btnSubmitQuiz">
                        <i class="bi bi-send-fill me-2"></i> Selesai
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </form>
</div>

<script>
    window.BASE_URL = "<?= base_url(); ?>";
    window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
    window.QUIZ_ID = "<?= $quiz->quiz_id; ?>";
    window.IS_DONE = <?= $is_done ? 'true' : 'false'; ?>; 
</script>

<script type="module" src="<?= base_url('assets/js/siswa/kuis_detail.js'); ?>"></script>