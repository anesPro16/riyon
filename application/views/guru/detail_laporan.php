<style>
  /* Styling Tabel Custom */
  .table-custom {
    border: 1px solid #000 !important;
    width: 100%;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
    font-size: 13px; /* Font agak diperkecil agar muat */
  }
  .table-custom th, .table-custom td {
    border: 1px solid #000 !important;
    padding: 6px;
    vertical-align: middle;
    text-align: center;
  }
  
  /* Warna Header */
  .bg-header-main { background-color: #f0f0f0; font-weight: bold; }
  
  /* Warna Kolom Mapel Bergantian agar mudah dibaca */
  .col-group-1 { background-color: #e3f2fd; } /* Biru Muda */
  .col-group-2 { background-color: #fff3e0; } /* Oranye Muda */
</style>

<div class="container-fluid py-3">

  <div class="pbl-header d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
      <!-- <a href="</?= base_url($url_name . '/pbl/tahap4/' . $class_id) ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Tahap 4
      </a> -->
      <a href="<?= base_url('guru/laporan'); ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
      </a>
    </div>
  </div>

  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
  
  <div class="card shadow-sm">
    <div class="card-header bg-white">
      <h5 class="m-0 font-weight-bold text-primary"><i class="bi bi-table me-2"></i> Rekapitulasi Nilai Akhir (Rata-rata)</h5>
    </div>

    <div class="card-body p-3">
      <div class="table-responsive">
        
        <table class="table-custom" id="rekapTable">
          <thead>
            <tr class="bg-header-main">
              <th rowspan="2" width="40">No.</th>
              <th rowspan="2" style="min-width: 150px; text-align: left;">Nama Siswa</th>
              
              <?php if(!empty($exam_subjects)) : ?>
                <?php $i = 0; foreach($exam_subjects as $subject): $i++; 
                  // Selang-seling warna header mapel
                  $bgClass = ($i % 2 == 0) ? 'col-group-2' : 'col-group-1';
                ?>
                  <th colspan="5" class="<?= $bgClass ?>"><?= $subject ?></th>
                <?php endforeach; ?>
              <?php endif; ?>

              <th rowspan="2" width="60">Total</th>
              <th rowspan="2" width="100">Aksi</th>
            </tr>

            <tr class="bg-header-main">
              <?php if(!empty($exam_subjects)) : ?>
                <?php $i = 0; foreach($exam_subjects as $subject): $i++;
                   $bgClass = ($i % 2 == 0) ? 'col-group-2' : 'col-group-1';
                ?>
                  <th class="<?= $bgClass ?>" style="font-size:11px;">UTS</th>
                  <th class="<?= $bgClass ?>" style="font-size:11px;">UAS</th>
                  <th class="<?= $bgClass ?>" style="font-size:11px;">Kuis</th>
                  <th class="<?= $bgClass ?>" style="font-size:11px;">Obs</th>
                  <th class="<?= $bgClass ?>" style="font-size:11px;">Esai</th>
                <?php endforeach; ?>
              <?php endif; ?>
            </tr>
          </thead>
          
          <tbody id="rekapTableBody">
             <tr><td colspan="100%" class="text-center p-4">Memuat data...</td></tr>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="refleksiModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="refleksiForm" autocomplete="off">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="refleksiModalLabel">Input Refleksi</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" id="modalUserId">
          <input type="hidden" name="class_id" value="<?= $class_id; ?>">
          <div class="mb-3">
            <label class="fw-bold">Siswa:</label>
            <input type="text" class="form-control-plaintext fw-bold" id="modalStudentName" readonly>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Refleksi Guru</label>
              <textarea name="teacher_reflection" class="form-control" rows="4"></textarea>
            </div>
            <div class="col-md-6 mb-3">
              <label>Feedback Siswa</label>
              <textarea name="student_feedback" class="form-control" rows="4"></textarea>
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

<script>
  window.BASE_URL = "<?= base_url(); ?>";
  window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
  window.CURRENT_CLASS_ID = '<?= $class_id; ?>';
  window.EXAM_SUBJECTS = <?= json_encode($exam_subjects); ?>;
</script>
<script type="module" src="<?= base_url('assets/js/detail_laporan.js'); ?>"></script>