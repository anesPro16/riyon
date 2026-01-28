<script>
  window.BASE_URL = "<?= base_url() ?>";
  window.CURRENT_CLASS_ID = "<?= $class_id ?>";
  window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name() ?>";
</script>

<div class="card">
  <div class="pbl-header d-flex justify-content-between align-items-center flex-wrap gap-2 my-3 mx-3">
    <div class="d-flex gap-2">
      <a href="<?= base_url($url_name . '/pbl/tahap4/' . $class_id) ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Tahap 4
      </a>
      <a href="<?= base_url($url_name . '/pbl/tahap5/' . $class_id); ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-list-task"></i> Tahap 5
      </a>
    </div>
  </div>
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Daftar Ujian</h5>
    <button class="btn btn-primary btn-sm" id="btnAddExam">
      <i class="bi bi-plus-circle"></i> Tambah Ujian
    </button>
  </div>

  <div class="card-body table-responsive">
    <table class="table table-hover" id="examTable">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Mata Pelajaran</th> <th>Jenis</th>
          <th>Waktu</th>
          <th>Status</th> <th width="150">Aksi</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="examModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form id="examForm">
        <input type="hidden" name="exam_id" id="exam_id">
        <input type="hidden" name="class_id" value="<?= $class_id ?>">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

        <div class="modal-header">
          <h5 class="modal-title" id="examModalLabel">Form Ujian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Mata Pelajaran</label>
              <select name="exam_name" id="examName" class="form-select" required>
                <option value="">-- Pilih Mapel --</option>
                <?php foreach ($subjects as $sub) : ?>
                  <option value="<?= $sub ?>"><?= $sub ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Jenis Ujian</label>
              <select name="type" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="UTS">UTS</option>
                <option value="UAS">UAS</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Mulai</label>
              <input type="datetime-local" name="start_time" id="startTime" class="form-control" required>
              <small class="text-muted">Tidak boleh waktu lampau</small>
            </div>

            <div class="col-md-6">
              <label class="form-label">Selesai</label>
               <input type="datetime-local" name="end_time" id="endTime" class="form-control" required>
            </div>

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

<script type="module" src="<?= base_url('assets/js/exam_modules.js') ?>"></script>