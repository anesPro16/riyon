<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1"><?= $title; ?></h4>
            <p class="text-muted">Silakan isi refleksi akhir pembelajaran ini.</p>
        </div>
        <a href="<?= base_url('siswa/pbl/tahap5/' . $class_id) ?>" class="btn btn-secondary">Kembali</a>
    </div>

    <!-- Card Instruksi -->
    <div class="card shadow-sm mb-4 border-start border-info border-4">
        <div class="card-body">
            <h5 class="card-title text-info"><i class="bi bi-info-circle"></i> Instruksi</h5>
            <p class="card-text"><?= nl2br(htmlspecialchars($reflection->description)); ?></p>
            
            <?php if ($submission): ?>
                <div class="alert alert-success mt-3 mb-0">
                    <i class="bi bi-check-circle-fill"></i> Anda telah mengirimkan refleksi ini pada <strong><?= date('d M Y, H:i', strtotime($submission->updated_at)); ?></strong>. Anda dapat mengeditnya kembali di bawah ini.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Form Refleksi -->
    <form id="reflectionForm">
        <input type="hidden" name="reflection_id" value="<?= $reflection->id; ?>">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lembar Refleksi Diri</h6>
            </div>
            <div class="card-body">
                
                <?php if (empty($prompts)): ?>
                    <p class="text-center text-muted py-4">Belum ada pertanyaan refleksi yang ditambahkan oleh Guru.</p>
                <?php else: ?>
                    <?php foreach ($prompts as $index => $p): ?>
                        <?php 
                            // Ambil jawaban lama jika ada
                            $val = isset($existing_answers[$p->id]) ? $existing_answers[$p->id] : ''; 
                        ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">
                                <?= ($index + 1) . '. ' . nl2br(htmlspecialchars($p->prompt_text)); ?>
                            </label>
                            <textarea 
                                name="answer_<?= $p->id; ?>" 
                                class="form-control bg-light" 
                                rows="4" 
                                placeholder="Tulis jawaban Anda di sini..." 
                                required><?= htmlspecialchars($val); ?></textarea>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
            <div class="card-footer bg-white text-end py-3">
                <button type="submit" class="btn btn-primary btn-lg px-4" id="btnSubmitReflection" <?= empty($prompts) ? 'disabled' : '' ?>>
                    <i class="bi bi-send"></i> Simpan Refleksi
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Config JS -->
<script>
    window.BASE_URL = "<?= base_url(); ?>";
    window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
</script>

<!-- Load Script -->
<script type="module" src="<?= base_url('assets/js/siswa/pbl_refleksi_akhir_detail.js'); ?>"></script>