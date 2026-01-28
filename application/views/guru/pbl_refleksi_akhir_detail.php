<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><?= $title; ?></h4>
    <a href="<?= base_url('guru/pbl/tahap5/' . $class_id) ?>" class="btn btn-secondary">← Kembali ke Tahap 5</a>
  </div>

  <div class="card shadow-sm mb-3 bg-light">
    <div class="card-body">
        <label class="fw-bold text-muted small text-uppercase">Instruksi Refleksi:</label>
        <p class="mb-0"><?= nl2br(htmlspecialchars($reflection->description)); ?></p>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0 text-primary">Daftar Pertanyaan Refleksi</h5>
      <button class="btn btn-primary btn-sm" id="btnAddPrompt">
        <i class="bi bi-plus-lg"></i> Tambah Pertanyaan
      </button>
    </div>
    <div class="card-body" id="promptsTableContainer">
      <table class="table table-hover" id="promptsTable">
        <thead class="table-light">
          <tr>
            <th style="width: 5%;">No</th>
            <th>Teks Pertanyaan (Prompt)</th>
            <th style="width: 15%;">Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0 text-success">Jawaban Refleksi Siswa</h5>
    </div>
    <div class="card-body" id="submissionsTableContainer">
      <table class="table table-hover" id="submissionsTable">
        <thead class="table-light">
          <tr>
            <th style="width: 5%;">No</th>
            <th>Nama Siswa</th>
            <th>Waktu Submit</th>
            <th style="width: 15%;">Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

</div>

<div class="modal fade" id="promptModal" tabindex="-1" aria-labelledby="promptModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="promptForm">
        <div class="modal-header">
          <h5 class="modal-title" id="promptModalLabel">Tambah Pertanyaan Refleksi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="promptId">
          <input type="hidden" name="reflection_id" value="<?= $reflection->id; ?>">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" 
                 value="<?= $this->security->get_csrf_hash(); ?>">

          <div class="mb-3">
            <label for="prompt_text" class="form-label">Teks Pertanyaan (Prompt)</label>
            <textarea class="form-control" id="prompt_text" name="prompt_text" rows="5" required></textarea>
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

<div class="modal fade" id="viewSubmissionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Jawaban Refleksi: <span id="studentNameTitle"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="submissionContentArea"></div>
        
        <div id="promptsData" data-prompts='<?= json_encode($prompts); ?>' style="display:none;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script>
  window.CURRENT_REFLECTION_ID = "<?= $reflection->id; ?>";
  window.BASE_URL = "<?= base_url(); ?>";
  window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
</script>
<script type="module" src="<?= base_url('assets/js/pbl_refleksi_akhir_detail.js'); ?>"></script>