<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-name" content="<?= $this->security->get_csrf_token_name(); ?>">
	<meta name="csrf-hash" content="<?= $this->security->get_csrf_hash(); ?>">
	<title>Login Staf | RiyonClass</title>

	<!-- Favicons -->
  <link href="<?= base_url('assets/img/favicon.png'); ?>" rel="icon">
  <link href="<?= base_url('assets/img/apple-touch-icon.png'); ?>" rel="apple-touch-icon">

	<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

	<link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
	<link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">

	<style>
		:root {
			--primary-color: #4154f1; /* Warna khas NiceAdmin */
			--primary-hover: #2f3fc9;
			--overlay-color: rgba(1, 41, 112, 0.7); /* Biru tua transparan */
		}

		body {
			font-family: 'Nunito', sans-serif;
			margin: 0;
			overflow-x: hidden;
			background-color: #f6f9ff;
		}

		/* LATAR BELAKANG */
		.login-page {
			min-height: 100vh;
			/* Pastikan path gambar benar */
			background: url('<?= base_url('assets/img/login-bg.jpg') ?>') center/cover no-repeat fixed; 
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
		}

		/* GRADIENT OVERLAY - Agar tulisan terbaca & terlihat elegan */
		.login-page::before {
			content: "";
			position: absolute;
			inset: 0;
			background: linear-gradient(135deg, rgba(1, 41, 112, 0.8) 0%, rgba(0, 0, 0, 0.6) 100%);
			z-index: 1;
		}

		.container {
			position: relative;
			z-index: 2;
		}

		/* BRANDING KIRI */
		.branding-text {
			color: #fff;
			padding-right: 2rem;
			animation: fadeInLeft 0.8s ease-out;
		}

		.branding-text h1 {
			font-size: 3.5rem;
			font-weight: 700;
			margin-bottom: 0.5rem;
			text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
		}

		.branding-text h3 {
			font-size: 1.5rem;
			font-weight: 300;
			margin-bottom: 1.5rem;
			opacity: 0.9;
		}

		.branding-divider {
			width: 80px;
			height: 4px;
			background: #fff;
			border-radius: 2px;
			margin-bottom: 1.5rem;
		}

		.branding-desc {
			font-size: 1.1rem;
			line-height: 1.6;
			opacity: 0.85;
			max-width: 90%;
		}

		/* KARTU LOGIN */
		.card-login {
			border: none;
			border-radius: 15px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
			background: #fff;
			overflow: hidden;
			animation: fadeInUp 0.8s ease-out;
			border-top: 5px solid var(--primary-color); /* Aksen warna di atas */
		}

		.card-body {
			padding: 2.5rem;
		}

		.logo-wrapper img {
			max-height: 60px;
			width: auto;
		}

		/* FORM INPUT (FLOATING LABELS) */
		.form-floating > .form-control {
			border-radius: 8px;
			border: 1px solid #ced4da;
		}

		.form-floating > .form-control:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 0 0.25rem rgba(65, 84, 241, 0.15);
		}

		.password-toggle {
			position: absolute;
			right: 15px;
			top: 50%;
			transform: translateY(-50%);
			cursor: pointer;
			z-index: 5;
			color: #6c757d;
		}

		/* TOMBOL */
		.btn-primary {
			background-color: var(--primary-color);
			border-color: var(--primary-color);
			padding: 12px;
			font-size: 1rem;
			border-radius: 8px;
			transition: all 0.3s;
		}

		.btn-primary:hover {
			background-color: var(--primary-hover);
			transform: translateY(-2px);
			box-shadow: 0 5px 15px rgba(65, 84, 241, 0.3);
		}

		/* ERROR MESSAGE */
		.alert-custom {
			font-size: 0.9rem;
			padding: 0.8rem;
			border-radius: 8px;
		}

		/* ANIMASI */
		@keyframes fadeInLeft {
			from { opacity: 0; transform: translateX(-30px); }
			to { opacity: 1; transform: translateX(0); }
		}
		@keyframes fadeInUp {
			from { opacity: 0; transform: translateY(30px); }
			to { opacity: 1; transform: translateY(0); }
		}

		/* RESPONSIVE */
		@media (max-width: 991px) {
			.branding-text {
				text-align: center;
				margin-bottom: 2rem;
				padding-right: 0;
			}
			.branding-divider { margin: 0 auto 1.5rem auto; }
			.branding-desc { margin: 0 auto; }
		}
	</style>
</head>

<body>

	<div class="login-page">
		<div class="container">
			<div class="row align-items-center justify-content-center">

				<div class="col-lg-6 col-md-10 branding-text d-none d-lg-block">
					<h1>RiyonClass</h1>
					<h3>SDN Pantai Hurip 02</h3>
					<div class="branding-divider"></div>
					<p class="branding-desc">
						Selamat datang di Dashboard Staf. <br>
						Kelola materi, pantau aktivitas siswa, dan dukung pembelajaran digital yang lebih interaktif dan efisien dalam satu platform terintegrasi.
					</p>
				</div>

				<div class="col-lg-5 col-md-8">
					<div class="text-center mb-4 d-lg-none text-white">
						<h2 class="fw-bold">RiyonClass</h2>
						<small>SDN Pantai Hurip 02</small>
					</div>

					<div class="card card-login">
						<div class="card-body">

							<div class="text-center mb-4 logo-wrapper">
								<img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo">
								<h5 class="fw-bold mt-3 text-dark">Login Staf</h5>
								<p class="text-muted small">Masuk untuk mengelola kelas Anda</p>
							</div>

							<?php if ($this->session->flashdata('error')): ?>
								<div class="alert alert-danger alert-custom d-flex align-items-center mb-3">
									<i class="bi bi-exclamation-circle-fill me-2"></i>
									<div><?= $this->session->flashdata('error'); ?></div>
								</div>
							<?php endif; ?>

							<?= form_open('staf'); ?>

							<div class="form-floating mb-3">
								<input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
								<label for="username"><i class="bi bi-person me-1"></i> Username / Email</label>
								<?= form_error('username', '<small class="text-danger ps-2">', '</small>'); ?>
							</div>

							<div class="mb-4 position-relative">
								<div class="form-floating">
									<input type="password" class="form-control" id="loginPassword" name="password" placeholder="Password" required>
									<label for="loginPassword"><i class="bi bi-lock me-1"></i> Password</label>
								</div>
								<span class="password-toggle" id="toggleLoginPassword">
									<i class="bi bi-eye-slash"></i>
								</span>
								<?= form_error('password', '<small class="text-danger ps-2">', '</small>'); ?>
							</div>

							<!-- <div class="d-flex justify-content-between align-items-center mb-3">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
									<label class="form-check-label small text-muted" for="rememberMe">Ingat Saya</label>
								</div>
							</div> -->

							<button type="submit" class="btn btn-primary w-100 fw-bold">
								Masuk ke Dashboard
							</button>

							<?= form_close(); ?>

							<div class="text-center mt-4">
								<small class="text-muted">
									&copy; <?= date('Y'); ?> <strong>SDN Pantai Hurip 02</strong>. <br>All Rights Reserved.
								</small>
							</div>

						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
	<script>
        // Script Show/Hide Password
        const toggleLoginPassword = document.getElementById('toggleLoginPassword');
        const loginPassword = document.getElementById('loginPassword');

        toggleLoginPassword.addEventListener('click', function() {
        	const type = loginPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        	loginPassword.setAttribute('type', type);

        	const icon = this.querySelector('i');
        	if(type === 'text') {
        		icon.classList.remove('bi-eye-slash');
        		icon.classList.add('bi-eye');
                icon.style.color = '#4154f1'; // Highlight warna saat terlihat
              } else {
              	icon.classList.remove('bi-eye');
              	icon.classList.add('bi-eye-slash');
                icon.style.color = '#6c757d'; // Kembali abu-abu
              }
            });
          </script>

        </body>
        </html>