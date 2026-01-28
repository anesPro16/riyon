<div class="container-fluid py-4 bg-light">

	<div class="row mb-4">
		<div class="col-12">
			<div class="alert alert-primary border-0 shadow-sm d-flex justify-content-between align-items-center text-white" style="background-color: #0d47a1;" role="alert">
				<div>
					<span class="fw-bold">Selamat Datang, <?= strtoupper($user['name']); ?></span>
					<span class="d-none d-md-inline ms-2 small opacity-75">| Email terdaftar: <?= $user['email']; ?></span>
				</div>
				<!-- <a href="#" class="btn btn-warning btn-sm fw-bold text-dark" style="font-size: 0.75rem;">Update Profil</a> -->
			</div>
		</div>
	</div>

	<div class="row g-4">
		<div class="col-lg-3">
			<div class="card border-0 shadow-sm text-center py-4 bg-white h-100">
				<div class="card-body">
					<div class="mx-auto mb-3 position-relative" style="width: 100px; height: 100px;">

						<img src="<?= base_url('profile/photo'); ?>" class="rounded-circle img-thumbnail shadow-sm" style="width: 100px; height: 100px; object-fit: cover;" alt="User Image">
						<span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle"></span>
					</div>

					<h5 class="fw-bold text-dark mb-1"><?= strtoupper($user['name']); ?></h5>
					<div class="badge bg-primary bg-opacity-10 text-primary mb-2">SISWA</div>
					<p class="text-muted small mb-3"><?= $user['email']; ?></p>

					<hr class="my-3 opacity-25">

					<div class="d-grid gap-2 text-start">
						<a href="#" class="btn btn-light btn-sm text-start text-muted"><i class="bi bi-book me-2"></i>Panduan Penggunaan</a>
						<a href="#" class="btn btn-light btn-sm text-start text-muted"><i class="bi bi-laptop me-2"></i>Panduan Ujian</a>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-9">

			<div class="d-flex align-items-center mb-3">
				<!-- <h5 class="fw-bold m-0"><i class="bi bi-grid-fill text-danger me-2"></i>Jadwal & Kelas Kuliah</h5> -->
			</div>

			<!-- <div class="row g-4">
				<?php if (!empty($classes)): ?>
					<?php foreach ($classes as $class): ?>
						<div class="col-md-6 col-xl-6">
							<div class="card h-100 border-0 shadow-sm overflow-hidden class-card">

								<div class="card-header text-white text-center py-3" style="background-color: #c62828;">
									<h5 class="card-title fw-bold mb-1 text-uppercase" style="font-size: 1rem; letter-spacing: 0.5px;">
										<?= htmlspecialchars($class->class_name); ?>
									</h5>
									<span class="badge bg-white text-danger fw-bold rounded-pill px-3">
										<i class="bi bi-clock me-1"></i>Senin, 08:00 - 10:00
									</span>
								</div>

								<div class="card-body p-4">
									<div class="row mb-2 align-items-center">
										<div class="col-1 text-center text-secondary"><i class="bi bi-person-fill fs-5"></i></div>
										<div class="col-4 text-muted small fw-bold">Dosen</div>
										<div class="col-7 text-dark fw-semibold small">: <?= htmlspecialchars($class->teacher_name); ?></div>
									</div>

									<div class="row mb-2 align-items-center">
										<div class="col-1 text-center text-secondary"><i class="bi bi-upc-scan fs-5"></i></div>
										<div class="col-4 text-muted small fw-bold">Kode MTK</div>
										<div class="col-7 text-dark fw-semibold small">: <?= htmlspecialchars($class->code); ?></div>
									</div>

									<div class="row mb-2 align-items-center">
										<div class="col-1 text-center text-secondary"><i class="bi bi-journal-bookmark fs-5"></i></div>
										<div class="col-4 text-muted small fw-bold">SKS</div>
										<div class="col-7 text-dark fw-semibold small">: 3 SKS</div>
									</div>

									<div class="row mb-2 align-items-center">
										<div class="col-1 text-center text-secondary"><i class="bi bi-geo-alt-fill fs-5"></i></div>
										<div class="col-4 text-muted small fw-bold">Ruang</div>
										<div class="col-7 text-dark fw-semibold small">: R.Online</div>
									</div>
								</div>

								<div class="card-footer bg-white border-top-0 pb-4 text-center d-flex gap-2 justify-content-center">
									<a href="<?= base_url('siswa/kelas/detail/' . $class->id) ?>" class="btn text-white fw-bold px-4 shadow-sm" style="background-color: #198754; width: 100%;">
										Masuk Kelas
									</a>
									<button class="btn btn-outline-primary"><i class="bi bi-chat-dots"></i></button>
									<button class="btn btn-outline-secondary"><i class="bi bi-archive"></i></button>
								</div>

							</div>
						</div>
					<?php endforeach; ?>
					<?php else: ?>
						<div class="col-12">
							<div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
								<i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
								<div>
									<h5 class="fw-bold mb-0">Belum ada kelas</h5>
									<p class="mb-0 small">Anda belum terdaftar di mata kuliah manapun.</p>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div> -->

				<div class="card border-0 shadow-sm mt-4">
					<div class="card-body">
						<h6 class="fw-bold text-dark mb-3">📢 Pengumuman Akademik</h6>
						<div class="alert alert-light border border-start-4 border-info">
							<p class="mb-0 small text-muted">Silakan ke menu laporan.</p>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<style>
		/* Styling Tambahan agar mirip referensi */
		.class-card {
			transition: transform 0.2s;
		}
		.class-card:hover {
			transform: translateY(-3px);
		}
		.text-justify {
			text-align: justify;
		}
	</style>