<style>
/* Custom Table Style agar mirip Excel/Laporan */
.table-report {
  width: 100%;
  border-collapse: collapse;
  font-family: Arial, sans-serif;
}
.table-report th, .table-report td {
  border: 1px solid #000 !important; /* Border Hitam */
  padding: 10px;
  text-align: center;
  vertical-align: middle;
}
.table-report thead th {
  background-color: #f8f9fa;
  font-weight: bold;
}
.text-left { text-align: left !important; }
.bg-total { background-color: #e9ecef; font-weight: bold; }
</style>

<div class="container-fluid py-3">

  <!-- <div class="pbl-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div class="d-flex gap-2">
      <a href="</?= base_url($url_name . '/pbl/tahap4/' . $class_id) ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Tahap 4
      </a>
    </div>
  </div> -->

  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
  <input type="hidden" id="currentUserId" value="<?= $user['user_id']; ?>"> 

  <div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <!-- <div class="card-header bg-white py-3">
                <h5 class="mb-0 card-title text-primary"><i class="bi bi-journal-text"></i> Laporan Hasil Belajar</h5>
            </div> -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table-report" id="nilaiTable">
                        <thead>
                            <tr>
                                <th width="25%">Mata Pelajaran</th>
                                <th>UTS</th>
                                <th>UAS</th>
                                <th>Kuis</th>
                                <th>Observasi</th>
                                <th>Esai</th>
                                <th>Rata-Rata</th>
                            </tr>
                        </thead>
                        <tbody id="nilaiTableBody">
                            <tr><td colspan="7" class="p-4">Memuat data nilai...</td></tr>
                        </tbody>
                        <tfoot id="nilaiTableFoot">
                            </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-success py-3">
                <h5 class="mb-0 card-title text-white"><i class="bi bi-chat-quote"></i> Refleksi Guru</h5>
            </div>
            <div class="card-body bg-light">
                <div class="mb-4">
                    <h6 class="fw-bold text-secondary text-uppercase small">Catatan Refleksi Guru</h6>
                    <div class="p-3 bg-white rounded border shadow-sm" id="viewTeacherReflection" style="min-height: 100px; white-space: pre-wrap;">- Belum ada catatan -</div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold text-secondary text-uppercase small">Feedback Untuk Anda</h6>
                    <div class="p-3 bg-white rounded border border-success shadow-sm" id="viewStudentFeedback" style="min-height: 100px; white-space: pre-wrap;">- Belum ada feedback -</div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

<script>
	window.BASE_URL = "<?= base_url(); ?>";
	window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
	window.CURRENT_CLASS_ID = '<?= $class_id; ?>';
    // Array Mapel dari PHP Controller
	window.EXAM_SUBJECTS = <?= json_encode($exam_subjects); ?>;
</script>
<script type="module" src="<?= base_url('assets/js/siswa/pbl_tahap5.js'); ?>"></script>