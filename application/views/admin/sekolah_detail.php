<div class="container-fluid">

    <div class="row">
        <div class="col-lg-12">

            <button class="btn btn-primary mb-3" id="btnAddClass">
                <i class="fas fa-plus"></i> Tambah Kelas Baru
            </button>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Kelas (Semua)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="kelasTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Nama Kelas</th>
                                    <th>Kode Kelas</th>
                                    <th>Guru Pengampu</th>
                                    <th width="29%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data dimuat via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="classModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="classModalLabel">Form Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="classForm">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                
                <input type="hidden" id="classId" name="id">

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="className" class="form-label">Nama Kelas</label>
                        <input type="text" class="form-control" id="className" name="name" required placeholder="Contoh: X IPA 1">
                    </div>

                    <div class="mb-3">
                        <label for="teacherId" class="form-label">Guru Pengampu</label>
                        <select class="form-select" id="teacherId" name="teacher_id" required>
                            <option value="">-- Pilih Guru --</option>
                            <?php foreach ($teachers as $t): ?>
                                <option value="<?= $t->id ?>"><?= htmlspecialchars($t->name, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Pilih guru yang bertanggung jawab untuk kelas ini.</div>
                    </div>
                    
                    <div class="alert alert-info py-2" style="font-size: 0.9rem;">
                        <i class="fas fa-info-circle mr-1"></i> Kode Kelas akan dibuat otomatis oleh sistem.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
        window.BASE_URL = '<?= base_url() ?>';
        window.CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name(); ?>';
</script>

<script type="module" src="<?= base_url('assets/js/kelas_crud.js') ?>"></script>