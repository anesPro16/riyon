<script>
  window.BASE_URL = "<?= base_url() ?>";
  window.CURRENT_CLASS_ID = "<?= $class_id ?>";
  window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name() ?>";
</script>

<div class="container-fluid py-4">
    <div class="d-flex gap-2">
      <!-- <a href="</?= base_url($url_name . '/pbl/tahap4/' . $class_id) ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>Tahap 4
      </a> -->
      <?php if ($url_name == 'guru'): ?>
        <a href="<?= base_url('exam/index/' . $class_id); ?>" class="btn btn-primary btn-sm">
          <i class="bi bi-list-task"></i>Ujian
        </a>

      <?php else: ?>
         <!-- <a href="</?= base_url($url_name . '/pbl/tahap5/' . $class_id); ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-list-task"></i>Tahap 5
      </a> -->
      <?php endif ?>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary"><i class="bi bi-journal-text"></i> Daftar Ujian Tersedia</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <div>
                    Hanya ujian yang berstatus <b>Aktif</b> dan dalam rentang waktu pengerjaan yang akan muncul di sini.
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="studentExamTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Mata Pelajaran</th>
                            <th>Jenis</th>
                            <th>Batas Waktu</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="module" src="<?= base_url('assets/js/student_exam_modules.js') ?>"></script>