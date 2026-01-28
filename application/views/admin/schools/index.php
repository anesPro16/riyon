<div class="container-fluid">

    <div class="row">
        <div class="col-lg-12">

            <button class="btn btn-primary mb-3" id="btnAddSchool">
                <i class="fas fa-plus"></i> Tambah Sekolah Baru
            </button>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Sekolah</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="schoolTable" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Sekolah</th>
                                    <th>Alamat</th>
                                    <th>Aksi</th>
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
</div>

<div class="modal fade" id="schoolModal" tabindex="-1" aria-labelledby="schoolModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="schoolModalLabel">Form Sekolah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="schoolForm">
                
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                
                <input type="hidden" id="schoolId" name="id">

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="schoolName" class="form-label">Nama Sekolah</label>
                        <input type="text" class="form-control" id="schoolName" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="schoolAddress" class="form-label">Alamat</label>
                        <textarea class="form-control" id="schoolAddress" name="address" rows="3"></textarea>
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
<script type="module" src="<?= base_url('assets/js/school.js') ?>"></script>