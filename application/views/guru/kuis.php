<style>
/* ===== PBL TAHAP 2 – UI + ANIMATION ENHANCEMENT ===== */

/* --- Empty State --- */
#quizEmptyState {
  border: 2px dashed #dee2e6 !important;
  border-radius: .75rem !important;
  background: #fafafa !important;
}

#quizEmptyState i {
  opacity: .6 !important;
}

.aksi{
  width: 24%;
}
/* --- Mobile optimization --- */
/*@media (max-width: 576px) {
  .card-header h5 {
    font-size: 1rem !important;
  }

  .btn-add-quiz {
    font-size: .8rem !important;
  }

  .aksi{
    width: 180px;
  }
}*/


</style>

<div class="container-fluid py-3">

  <!-- ===== HEADER ===== -->
  <div class="pbl-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <!-- <div>
      <h4 class="mb-1">
        <i class="bi bi-diagram-3 me-2 text-primary"></i>
        Tahap 2 – Organisasi Belajar
      </h4>
      <span class="badge bg-info pbl-badge">Project Based Learning</span>
    </div> -->

    <div class="d-flex gap-2">
      <a href="<?= base_url($url_name . '/pbl/index/' . $class_id) ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Materi
      </a>
      <a href="<?= base_url($url_name . '/pbl/observasi/' . $class_id); ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-list-task"></i> Observasi
      </a>
    </div>
  </div>

  <!-- CSRF -->
  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
         value="<?= $this->security->get_csrf_hash(); ?>">
  <input type="hidden" id="classIdHidden" value="<?= $class_id; ?>">

  <!-- ===== KONTEN UTAMA : KUIS ===== -->
  <div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="m-0 font-weight-bold text-primary">
        <i class="bi bi-card-checklist me-2"></i>
        Daftar Kuis
      </h5>
      <?php if ($this->session->userdata('role') === 'Guru'): ?>
        <button class="btn btn-primary btn-sm btn-add-quiz" id="btnAddQuiz">
          <i class="bi bi-plus-circle"></i> Buat Kuis
        </button>
      <?php endif ?>
    </div>

    <div class="card-body p-0 px-2 quizTableContainer">
      <div class="table-responsive">
        <!-- TABLE -->
        <table class="table mb-0" id="quizTable">
          <thead class="table-light">
            <tr>
              <th width="6%">No</th>
              <th>Kuis</th>
              <th>Mata Pelajaran </th>
              <!-- <th style="width:20%" class="aksi">Aksi</th> -->
              <th class="aksi">Aksi</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<!-- ===== MODAL ===== -->
<div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow border-0">
      <form id="quizForm" autocomplete="off">

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="quizModalLabel">
            <i class="bi bi-pencil-square me-2"></i> Form Kuis
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <input type="hidden" name="quiz_id" id="quizId">
          <input type="hidden" name="class_id" id="quizClassId" value="<?= $class_id; ?>">

          <div class="mb-3">
            <label class="form-label fw-semibold">Kuis</label>
            <input type="text" name="title" id="quizTitle" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Mata Pelajaran</label>
              <select name="subjects" id="subjects" class="form-select" required>
                <option value="">-- Pilih Mapel --</option>
                <?php foreach ($subjects as $sub) : ?>
                  <option value="<?= $sub ?>"><?= $sub ?></option>
                <?php endforeach; ?>
              </select>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Simpan
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
  window.BASE_URL = "<?= base_url(); ?>";
  window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
  window.IS_ADMIN_OR_GURU = <?= $is_admin_or_guru ? 'true' : 'false' ?>;
  window.CURRENT_CLASS_ID = '<?= $class_id; ?>';
  window.URL_NAME = '<?= $url_name; ?>';
</script>


<script type="module" src="<?= base_url('assets/js/kuis.js'); ?>"></script>
