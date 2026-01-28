<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <p class="text-muted"><?= htmlspecialchars($quiz->description, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <a href="<?= base_url('guru/pbl/tahap4/' . $class_id) ?>" class="btn btn-secondary">← Kembali ke Tahap 4</a>
  </div>


  <button class="btn btn-primary my-3" id="btnAddQuestion">
    <i class="bi bi-plus-lg"></i> Tambah Pertanyaan
  </button>

  <div class="card shadow-sm">
    <div class="card-header">
      <h5 class="mb-0">Daftar Pertanyaan</h5>
    </div>
    <div class="card-body" id="questionTableContainer"> <table class="table table-hover" id="questionTable">
        <thead>
          <tr>
            <th style="width: 5%;">No</th>
            <th>Pertanyaan</th>
            <th style="width: 10%;">Jawaban</th>
            <th style="width: 15%;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="questionForm">
        <div class="modal-header">
          <h5 class="modal-title" id="questionModalLabel">Tambah Pertanyaan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="questionId">
          <input type="hidden" name="quiz_id" value="<?= $quiz->id; ?>">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" 
                 value="<?= $this->security->get_csrf_hash(); ?>">

          <div class="mb-3">
            <label for="question_text" class="form-label">Teks Pertanyaan</label>
            <textarea class="form-control" id="question_text" name="question_text" rows="3" required></textarea>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="option_a" class="form-label">Opsi A</label>
              <input type="text" class="form-control" id="option_a" name="option_a" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="option_b" class="form-label">Opsi B</label>
              <input type="text" class="form-control" id="option_b" name="option_b" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="option_c" class="form-label">Opsi C</label>
              <input type="text" class="form-control" id="option_c" name="option_c" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="option_d" class="form-label">Opsi D</label>
              <input type="text" class="form-control" id="option_d" name="option_d" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="correct_answer" class="form-label">Jawaban Benar</label>
            <select class="form-select" id="correct_answer" name="correct_answer" required>
              <option value="" disabled selected>-- Pilih Jawaban --</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Kirim data dari PHP ke JavaScript
  window.CURRENT_QUIZ_ID = "<?= $quiz->id; ?>";
  window.BASE_URL = "<?= base_url(); ?>";
  window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
</script>
<script type="module" src="<?= base_url('assets/js/pbl_kuis_evaluasi_detail.js'); ?>"></script>