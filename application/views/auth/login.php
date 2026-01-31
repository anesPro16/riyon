<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-name" content="<?= $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-hash" content="<?= $this->security->get_csrf_hash(); ?>">
    <title>Login | RiyonClass</title>

    <!-- Favicons -->
    <link href="<?= base_url('assets/img/favicon.png'); ?>" rel="icon">
    <link href="<?= base_url('assets/img/apple-touch-icon.png'); ?>" rel="apple-touch-icon">

    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">

    <style>
    body {
        margin: 0;
        min-height: 100vh;
        font-family: 'Segoe UI', sans-serif;
    }

    /* BACKGROUND */
    .login-page {
        min-height: 100vh;
        /*background: url('</?= base_url('assets/img/login-bg.jpg') ?>') center/cover no-repeat;*/
        background: url('<?= base_url('assets/img/cover.jpg') ?>') center/cover no-repeat;
        position: relative;
        display: flex;
        align-items: center;
    }

    .login-page::before {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
    }

    .login-wrapper {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 1200px;
        margin: auto;
        padding: 40px;
    }

    /* BRANDING */
    .branding {
        color: #fff;
    }

    .branding h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .branding p {
        font-size: 1.2rem;
        opacity: 0.95;
    }

    /* LOGIN CARD */
    .login-card {
        max-width: 330px;
        /* lebih ramping */
        border-radius: 16px;
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.35);
    }

    .login-card .form-control {
        height: 40px;
        font-size: 14px;
    }

    @media (max-width: 991px) {
        .branding {
            display: none;
        }

        .login-wrapper {
            padding: 20px;
        }
    }

    /* MOBILE BRANDING */
    .mobile-branding h2 {
        letter-spacing: 1px;
    }

    /* MOBILE FOOTER */
    .mobile-footer {
        position: absolute;
        bottom: 15px;
        left: 0;
        right: 0;
        z-index: 3;
        color: rgba(255, 255, 255, 0.7);
    }

    /* MOBILE CARD CENTER */
    @media (max-width: 991px) {
        .login-page {
            justify-content: center;
            padding-top: 40px;
        }

        .login-card {
            margin: auto;
        }
    }
    </style>
</head>

<body>

    <div class="login-page">
        <div class="login-wrapper">
            <!-- BRANDING MOBILE -->
            <div class="mobile-branding text-center mb-4 d-lg-none">
                <h2 class="fw-bold text-white mb-1">RiyonClass</h2>
                <small class="text-white-50">SDN Pantai Hurip 02</small>
            </div>

            <div class="row align-items-center">

                <!-- LEFT BRANDING -->
                <div class="col-lg-7 branding">
                    <h1>RiyonClass</h1>
                    <p>SDN Pantai Hurip 02</p>
                    <p class="mt-4">
                        Platform pembelajaran digital untuk mendukung proses belajar
                        yang lebih interaktif, sederhana, dan menyenangkan.
                    </p>
                </div>

                <!-- RIGHT LOGIN -->
                <div class="col-lg-5 d-flex justify-content-end">
                    <div class="card login-card border-0 w-100">
                        <div class="card-body p-3 p-md-4">

                            <div class="text-center mb-4">
                                <img src="<?= base_url('assets/img/logo.png') ?>" width="64" class="mb-2" alt="
                                    RiyonClass">
                                <h6 class="fw-bold mb-1">Selamat Datang</h6>
                                <small class="text-muted">Silakan login untuk melanjutkan</small>
                            </div>

                            <?php if ($this->session->flashdata('success')): ?>
                                <div class="alert alert-success alert-custom d-flex align-items-center mb-3">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                                    <div><?= $this->session->flashdata('success'); ?></div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?= $this->session->flashdata('error'); ?>
                            </div>
                            <?php endif; ?>

                            <?= form_open('auth'); ?>

                            <div class="mb-2">
                                <label class="form-label">Username atau Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <?= form_error('username', '<small class="text-danger">', '</small>'); ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>

                                    <input type="password" name="password" id="loginPassword" class="form-control"
                                        required>

                                    <span class="input-group-text password-toggle" id="toggleLoginPassword"
                                        style="cursor:pointer;">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>

                                <?= form_error('password', '<small class="text-danger">', '</small>'); ?>
                            </div>


                            <button type="submit" class="btn btn-primary w-100 fw-bold mt-2">
                                Masuk
                            </button>

                            <?= form_close(); ?>

                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    Belum punya akun?
                                    <a href="<?= site_url('auth/register'); ?>" class="fw-bold text-decoration-none">
                                        Daftar sekarang
                                    </a>
                                </small>
                                <!-- COPYRIGHT -->
                                <div class="text-center mt-3">
                                    <small class="mt-2 text-center text-muted">
                                        © <?= date('Y'); ?> SDN Pantai Hurip 02
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
    const toggleLoginPassword = document.getElementById('toggleLoginPassword');
    const loginPassword = document.getElementById('loginPassword');

    toggleLoginPassword.addEventListener('click', function() {
        const type = loginPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        loginPassword.setAttribute('type', type);

        const icon = this.querySelector('i');
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });
    </script>

</body>

</html>