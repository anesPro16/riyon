<style>

.aksi{
  width: 27%;
}

</style>

<div class="container-fluid py-3">

  <!-- ===== HEADER ===== -->
  <div class="pbl-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="d-flex gap-2">
      <a href="<?= base_url($url_name . '/pbl/kuis/' . $class_id) ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kuis
      </a>
      <a href="<?= base_url($url_name . '/pbl/esai/' . $class_id); ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-list-task"></i> Esai
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
        Daftar Ruang Upload
      </h5>
      <?php if ($this->session->userdata('role') === 'Guru'): ?>
        <button class="btn btn-primary btn-sm btn-add-quiz" id="btnAddObservasi">
          <i class="bi bi-plus-circle"></i> Buat Ruang
        </button>
      <?php endif ?>
    </div>

    <div class="card-body p-0 px-2" id="observasiContainer">
      <div class="table-responsive">
        <!-- TABLE -->
        <table class="table mb-0" id="observasiTable">
          <thead class="table-light">
            <tr>
              <th width="6%">No</th>
              <th>Judul</th>
              <th>Mata Pelajaran</th>
              <th>Instruksi</th>
              <th class="aksi">Aksi</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<!-- Modal 1: Observasi -->
<div class="modal fade" id="observasiModal" tabindex="-1" aria-labelledby="observasiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg border-0">
      <form id="observasiForm" autocomplete="off">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title mb-0" id="observasiModalLabel">Form Ruang Observasi</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <input type="hidden" name="id" id="observasiId">
          <input type="hidden" name="class_id" value="<?= $class_id; ?>">
          
          <div class="mb-3">
            <label for="observasiTitle" class="form-label">Judul Ruang</label>
            <input type="text" name="title" id="observasiTitle" class="form-control" required>
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
          <div class="mb-3">
            <label for="instructionLabel" class="form-label">Instruksi</label>
            <input type="text" name="instruction" id="instructionLabel" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
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
<script type="module" src="<?= base_url('assets/js/observasi.js'); ?>"></script>