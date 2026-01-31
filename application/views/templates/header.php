<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="csrf-name" content="<?= $this->security->get_csrf_token_name(); ?>">
  <meta name="csrf-hash" content="<?= $this->security->get_csrf_hash(); ?>">
  
  <title><?= isset($title) ? $title : 'RiyonClass'; ?></title>

  <!-- Favicons -->
  <link href="<?= base_url('assets/img/favicon.png'); ?>" rel="icon">
  <link href="<?= base_url('assets/img/apple-touch-icon.png'); ?>" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Nunito:300,400,600,700|Poppins:300,400,500,600,700" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css'); ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/simple-datatables/style.css'); ?>" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?= base_url('assets/css/style.css'); ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/kelasqu.css'); ?>" rel="stylesheet">

  <!-- <script src="</?= base_url('assets/js/jquery-3.6.0.min.js') ?>"></script> -->
  <!-- <script src="</?= base_url('assets/js/csrf.js'); ?>"></script> -->
</head>

<body>
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="<?= base_url(); ?>" class="logo d-flex align-items-center">
        <!-- <img src="<?= base_url('assets/img/logo.png'); ?>" alt=""> -->
        <span class="d-none d-lg-block">RiyonClass</span>
      </a>
      <!-- <i class="bi bi-list toggle-sidebar-btn"></i> -->
      <i class="bi bi-layout-sidebar toggle-sidebar-btn"></i>
      <!-- <i class="d-none d-md-block  bi layout-sidebar-reverse toggle-sidebar-btn"></i> -->
    </div>

    <!-- <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div> -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <!-- <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a> -->
        </li><!-- End Search Icon-->


        <?php if (isset($user) ? $user : null): ?>
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?= base_url('profile/photo/') .  (isset($user['image']) ? $user['image'] : 'foto.jpg'); ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?= $user['username'] ?? 'User'; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?= $user['name'] ?? 'User'; ?></h6>
              <span><?= $this->session->userdata('role') ?? ''; ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?= base_url('profile')  ?>">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li> 

            <li>
              <!-- <a class="dropdown-item d-flex align-items-center" href="</?= base_url('auth\logout') ?>"> -->
              <a class="dropdown-item d-flex align-items-center" href="<?= base_url(($this->session->userdata('role') == 'Siswa') ? 'auth\logout' : 'staf\index\logout') ?>">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->
        <?php endif ?>

      </ul>
    </nav><!-- End Icons Navigation -->
    
  </header>
  <!-- End Header -->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1><?= $title ?? 'RiyonClass'; ?></h1>
    </div><!-- End Page Title -->