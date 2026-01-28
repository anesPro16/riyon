<style>
  .num-label {
    position: absolute;
    top: 1px;
    left: 2px;
    font-size: 9px;
    color: #333;
    pointer-events: none; /* [TAMBAHAN] Agar label tidak mengganggu klik sel */
  }

  .num-label.down {
    left: auto;
    right: 1px;
  }

  .tts-cell {
    text-align: center;
    line-height: 10px;
    font-weight: bold;
    cursor: pointer;
    background-color: #fff;
    transition: 0.1s;
    position: relative; 
  }

  .letter-span {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none; /* Biarkan klik tembus ke sel */
  }

  .tts-cell.selected-cell {
    background-color: #b6d4fe !important;
    border: 2px solid #0d6efd;
  }

  .tts-cell.highlight-cell {
    background-color: #d1e7ff;
  }

  .editing-mode .tts-cell:hover {
    background-color: #e3f2fd;
  }
</style>

<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= base_url('guru/pbl/tahap2/' . $class_id) ?>" class="btn btn-secondary">← Kembali</a>
  </div>

  <input type="hidden" id="ttsIdHidden" value="<?= $tts->id; ?>">
  <input type="hidden" id="classIdHidden" value="<?= $class_id; ?>">
  <input type="hidden" id="ttsGridSize" value="<?= (int)$tts->grid_size; ?>">
  
  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

  <!-- <div class="form-check mb-2">
    <input type="checkbox" class="form-check-input" id="toggleGridEdit">
    <label class="form-check-label" for="toggleGridEdit">
      Mode Editor Interaktif (Drag Grid)
    </label>
  </div> -->


  <div class="card shadow mb-4">
    <div class="card-body">
      <h5><?= htmlspecialchars($tts->title, ENT_QUOTES, 'UTF-8'); ?></h5>
      <p><strong>Preview Grid:</strong></p>
      <p>Silakan pilih kotak terlebih dahulu!</p>
      <!-- <div id="ttsGridPreview" class="border p-3 mb-3" style="display:grid; grid-template-columns: repeat(10, 25px); gap:2px;"> -->
      <div id="ttsGridPreview" class="border p-3 mb-3" style="display:grid; justify-content: center; gap:2px;">
      </div>
      <button class="btn btn-primary btn-sm mb-3" id="btnAddQuestion">+ Tambah Pertanyaan</button>

      <table class="table table-bordered" id="questionTable">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Nomor</th>
            <th>Arah</th>
            <th>Pertanyaan</th>
            <th>Jawaban</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="questionModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="questionForm">
        <div class="modal-header">
          <h5 class="modal-title" id="questionModalLabel">Tambah Pertanyaan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="questionId">
          <input type="hidden" name="tts_id" value="<?= $tts->id; ?>">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" 
                 value="<?= $this->security->get_csrf_hash(); ?>">

          <div class="mb-3">
            <label class="form-label">Nomor</label>
            <input type="number" name="number" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Arah</label>
            <select name="direction" class="form-select" required>
              <option value="across">Mendatar</option>
              <option value="down">Menurun</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Pertanyaan</label>
            <textarea name="question" class="form-control" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Jawaban</label>
            <input type="text" name="answer" class="form-control text-uppercase" required>
          </div>
        </div>
        <div class="mb-3 d-flex gap-2">
          <div style="flex:1;">
            <label class="form-label">Koordinat X (Kolom)</label>
            <input type="number" name="start_x" class="form-control" min="1" max="<?= (int)$tts->grid_size; ?>" readonly required>
          </div>
          <div style="flex:1;">
            <label class="form-label">Koordinat Y (Baris)</label>
            <input type="number" name="start_y" class="form-control" min="1" max="<?= (int)$tts->grid_size; ?>" readonly required>
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
</script>
<script type="module" src="<?= base_url('assets/js/tts_detail.js'); ?>"></script>