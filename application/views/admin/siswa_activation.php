<div class="row">
    <div class="col-lg-12">

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Aktivasi Akun Siswa</h6>
                <button class="btn btn-sm btn-secondary" onclick="window.location.reload()"><i class="fas fa-sync"></i> Refresh</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="siswaTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama Siswa</th>
                                <th>Status</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="activationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activationModalLabel">Edit Status Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="activationForm">
                <div class="modal-body">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id" id="userId">

                    <div class="mb-3">
                        <label for="userName" class="form-label">Nama Siswa</label>
                        <input type="text" class="form-control" id="userName" readonly disabled>
                        <small class="text-muted">Nama tidak dapat diubah di menu ini.</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch p-3 border rounded bg-light">
                            <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="is_active" name="is_active" value="1" style="transform: scale(1.3);">
                            <label class="form-check-label fw-bold pt-1" for="is_active">
                                Akun Aktif?
                            </label>
                        </div>
                        <small class="text-muted">Jika dinonaktifkan, siswa tidak bisa login.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.BASE_URL = '<?= base_url() ?>';
    window.CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name(); ?>';
</script>

<script type="module" src="<?= base_url('assets/js/siswa_activation.js') ?>"></script>