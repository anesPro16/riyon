<div class="row">
	<div class="col-lg-12">

		<?= form_error('menu', '<div class="alert alert-danger" role="alert">', '</div>'); ?>

		<?= $this->session->flashdata('message'); ?>

		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<button class="btn btn-primary" id="btnAddMenu">
					<i class="fas fa-plus"></i> Tambah Menu
				</button>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered" id="menuTable" width="100%" cellspacing="0">
						<thead>
							<tr>
								<th style="width: 10%;">No</th>
								<th>Menu</th>
								<th style="width: 20%;">Aksi</th>
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

<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="menuModalLabel">Tambah Menu</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="menuForm">
				<div class="modal-body">
					<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
					
					<input type="hidden" name="id" id="menuId">
					<div class="mb-3">
						<label for="menuName" class="form-label">Nama Menu</label>
						<input type="text" class="form-control" id="menuName" name="menu" required>
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

<script type="module" src="<?= base_url('assets/js/menu.js') ?>"></script>