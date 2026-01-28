<div class="container-fluid">

    <div class="row">
        <div class="col-lg-12">

            <button class="btn btn-primary mb-3" id="btnAddStudent">
                <i class="fas fa-user-plus"></i> Tambah Siswa Baru
            </button>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="studentTable" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Email</th>
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

<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalLabel">Form Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="studentForm">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                
                <input type="hidden" id="studentId" name="id">

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="studentName" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="studentName" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="studentEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="studentEmail" name="email">
                    </div>

                    <hr>

                    <div class="mb-3" id="usernameGroup">
                        <label for="studentUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="studentUsername" name="username">
                        <div class="form-text">Hanya bisa diisi saat menambah siswa baru.</div>
                    </div>
                    
                    <div class="mb-3" id="passwordGroup">
                        <label for="studentPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="studentPassword" name="password">
                        <div class="form-text">Kosongkan untuk password default ('password').</div>
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
<script type="module" src="<?= base_url('assets/js/student.js') ?>"></script>