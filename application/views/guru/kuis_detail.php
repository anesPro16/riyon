<div class="container-fluid py-4">

	<?php if ($this->session->flashdata('import_success')): ?>
		<div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
			<div class="d-flex align-items-center">
				<i class="bi bi-check-circle-fill me-2 fs-4"></i>
				<div>
					<strong>Berhasil!</strong> <?= $this->session->flashdata('import_success'); ?>
				</div>
			</div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<div class="card border-0 shadow-sm mb-4">
		<div class="card-body p-4">
			<div class="row align-items-center">
				<div class="col-md-8">
					<h6 class="text-uppercase text-muted fw-bold small mb-1">
						<i class="bi bi-journal-text me-1"></i> Detail Kuis
					</h6>
					<h3 class="fw-bold text-dark mb-0">
						<?= htmlspecialchars($quiz->subjects, ENT_QUOTES, 'UTF-8'); ?>
					</h3>
				</div>
				<div class="col-md-4 text-md-end mt-3 mt-md-0">
					<a href="<?= base_url('guru/pbl/kuis/' . $quiz->class_id) ?>" class="btn btn-outline-secondary">
						<i class="bi bi-arrow-left me-1"></i> Kembali
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="d-flex flex-wrap gap-2 mb-4">
		<button class="btn btn-primary px-4" id="btnAddQuestion">
			<i class="bi bi-plus-lg me-1"></i> Tambah Soal
		</button>
		<div class="vr mx-2 d-none d-md-block text-muted"></div>
		<a href="<?= base_url('guru/pbl_kuis/export_quiz/' . $quiz->quiz_id) ?>" class="btn btn-success text-white">
			<i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
		</a>
		<button class="btn btn-info text-dark" data-bs-toggle="modal" data-bs-target="#importModal">
			<i class="bi bi-cloud-upload me-1"></i> Import Soal
		</button>
	</div>

	<div class="row g-4">
		<div class="col-lg-12">
			<div class="card border-0 shadow-sm h-100">
				<div class="card-header bg-white py-3 border-bottom">
					<h5 class="m-0 fw-bold text-primary">
						<i class="bi bi-list-check me-2"></i> Daftar Soal
					</h5>
				</div>
				<div class="card-body p-0 kuisContainer">
					<div class="table-responsive">
						<table class="table table-hover align-middle mb-0" id="questionTable">
							<thead class="bg-light">
								<tr>
									<th class="text-secondary text-uppercase small fw-bold py-3 px-3" width="5%">No</th>
									<th class="text-secondary text-uppercase small fw-bold py-3">Soal</th>
									<th class="text-secondary text-uppercase small fw-bold py-3 text-center" width="10%">Kunci Jawaban</th>
									<th class="text-secondary text-uppercase small fw-bold py-3 text-center px-3" width="15%">Aksi</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-12">
			<div class="card shadow-sm h-100">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-award"></i> Daftar Nilai</h5>
      </div>
      <div class="card-body" id="submissionsTableContainer">
        <div class="table-responsive">
          <table class="table table-hover table-striped" id="submissionsTable">
            <thead class="table-light">
              <tr>
                <th width="6%">No</th>
                <th>Murid</th>
                <th>Nilai</th>
                <th>Waktu</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <!-- Diisi oleh JavaScript submissionHandler -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
		</div>
	</div>

</div>

<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content border-0 shadow">
			<form id="questionForm">
				<div class="modal-header bg-primary text-white">
					<h5 class="modal-title fw-bold" id="questionModalLabel">
						<i class="bi bi-pencil-square me-2"></i>Form Soal
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body p-4">
					<input type="hidden" name="id" id="questionId">
					<input type="hidden" name="quiz_id" value="<?= $quiz->quiz_id; ?>">
					<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

					<div class="mb-4">
						<label for="question_text" class="form-label fw-bold">Teks Soal</label>
						<textarea class="form-control" id="question_text" name="question_text" rows="3" placeholder="Tuliskan Soal disini..." required></textarea>
					</div>

					<label class="form-label fw-bold mb-2">Pilihan Jawaban</label>
					<div class="row g-3 mb-4">
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-text bg-light fw-bold">A</span>
								<input type="text" class="form-control" id="option_a" name="option_a" placeholder="Opsi A" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-text bg-light fw-bold">B</span>
								<input type="text" class="form-control" id="option_b" name="option_b" placeholder="Opsi B" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-text bg-light fw-bold">C</span>
								<input type="text" class="form-control" id="option_c" name="option_c" placeholder="Opsi C" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-text bg-light fw-bold">D</span>
								<input type="text" class="form-control" id="option_d" name="option_d" placeholder="Opsi D" required>
							</div>
						</div>
					</div>

					<div class="mb-2">
						<label for="correct_answer" class="form-label fw-bold text-success">Kunci Jawaban</label>
						<select class="form-select border-success" id="correct_answer" name="correct_answer" required>
							<option value="" disabled selected>-- Pilih Jawaban Benar --</option>
							<option value="A">A</option>
							<option value="B">B</option>
							<option value="C">C</option>
							<option value="D">D</option>
						</select>
					</div>
				</div>
				<div class="modal-footer bg-light">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-primary px-4">Simpan Soal</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content border-0 shadow">
			<form action="<?= base_url('guru/pbl_kuis/import_quiz'); ?>" method="post" enctype="multipart/form-data">
				<div class="modal-header bg-info text-white">
					<h5 class="modal-title fw-bold text-dark" id="importModalLabel">
						<i class="bi bi-file-earmark-arrow-up me-2"></i>Import Soal
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body p-4">
					<input type="hidden" name="quiz_id_import" value="<?= $quiz->quiz_id; ?>">
					<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

					<div class="mb-3">
						<label for="import_file" class="form-label fw-bold">Pilih file (Excel/CSV)</label>
						<input class="form-control" type="file" id="import_file" name="import_file" required>
					</div>

					<div class="alert alert-light border small text-muted">
						<i class="bi bi-info-circle-fill text-info me-1"></i>
						Pastikan file memiliki header kolom: 
						<code class="text-dark">question_text</code>, <code class="text-dark">option_a</code>, <code class="text-dark">option_b</code>, <code class="text-dark">option_c</code>, <code class="text-dark">option_d</code>, <code class="text-dark">correct_answer</code>.
					</div>
				</div>
				<div class="modal-footer bg-light">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-info text-dark px-4">Upload File</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	window.QUIZ_ID = "<?= $quiz->quiz_id; ?>";
	window.BASE_URL = "<?= base_url(); ?>";
	window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
</script>
<script type="module" src="<?= base_url('assets/js/kuis_detail.js'); ?>"></script>