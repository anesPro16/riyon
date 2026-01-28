<style>
	/* Grid Container */
	#ttsGridContainer {
		display: grid;
		background-color: #333;
		padding: 2px;
		border: 2px solid #333;
		margin: 0 auto;
	}

	/* Sel TTS */
	.tts-cell {
		position: relative;
		background-color: #222;
		width: 30px;
		height: 30px;
		border: 1px solid #555;
	}

	/* Sel Aktif (Input) */
	.tts-cell.active-cell {
		background-color: #fff;
	}

	/*.tts-cell input.hint-char {
		color: #0d6efd;
		font-weight: 900;
		background-color: #f8f9fa;
	}*/

	/* Input di dalam sel */
	.tts-cell input {
		width: 100%;
		height: 100%;
		border: none;
		text-align: center;
		font-weight: bold;
		text-transform: uppercase;
		font-size: 14px;
		outline: none;
		background: transparent;
		padding: 0;
		cursor: text;
		position: absolute; /* Supaya input menimpa area, tapi di bawah label nomor */
		top: 0;
		left: 0;
		z-index: 1;
	}

	/* Fokus pada sel */
	.tts-cell input:focus {
		background-color: #e3f2fd;
	}

	/* --- [PERBAIKAN CSS LABEL NOMOR] --- */
	.num-label {
		position: absolute;
		top: 0px;
		left: 1px;
		font-size: 9px; /* Ukuran font kecil agar muat */
		line-height: 2;
		color: #333;
		pointer-events: none; /* Agar klik tembus ke input */
		z-index: 2; /* Di atas input */
		font-family: sans-serif;
	}

	/* Posisi khusus untuk menurun (Kanan Atas) */
	.num-label.down {
		left: auto;
		right: 1px;
	}
	/* ----------------------------------- */

	/* List Pertanyaan */
	.clue-box {
		height: 400px;
		overflow-y: auto;
		background: #f8f9fc;
	}
	.clue-item {
		cursor: pointer;
		padding: 5px;
		border-bottom: 1px solid #eee;
		font-size: 0.9rem;
	}
	.clue-item:hover, .clue-item.active-clue {
		background-color: #d1e7ff;
		color: #000;
		font-weight: bold;
	}
</style>

<div class="container py-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<div>
			<h4 class="mb-1"><?= $title; ?></h4>
			<p class="text-muted">Isi teka-teki silang berikut.</p>
		</div>
		<a href="<?= base_url('siswa/pbl/tahap2/' . $class_id) ?>" class="btn btn-secondary">Kembali</a>
	</div>

	<?php if ($result): ?>
		<div class="alert alert-success text-center shadow-sm">
			<h4 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Selesai!</h4>
			<p>Anda telah menyelesaikan TTS ini.</p>
			<hr>
			<h1 class="display-4 fw-bold"><?= $result->score; ?></h1>
			<p class="mb-0">Benar: <?= $result->total_correct; ?> dari <?= $result->total_questions; ?> Soal</p>
		</div>
		<?php else: ?>
			<div class="row">
				<div class="col-lg-7 mb-4 text-center">
					<div class="card shadow-sm">
						<div class="card-body overflow-auto">
							<div id="ttsGridContainer"></div>
						</div>
					</div>
				</div>

				<div class="col-lg-5">
					<div class="card shadow-sm h-100">
						<div class="card-header bg-primary text-white">
							<h6 class="m-0">Petunjuk</h6>
						</div>
						<div class="card-body p-0">
							<ul class="nav nav-tabs nav-fill" id="clueTabs" role="tablist">
								<li class="nav-item">
									<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#across" type="button">Mendatar</button>
								</li>
								<li class="nav-item">
									<button class="nav-link" data-bs-toggle="tab" data-bs-target="#down" type="button">Menurun</button>
								</li>
							</ul>
							<div class="tab-content clue-box p-2" id="clueContent">
								<div class="tab-pane fade show active" id="across">
									<div class="list-group list-group-flush" id="listAcross"></div>
								</div>
								<div class="tab-pane fade" id="down">
									<div class="list-group list-group-flush" id="listDown"></div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<button class="btn btn-success w-100" id="btnSubmitTTS">
								<i class="bi bi-send"></i> Kirim Jawaban
							</button>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<form id="ttsSubmissionForm">
		<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
		<input type="hidden" name="tts_id" value="<?= $tts->id; ?>">
	</form>

	<script>
		window.BASE_URL = "<?= base_url(); ?>";
		window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
		window.TTS_ID = "<?= $tts->id; ?>";
		window.GRID_SIZE = <?= (int)$tts->grid_size; ?>;
	</script>

	<?php if (!$result): ?>
		<script type="module" src="<?= base_url('assets/js/siswa/pbl_tts_detail.js'); ?>"></script>
		<?php endif; ?>