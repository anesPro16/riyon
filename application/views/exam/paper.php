<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"> -->
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css'); ?>" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="<?= base_url('assets/css/style.css'); ?>" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; user-select: none; /* Mencegah copy paste */ }
        .question-card { min-height: 300px; }
        .nav-btn-outline { width: 40px; height: 40px; border: 1px solid #dee2e6; display: flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 5px; font-weight: bold; }
        .nav-btn-outline.active { background-color: #0d6efd; color: white; border-color: #0d6efd; }
        .nav-btn-outline.answered { background-color: #198754; color: white; border-color: #198754; }
        .nav-btn-outline.active.answered { background-color: #0a58ca; border-color: #0a58ca; } /* Aktif tapi sudah dijawab */
        .timer-box { font-family: 'Courier New', monospace; font-weight: bold; font-size: 1.5rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1"><?= $attempt->exam_name ?></span>
    
    <div class="d-flex align-items-center">
        <div class="bg-white text-dark px-3 py-1 rounded me-3 timer-box" id="timerDisplay">
            --:--:--
        </div>
        <button class="btn btn-outline-light d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav">
            <i class="bi bi-grid-3x3-gap-fill"></i>
        </button>
    </div>
  </div>
</nav>

<div class="container-fluid mt-4">
    <div class="row">
        
        <div class="col-lg-9 mb-4">
            <form id="examPaperForm">
                <input type="hidden" name="attempt_id" value="<?= $attempt->id ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                <?php foreach ($questions as $index => $q) : 
                    $no = $index + 1;
                    $saved_val = isset($saved_answers[$q->id]) ? $saved_answers[$q->id] : '';
                    $display = ($index === 0) ? '' : 'd-none'; // Hanya tampilkan soal pertama
                ?>
                
                <div class="card shadow-sm border-0 question-container <?= $display ?>" id="q_<?= $index ?>" data-index="<?= $index ?>" data-qid="<?= $q->id ?>">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Soal No. <?= $no ?></h5>
                    </div>
                    <div class="card-body question-card">
                        <p class="lead mb-4"><?= nl2br($q->question) ?></p>

                        <div class="list-group">
                            <?php foreach (['a','b','c','d'] as $opt) : 
                                $opt_text = $q->{'option_'.$opt}; 
                                $checked = ($saved_val == strtoupper($opt)) ? 'checked' : '';
                            ?>
                            <label class="list-group-item list-group-item-action d-flex align-items-center p-3 border rounded mb-2">
                                <input class="form-check-input me-3 answer-radio" type="radio" 
                                       name="ans_<?= $q->id ?>" 
                                       value="<?= strtoupper($opt) ?>" 
                                       <?= $checked ?>>
                                <span class="fw-bold me-2"><?= strtoupper($opt) ?>.</span>
                                <span><?= $opt_text ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary btn-prev" <?= ($index==0)?'disabled':'' ?>><i class="bi bi-arrow-left"></i> Sebelumnya</button>
                        
                        <?php if ($index == count($questions) - 1) : ?>
                            <button type="button" class="btn btn-success btn-finish"><i class="bi bi-check-circle"></i> Selesai Ujian</button>
                        <?php else : ?>
                            <button type="button" class="btn btn-primary btn-next">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </form>
        </div>

        <div class="col-lg-3 d-none d-lg-block">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">Navigasi Soal</div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 justify-content-center" id="navContainerDesktop">
                        </div>
                </div>
                <div class="card-footer bg-light small">
                    <span class="badge bg-secondary">Putih</span> Belum dijawab<br>
                    <span class="badge bg-success">Hijau</span> Sudah dijawab<br>
                    <span class="badge bg-primary">Biru</span> Sedang dibuka
                </div>
            </div>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNav">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Navigasi Soal</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div class="d-flex flex-wrap gap-2 justify-content-center" id="navContainerMobile"></div>
  </div>
</div>

<script>
    window.BASE_URL = "<?= base_url() ?>";
    window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name() ?>";
    window.TOTAL_QUESTIONS = <?= count($questions) ?>;
    // Waktu Selesai (Server Time format ISO)
    window.END_TIME = "<?= str_replace(' ', 'T', $attempt->end_time) ?>"; 
</script>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/sweetalert.js') ?>"></script>
<script type="module" src="<?= base_url('assets/js/exam_paper.js') ?>"></script>

</body>
</html>