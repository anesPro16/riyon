<div class="container-fluid">
	  <!-- Header Halaman -->
	<div class="d-flex justify-content-between align-items-center mb-3">
		<a href="<?= base_url('siswa/pbl/esai/' . $class_id) ?>" class="btn btn-secondary">← Kembali</a>
		<h3 class="text-primary mb-0"><?= htmlspecialchars($essay->title, ENT_QUOTES, 'UTF-8'); ?></h3>
	</div>

	<input type="hidden" id="currentEssayId" value="<?= $essay->essay_id; ?>">
	<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

	<div class="row">
		<div class="col-lg-7 mb-4">
			<div class="card shadow-sm border-0 h-100">
				<div class="card-header bg-white py-3">
					<h5 class="card-title mb-0 text-primary"><i class="bi bi-question-circle"></i> Soal Esai</h5>
				</div>
				<div class="card-body" id="questionTableContainer">
					<div class="table-responsive">
						<table class="table table-hover align-middle" id="questionTable">
							<thead class="table-light">
								<tr>
									<th width="10%">No</th>
									<th>Soal</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-5 mb-4">
			<div class="card shadow-sm border-0 h-100">
				<div class="card-header bg-white py-3">
					<h5 class="card-title mb-0 text-success"><i class="bi bi-pencil-square"></i> Jawab Soal</h5>
				</div>
				<div class="card-body">
					<div class="mb-4">
						<h6>Status Pengerjaan:</h6>
						<?php if ($submission): ?>
							<div class="alert alert-success d-flex align-items-center" role="alert">
								<i class="bi bi-check-circle-fill me-2"></i>
								<div>Sudah dikerjakan pada <br><small><?= date('d-m-Y H:i', strtotime($submission->created_at)); ?></small></div>
							</div>
							<?php else: ?>
								<div class="alert alert-danger d-flex align-items-center" role="alert">
									<i class="bi bi-exclamation-circle me-2"></i>
									<div>Belum mengumpulkan jawaban.</div>
								</div>
							<?php endif; ?>
						</div>

						<?php if ($submission && $submission->grade !== null): ?>
							<div class="card bg-light mb-3">
								<div class="card-body text-center">
									<h6 class="text-muted">Nilai Anda</h6>
									<h1 class="display-4 fw-bold text-primary"><?= $submission->grade; ?></h1>
									<?php if ($submission->feedback): ?>
										<hr>
										<p class="mb-1 fw-bold text-start">Feedback Guru:</p>
										<p class="text-muted text-start mb-0">"<?= html_escape($submission->feedback); ?>"</p>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>

						<div class="d-grid gap-2">
							<?php 
							$is_graded = ($submission && $submission->grade !== null);
							$btn_text = $submission ? 'Edit Jawaban' : 'Jawab Soal';
							$btn_cls = $submission ? 'btn-outline-primary' : 'btn-primary';
							
                        // Siapkan data untuk JS
							$sub_id = $submission ? $submission->id : '';
							$sub_content = $submission ? html_escape($submission->submission_content) : '';
							?>

							<?php if (!$is_graded): ?>
								<button class="btn <?= $btn_cls; ?> btn-lg" id="btnOpenAnswerModal"
									data-id="<?= $sub_id; ?>"
									data-content="<?= $sub_content; ?>">
									<i class="bi bi-send"></i> <?= $btn_text; ?>
								</button>
								<?php else: ?>
									<!-- <button class="btn btn-secondary" disabled>
										<i class="bi bi-lock"></i> Jawaban Terkunci (Sudah Dinilai)
									</button> -->
									<button class="btn btn-info text-dark mt-2" data-bs-toggle="modal" data-bs-target="#readOnlyModal">
										<i class="bi bi-eye"></i> Periksa
									</button>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="answerModal" tabindex="-1" aria-labelledby="answerModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered">
				<div class="modal-content">
					<form id="answerForm">
						<div class="modal-header bg-primary text-white">
							<h5 class="modal-title" id="answerModalLabel">Jawab Esai</h5>
							<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<input type="hidden" name="essay_id" value="<?= $essay->essay_id; ?>">
							<input type="hidden" name="submission_id" id="submissionId">
							
							<div class="alert alert-info">
								<small><i class="bi bi-info-circle"></i> Silakan jawab semua pertanyaan soal pada kolom di bawah ini. Anda bisa menulis nomor soal untuk memisahkan jawaban.</small>
							</div>

							<div class="mb-3">
								<label for="submissionContent" class="form-label fw-bold">Jawaban Anda:</label>
								<textarea name="submission_content" id="submissionContent" class="form-control" rows="10" placeholder="Tulis jawaban Anda di sini..." required></textarea>
							</div>
						</div>
						<div class="modal-footer bg-light">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
							<button type="submit" class="btn btn-primary">
							 	Kirim Jawaban
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<?php if($submission): ?>
			<div class="modal fade" id="readOnlyModal" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Jawaban Saya</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
						</div>
						<div class="modal-body">
							<div class="p-3 bg-light border rounded">
								<?= nl2br(html_escape($submission->submission_content)); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>


		<!-- Script Loader -->
		<script>
			window.BASE_URL = "<?= base_url(); ?>";
			window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
  // Tidak perlu CURRENT_ESSAY_ID di sini karena sudah ada di form
  // Namun, kita bisa tambahkan untuk kemudahan di JS
  window.CURRENT_ESSAY_ID = "<?= $essay->essay_id; ?>";
</script>
<script type="module" src="<?= base_url('assets/js/pbl_esai_detail_siswa.js'); ?>"></script>