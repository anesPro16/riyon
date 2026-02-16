<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Daftar Akun Murid | RiyonClass</title>

	<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

	<link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
	<link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">

	<style>
		:root {
			--primary-color: #4154f1;
			--primary-hover: #2f3fc9;
			--success-color: #198754;
			--warning-color: #ffc107;
			--danger-color: #dc3545;
		}

		body {
			font-family: 'Nunito', sans-serif;
			margin: 0;
			min-height: 100vh;
			background-color: #f6f9ff;
			/* Menggunakan gambar background yang sama atau bertema sekolah */
			background: linear-gradient(135deg, rgba(1, 41, 112, 0.85) 0%, rgba(65, 84, 241, 0.5) 100%),
			url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=2022&auto=format&fit=crop');
			background-size: cover;
			background-position: center;
			background-attachment: fixed;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.container {
			padding-top: 2rem;
			padding-bottom: 2rem;
		}

		/* CARD STYLE */
		.register-card {
			border: none;
			border-radius: 15px;
			box-shadow: 0 15px 35px rgba(0,0,0,0.2);
			background: #fff;
			overflow: hidden;
			border-top: 5px solid var(--primary-color);
		}

		/* SISI KIRI (INFO/BRANDING) */
		.brand-side {
			background-color: #fcfdfeb8;
			padding: 3rem 2rem;
			display: flex;
			flex-direction: column;
			justify-content: center;
		}

		.brand-side h2 {
			font-weight: 800;
			color: #012970;
			margin-bottom: 1rem;
		}

		.feature-list {
			list-style: none;
			padding: 0;
			margin-top: 1.5rem;
		}

		.feature-list li {
			margin-bottom: 1rem;
			display: flex;
			align-items: center;
			font-size: 1.05rem;
			color: #444;
		}

		.feature-list i {
			color: var(--primary-color);
			font-size: 1.5rem;
			margin-right: 15px;
			background: rgba(65, 84, 241, 0.1);
			padding: 8px;
			border-radius: 50%;
		}

		/* FORM INPUTS */
		.form-floating > .form-control {
			border-radius: 8px;
			border: 1px solid #ced4da;
			height: calc(3.5rem + 2px);
		}

		.form-floating > .form-control:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 0 0.25rem rgba(65, 84, 241, 0.15);
		}

		.form-floating > label {
			padding-left: 1rem;
		}

		/* PASSWORD STRENGTH */
		.password-strength-meter {
			height: 6px;
			background-color: #e9ecef;
			border-radius: 3px;
			margin-top: 8px;
			overflow: hidden;
			transition: all 0.3s;
		}

		.strength-bar {
			height: 100%;
			width: 0;
			transition: width 0.3s ease, background-color 0.3s ease;
		}

		/* TOMBOL */
		.btn-register {
			background-color: var(--primary-color);
			border: none;
			padding: 12px;
			font-weight: 700;
			border-radius: 8px;
			font-size: 1.1rem;
			transition: all 0.3s;
		}

		.btn-register:hover:not(:disabled) {
			background-color: var(--primary-hover);
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(65, 84, 241, 0.3);
		}

		.btn-register:disabled {
			background-color: #aab2f5;
			cursor: not-allowed;
		}

		/* RESPONSIVE */
		@media (max-width: 991px) {
			.brand-side {
				padding: 2rem;
				text-align: center;
				background-color: rgba(255,255,255,0.9);
			}
			.feature-list li {
				justify-content: center;
				text-align: left;
			}
		}
	</style>
</head>

<body>

	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xl-10 col-lg-11">

				<div class="card register-card">
					<div class="row g-0">

						<div class="col-lg-5 d-none d-lg-block bg-light brand-side">
							<h2>Halo, Murid Hebat!</h2>
							<p class="text-muted mb-4">Buat akunmu sekarang dan mulai petualangan belajar yang seru di RiyonClass.</p>

							<ul class="feature-list">
								<li>
									<i class="bi bi-play-circle-fill"></i>
									<div>Akses Materi File atau Video</div>
								</li>
								<li>
									<i class="bi bi-trophy-fill"></i>
									<div>Kerjakan Kuis & Raih Nilai</div>
								</li>
								<li>
									<i class="bi bi-journal-album"></i>
									<div>Lihat Hasil Belajar</div>
								</li>
							</ul>

							<div class="mt-5 text-center">
								<img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" style="height: 40px; opacity: 0.7;">
								<div class="small text-muted mt-2">SDN Pantai Hurip 02</div>
							</div>
						</div>

						<div class="col-lg-7 p-4 p-md-5">
							<div class="text-center mb-4 d-lg-none">
								<h3 class="fw-bold text-primary">RiyonClass</h3>
								<p class="text-muted">Buat Akun Murid Baru</p>
							</div>

							<div class="d-none d-lg-block mb-4">
								<h3 class="fw-bold" style="color: #012970;">Buat Akun Baru</h3>
								<p class="text-muted small">Lengkapi data diri kamu di bawah ini ya!</p>
							</div>

							<?php if ($this->session->flashdata('error')): ?>
								<div class="alert alert-danger d-flex align-items-center small p-2 mb-3">
									<i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
									<div><?= $this->session->flashdata('error'); ?></div>
								</div>
							<?php endif; ?>

							<?php if (isset($is_quota_full) && $is_quota_full): ?>
							    <div class="alert alert-warning border-0 shadow-sm">
							        <i class="bi bi-exclamation-circle-fill me-2"></i>
							        <strong>Pendaftaran Tutup!</strong> Kuota harian (<?= $limit  ?> murid) telah terpenuhi. Silakan kembali besok.
							    </div>
							<?php elseif(isset($remaining_quota)): ?>
							     <div class="alert alert-info border-0 shadow-sm py-2 small">
							        <i class="bi bi-info-circle me-1"></i>
							        Sisa kuota hari ini: <strong><?= $remaining_quota ?></strong> murid.
							    </div>
							<?php endif; ?>

							<!-- <form action="</?= site_url('auth/register_action'); ?>" method="POST"> -->
							<?= form_open('auth/register_action'); ?>

								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="name" name="name" placeholder="Nama Lengkap" value="<?= set_value('name'); ?>" required>
									<label for="name"><i class="bi bi-person-vcard me-1"></i> Nama Lengkap</label>
									<?= form_error('name', '<small class="text-danger ps-2">', '</small>'); ?>
								</div>

								<div class="form-floating mb-3">
									<input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= set_value('email'); ?>">
									<label for="email"><i class="bi bi-envelope me-1"></i> Email (Opsional)</label>
									<?= form_error('email', '<small class="text-danger ps-2">', '</small>'); ?>
								</div>

								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?= set_value('username'); ?>" required>
									<label for="username"><i class="bi bi-at me-1"></i> Username</label>
									<small class="text-muted ps-2" style="font-size: 0.75rem;">Gunakan nama panggilanmu agar mudah diingat.</small>
									<?= form_error('username', '<small class="text-danger ps-2">', '</small>'); ?>
								</div>

								<div class="row g-2">
									<div class="col-md-6">
										<div class="form-floating mb-2">
											<input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
											<label for="password"><i class="bi bi-lock me-1"></i> Password</label>
										</div>
										<div class="password-strength-meter">
											<div class="strength-bar" id="strengthBar"></div>
										</div>
										<small id="strengthText" class="text-muted" style="font-size: 0.75rem;">Kekuatan Password</small>
									</div>

									<div class="col-md-6">
										<div class="form-floating mb-3">
											<input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Ulangi Password" required>
											<label for="password_confirm"><i class="bi bi-shield-lock me-1"></i> Ulangi Password</label>
										</div>
									</div>
								</div>
								<?= form_error('password', '<div class="text-danger small mb-2">', '</div>'); ?>

								<div class="form-check mb-3 mt-1">
									<input class="form-check-input" type="checkbox" id="showPass">
									<label class="form-check-label small text-muted" for="showPass">
										Lihat Password
									</label>
								</div>

								<div class="form-check mb-4">
									<input class="form-check-input" type="checkbox" id="termsCheck" required>
									<label class="form-check-label small text-muted" for="termsCheck">
										Saya setuju dengan <a href="#" class="text-decoration-none fw-bold">Aturan Penggunaan</a>
									</label>
								</div>

								<!-- <button type="submit" id="registerBtn" class="btn btn-register w-100 text-white" disabled>
									<i class="bi bi-person-plus-fill me-2"></i> Buat Akun Sekarang
								</button> -->

								<button type="submit" id="registerBtn" class="btn btn-register w-100 text-white" 
								    <?= (isset($is_quota_full) && $is_quota_full) ? 'disabled' : 'disabled' ?> >
								    <i class="bi bi-person-plus-fill me-2"></i> Buat Akun Sekarang
								</button>

								<div class="text-center mt-4">
									<p class="small text-muted mb-0">Sudah punya akun?</p>
									<a href="<?= site_url('auth'); ?>" class="fw-bold text-decoration-none fs-6">
										Masuk disini
									</a>
								</div>

							<?= form_close(); ?>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

	<script>
		const passwordInput = document.getElementById('password');
		const confirmInput = document.getElementById('password_confirm');
		const showPassCheck = document.getElementById('showPass');
		const strengthBar = document.getElementById('strengthBar');
		const strengthText = document.getElementById('strengthText');
		const termsCheck = document.getElementById('termsCheck');
		const registerBtn = document.getElementById('registerBtn');

    // Toggle Show Password
    showPassCheck.addEventListener('change', function() {
    	const type = this.checked ? 'text' : 'password';
    	passwordInput.type = type;
    	confirmInput.type = type;
    });

    // Password Strength Logic
    passwordInput.addEventListener('input', function() {
    	const val = this.value;
    	let score = 0;

    	if (val.length >= 8) score++;
    	if (/[A-Z]/.test(val)) score++;
    	if (/[0-9]/.test(val)) score++;
    	if (/[^A-Za-z0-9]/.test(val)) score++;

    	let width = 0;
    	let color = '#e9ecef';
    	let text = 'Terlalu Pendek';

    	if(val.length > 0) {
    		if(score <= 1) { width = 30; color = '#dc3545'; text = 'Lemah'; }
    		else if(score === 2) { width = 60; color = '#ffc107'; text = 'Lumayan'; }
    		else if(score >= 3) { width = 100; color = '#198754'; text = 'Kuat!'; }
    	}

    	strengthBar.style.width = width + '%';
    	strengthBar.style.backgroundColor = color;
    	strengthText.innerText = val.length > 0 ? text : 'Kekuatan Password';
    	strengthText.style.color = color;
    });

    // Enable Button only if Term checked
    /*termsCheck.addEventListener('change', function() {
    	registerBtn.disabled = !this.checked;
    });*/
    // Update script JS di register.php
		const isQuotaFull = <?= (isset($is_quota_full) && $is_quota_full) ? 'true' : 'false' ?>;

		termsCheck.addEventListener('change', function() {
		    // Hanya enable jika dicentang DAN kuota TIDAK penuh
		    if (!isQuotaFull) {
		        registerBtn.disabled = !this.checked;
		    }
		});

  </script>

</body>
</html>