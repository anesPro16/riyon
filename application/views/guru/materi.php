<style>


.aksi{
  width: 20%;
}
/* --- Mobile optimization --- */
@media (max-width: 576px) {
  .card-header h5 {
    font-size: 1rem !important;
  }

  .aksi{
    width: 180px;
  }
}


</style>

<div class="container-fluid py-4">

  <div class="pbl-header d-flex justify-content-between align-items-center flex-wrap gap-2">

    <div class="d-flex gap-2">
      <a href="<?= base_url($url_name . '/dashboard/class_detail/' . $class_id) ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
      </a>
      <a href="<?= base_url($url_name . '/pbl/kuis/' . $class_id); ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-list-task"></i> Kuis
      </a>
    </div>
  </div>

  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
  <input type="hidden" id="classIdHidden" value="<?= $class_id; ?>">

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="m-0 font-weight-bold text-primary"><i class="bi bi-journal-text me-2"></i>Daftar Materi</h5>
      <?php if ($is_admin_or_guru): ?>
        <button class="btn btn-sm btn-success" id="btnAddPbl">
          <i class="bi bi-plus-lg"></i> Buat Materi
        </button>
      <?php endif; ?>
    </div>
    
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="pblTable" width="100%">
          <thead class="table-light">
            <tr>
              <th width="5%">No</th>
              <th width="25%">Judul Masalah</th>
              <th width="40%">Deskripsi</th>
              <th width="15%">File</th>
              <?php if ($is_admin_or_guru): ?>
                <th width="15%">Aksi</th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
            </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<?php if ($is_admin_or_guru): ?>
<div class="modal fade" id="pblModal" tabindex="-1" aria-labelledby="pblModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="pblForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="pblModalLabel">Form Materi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="pblId" name="id">
          <input type="hidden" name="class_id" value="<?= $class_id; ?>">

          <div class="mb-3">
            <label for="pblTitle" class="form-label fw-bold">Judul</label>
            <input type="text" class="form-control" name="title" id="pblTitle" placeholder="Judul Materi" required>
          </div>

          <div class="mb-3">
            <label for="pblReflection" class="form-label fw-bold">Deskripsi</label>
            <textarea class="form-control" name="reflection" id="pblReflection" rows="4" placeholder="Deskripsikan masalah" required></textarea>
          </div>

          <div class="mb-3">
            <label for="pblFile" class="form-label fw-bold">Upload Materi</label>
            <input type="file" class="form-control" name="file" id="pblFile" accept=".jpg,.jpeg,.png,.pdf,.mp4">
            <div class="form-text text-xs">Format: JPG, PDF, MP4. Maks 5MB.</div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-eye"></i> Pratinjau Materi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center bg-dark p-0 d-flex align-items-center justify-content-center" style="min-height: 400px; background-color: #f8f9fa !important;">
        <div id="previewContainer" class="w-100 h-100"></div>
      </div>
      <div class="modal-footer justify-content-between">
        <span class="text-muted small" id="previewFilename"></span>
        <a href="javascript:;" id="btnDownload" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-download"></i> Download
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  window.BASE_URL = "<?= base_url(); ?>";
  window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
  window.IS_ADMIN_OR_GURU = <?= $is_admin_or_guru ? 'true' : 'false' ?>;
  window.CURRENT_CLASS_ID = '<?= $class_id; ?>';
</script>

<script type="module" src="<?= base_url('assets/js/materi.js'); ?>"></script>