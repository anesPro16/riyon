<div class="container-fluid">

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Formulir Kelas Baru</h6>
                </div>
                <div class="card-body">
                    <!-- <form action="</?= base_url('guru/kelas/tambah/' . $sekolah->id); ?>" method="POST"> -->
                        <?= form_open('guru/kelas/tambah/' . $sekolah->id); ?>
                        
                        <?php if (validation_errors()): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= validation_errors(); ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= set_value('name'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Kode Kelas</label>
                            <input type="text" class="form-control" id="code" name="code" value="<?= set_value('code'); ?>" required>
                            <div class="form-text">Buat kode unik untuk kelas ini (misal: "FISIKA-X-1").</div>
                        </div>

                        <hr>
                        
                        <a href="<?= base_url('guru/dashboard/detail/' . $sekolah->id); ?>" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Kelas
                        </button>
                    <!-- </form> -->
                    <?= form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>