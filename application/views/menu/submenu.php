<div class="row">
    <div class="col-lg-12">

        <?= $this->session->flashdata('message'); ?>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <button class="btn btn-primary" id="btnAddSubmenu">
                    <i class="fas fa-plus"></i> Tambah Submenu Baru
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="submenuTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Judul</th>
                                <th>Menu</th>
                                <th>Url</th>
                                <th>Ikon</th>
                                <th style="width: 10%;">Aktif</th>
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

<div class="modal fade" id="submenuModal" tabindex="-1" aria-labelledby="submenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submenuModalLabel">Tambah Submenu Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="submenuForm">
                <div class="modal-body">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    
                    <input type="hidden" name="id" id="submenuId">

                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Submenu</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="menu_id" class="form-label">Menu Induk</label>
                        <select name="menu_id" id="menu_id" class="form-select" required>
                            <option value="">Pilih Menu...</option>
                            <?php foreach ($menu as $m) : ?>
                                <option value="<?= $m['id']; ?>"><?= $m['menu']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="url" class="form-label">URL</label>
                        <input type="text" class="form-control" id="url" name="url" required>
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label">Ikon</label>
                        <input type="text" class="form-control" id="icon" name="icon" required>
                        <small class="form-text text-muted">Contoh: ri-add-line</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Aktif?
                            </label>
                        </div>
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

<script type="module" src="<?= base_url('assets/js/submenu.js') ?>"></script>