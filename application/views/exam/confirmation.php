<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h4 class="mb-0 fw-bold"><?= $exam->exam_name ?></h4>
                    <span class="badge bg-white text-primary mt-2"><?= $exam->type ?></span>
                </div>
                
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="<?= base_url('assets/img/news-4.jpg') ?>" alt="Exam" style="height: 150px; opacity: 0.8;" class="img-fluid mb-3">
                        <p class="text-muted">Silakan baca detail ujian di bawah ini sebelum memulai.</p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded text-center">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.75rem;">Mulai</small>
                                <span class="fs-5 text-dark"><?= date('d M Y, H:i', strtotime($exam->start_time)) ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded text-center">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.75rem;">Selesai</small>
                                <span class="fs-5 text-danger"><?= date('d M Y, H:i', strtotime($exam->end_time)) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle"></i> Peraturan Ujian:</h6>
                        <ul class="mb-0 small">
                            <li>Pastikan koneksi internet Anda stabil.</li>
                            <li>Dilarang membuka tab lain atau browser lain selama ujian berlangsung.</li>
                            <li>Waktu akan terus berjalan meskipun Anda keluar dari halaman ujian.</li>
                            <li>Jawaban akan tersimpan otomatis setiap Anda berpindah soal.</li>
                        </ul>
                    </div>
                </div>

                <div class="card-footer bg-white p-4 border-top-0 d-flex justify-content-between align-items-center">
                    <a href="<?= base_url('exam/student_list/'.$exam->class_id) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    
                    <form action="<?= base_url('exam/start_attempt') ?>" method="POST">
                        <input type="hidden" name="exam_id" value="<?= $exam->exam_id ?>">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            Mulai Kerjakan <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>