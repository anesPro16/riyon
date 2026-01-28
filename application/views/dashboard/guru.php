<style>
  /* Styling utama untuk kartu versi simpel */

  /* Styling untuk kartu fitur */
  .feature-card {
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
  }

  .feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    border-color: #0d6efd;
  }

  /* Styling untuk ikon di dalam kartu */
  .feature-icon {
    font-size: 3.5rem;
    /* Ukuran ikon yang besar */
    margin-bottom: 1rem;
  }

  /* Memberi warna berbeda untuk setiap ikon */
  .icon-materi {
    color: #0d6efd;
    /* Biru */
  }

  .icon-tugas {
    color: #198754;
    /* Hijau */
  }

  .icon-diskusi {
    color: #ffc107;
    /* Kuning */
  }

  .feature-card .card-title {
    color: #343a40;
  }
</style>

<div class="container py-5">
  <div class="text-center mb-4">
    <h2>Selamat Datang, <?= $user['username']; ?> </h2>
    <p class="lead">Anda login sebagai <strong><?= ($user['role_id'] == 1) ? 'Guru' : ''; ?></strong></p>
  </div>

  <div class="row g-4">

    <div class="col-lg-4 col-md-6">
      <a href="<?= site_url('guru/materi/index'); ?>" class="text-decoration-none">
        <div class="card feature-card h-100 text-center shadow-sm">
          <div class="card-body">
            <i class="bi bi-journal-text feature-icon icon-materi"></i>
            <h5 class="card-title mt-3 fw-bold">Materi Pembelajaran</h5>
            <p class="card-text text-muted">Akses semua file, modul, presentasi, dan video perkuliahan.</p>
          </div>
        </div>
      </a>
    </div>

    <div class="col-lg-4 col-md-6">
      <a href="<?= site_url('guru/tugas'); ?>" class="text-decoration-none">
        <div class="card feature-card h-100 text-center shadow-sm">
          <div class="card-body">
            <i class="bi bi-clipboard2-check feature-icon icon-tugas"></i>
            <h5 class="card-title mt-3 fw-bold">Kumpulan Tugas</h5>
            <p class="card-text text-muted">Lihat daftar tugas, kumpulkan pekerjaan, dan periksa tenggat waktu.</p>
          </div>
        </div>
      </a>
    </div>

    <div class="col-lg-4 col-md-6">
      <a href="<?= site_url('guru/forum'); ?>" class="text-decoration-none">
        <div class="card feature-card h-100 text-center shadow-sm">
          <div class="card-body">
            <i class="bi bi-chat-left-text feature-icon icon-diskusi"></i>
            <h5 class="card-title mt-3 fw-bold">Forum Diskusi</h5>
            <p class="card-text text-muted">Bertanya, berdiskusi, dan berkolaborasi dengan dosen dan teman sekelas.</p>
          </div>
        </div>
      </a>
    </div>

  </div>
</div>