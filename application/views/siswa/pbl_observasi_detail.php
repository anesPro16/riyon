<div class="container py-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<div>
			<p class="text-muted mb-0">Unggah hasil observasi Anda di sini.</p>
		</div>
		<a href="<?= base_url('siswa/pbl/observasi/' . $class_id) ?>" class="btn btn-secondary">
			<i class="bi bi-arrow-left"></i> Kembali
		</a>
	</div>

	<!-- [BARU] Menampilkan Nilai & Feedback jika ada -->
	<?php if (!empty($result)) : ?>
		<div class="card shadow-sm mb-4 border-start border-success border-4">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-3 text-center border-end">
						<h6 class="text-uppercase text-muted fw-bold">Nilai Anda</h6>
						<h1 class="display-4 fw-bold text-success mb-0"><?= $result->score; ?></h1>
					</div>
					<div class="col-md-9 ps-md-4">
						<h5 class="card-title text-success"><i class="bi bi-chat-quote-fill"></i> Umpan Balik Guru:</h5>
						<p class="card-text fst-italic bg-light p-3 rounded">
							<?= !empty($result->feedback) ? nl2br(htmlspecialchars($result->feedback)) : 'Belum ada umpan balik tertulis.'; ?>
						</p>
						<small class="text-muted">Dinilai pada: <?= date('d M Y, H:i', strtotime($result->created_at)); ?></small>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!-- Instruksi Tugas -->
	<div class="card shadow-sm mb-4 border-start border-primary border-4">
		<div class="card-body">
			<h5 class="card-title text-primary">Instruksi Tugas:</h5>
			<p class="card-text"><?= nl2br(htmlspecialchars($slot->instruction)); ?></p>
		</div>
	</div>

	<!-- Tabel Upload -->
	<div class="card shadow-sm">
		<div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
			<h6 class="m-0 font-weight-bold text-primary">File Saya</h6>
			
			<!-- Button Upload -->
			<!-- Jika sudah dinilai, Anda bisa opsional menonaktifkan tombol upload dengan menambahkan kondisi -->
			<!-- Contoh: <button ... <//?= !empty($result) ? 'disabled' : '' ?>> -->
			<button class="btn btn-primary btn-sm" id="btnAddUpload">
				<i class="bi bi-cloud-upload"></i> Upload File Baru
			</button>
		</div>
		<div class="card-body" id="uploadContainer">
			<div class="table-responsive">
				<table class="table table-bordered table-hover" id="myUploadsTable" width="100%" cellspacing="0">
					<thead class="table-light">
						<tr>
							<th style="width: 5%;">No</th>
							<th>Nama File Asli</th>
							<th>Keterangan</th>
							<th>Tanggal Upload</th>
							<th style="width: 15%;">Aksi</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="uploadForm" enctype="multipart/form-data">
				<div class="modal-header">
					<h5 class="modal-title" id="uploadModalLabel">Upload Hasil Observasi</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="observation_slot_id" value="<?= $slot->observation_id; ?>">
					<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

					<div class="mb-3">
						<label for="file_upload" class="form-label">Pilih File <span class="text-danger">*</span></label>
						<input class="form-control" type="file" id="file_upload" name="file_upload" required>
						<div class="form-text">Format: PDF, Word, Image. Max: 5MB.</div>
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Keterangan Tambahan (Opsional)</label>
						<textarea class="form-control" id="description" name="description" rows="3" placeholder="Contoh: Observasi lapangan hari ke-1"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-primary">Upload</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	window.BASE_URL = "<?= base_url(); ?>";
	window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
	window.SLOT_ID = "<?= $slot->observation_id; ?>";
</script>

<script type="module" src="<?= base_url('assets/js/siswa/pbl_observasi_detail.js'); ?>"></script>