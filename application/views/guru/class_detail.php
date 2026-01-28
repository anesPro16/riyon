<div class="container-fluid py-4">

	<div class="card border-0 shadow-sm mb-4">
		<div class="card-body p-4">
			<div class="row align-items-center">
				<div class="col-lg-8 mb-3 mb-lg-0">
					<h6 class="text-uppercase text-muted fw-bold small mb-1">Detail Kelas</h6>
					<h2 class="fw-bold text-dark mb-2">
						<?= htmlspecialchars($kelas->name, ENT_QUOTES, 'UTF-8'); ?>
					</h2>
					<div class="d-flex align-items-center text-secondary">
						<i class="bi bi-person-badge me-2 fs-5"></i>
						<span>Guru Pengampu: <strong class="text-dark"><?= htmlspecialchars($kelas->teacher_name, ENT_QUOTES, 'UTF-8'); ?></strong></span>
					</div>
				</div>

				<div class="col-lg-4 text-lg-end">
					<div class="d-inline-block text-start bg-light rounded-3 p-3 border">
						<small class="d-block text-muted mb-1">Kode Kelas</small>
						<div class="d-flex align-items-center">
							<span class="fs-4 fw-bold font-monospace text-primary me-3">
								<?= htmlspecialchars($kelas->code, ENT_QUOTES, 'UTF-8'); ?>
							</span>
							<span class="badge bg-white text-dark border shadow-sm">
								<i class="bi bi-people-fill me-1 text-primary"></i> 
								<span id="jumlah-siswa"><?= $kelas->student_count; ?></span> Siswa
							</span>
						</div>
					</div>
				</div>
			</div>

			<hr class="my-4 text-muted opacity-25">

			<div class="d-flex flex-wrap gap-2">
				<?php if(isset($role_controller) && $role_controller == 'admin'): ?>
					<a href="<?= base_url('admin/dashboard/classes') ?>" class="btn btn-outline-secondary">
						<i class="bi bi-arrow-left me-1"></i> Kembali ke Kelola Kelas
					</a>
					<?php else: ?>
						<a href="<?= base_url('guru/dashboard') ?>" class="btn btn-outline-secondary">
							<i class="bi bi-arrow-left me-1"></i> Dashboard
						</a>

						<a href="<?= base_url('guru/pbl/index/' . $kelas->id); ?>" class="btn btn-primary">
							<i class="bi bi-lightbulb-fill me-1"></i> Materi
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="card border-0 shadow-sm">
			<div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
				<div>
					<h5 class="m-0 fw-bold text-dark">Daftar Siswa</h5>
					<small class="text-muted">Data siswa yang terdaftar di kelas ini</small>
				</div>

				<?php if (isset($can_manage_students) && $can_manage_students === true): ?>
					<button class="btn btn-success btn-sm px-3" id="btnAddStudent" data-bs-toggle="modal" data-bs-target="#siswaModal">
						<i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa
					</button>
				<?php endif; ?>
			</div>

			<div class="card-body p-0" id="siswaTableContainer">
				<div class="table-responsive">
					<table class="table table-hover align-middle mb-0" id="siswaTable" style="width:100%">
						<thead class="bg-light">
							<tr>
								<th class="py-3 px-4 text-secondary text-uppercase small fw-bold border-bottom-0" style="width: 5%;">No</th>
								<th class="py-3 text-secondary text-uppercase small fw-bold border-bottom-0">Nama Lengkap</th>
								<th class="py-3 text-secondary text-uppercase small fw-bold border-bottom-0">Username</th>
								<th class="py-3 text-secondary text-uppercase small fw-bold border-bottom-0">Email</th>

								<?php if (isset($can_manage_students) && $can_manage_students === true): ?>
									<th class="py-3 px-4 text-secondary text-uppercase small fw-bold border-bottom-0" style="width: 15%;">Aksi</th>
								<?php endif; ?>
							</tr>
						</thead>
						<tbody class="border-top-0">
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>

	<?php if (isset($can_manage_students) && $can_manage_students === true): ?>
		<div class="modal fade" id="siswaModal" tabindex="-1" aria-labelledby="siswaModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content border-0 shadow">

					<form id="studentForm">
						<div class="modal-header bg-light border-bottom-0">
							<h5 class="modal-title fw-bold" id="siswaModalLabel">
								<i class="bi bi-person-plus text-primary me-2"></i>Tambah Siswa
							</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
						</div>

						<div class="modal-body p-4">
							<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
							<input type="hidden" name="class_id" id="classIdHidden" value="<?= $kelas->id; ?>">

							<div class="mb-3">
								<label for="studentSelect" class="form-label fw-semibold">Pilih Siswa</label>
								<select class="form-select form-select-lg" id="studentSelect" name="student_id" required>
									<option value="">-- Cari Nama Siswa --</option>
									<?php foreach($siswa_list as $s): ?>
										<option value="<?= $s->id; ?>">
											<?= htmlspecialchars($s->name, ENT_QUOTES, 'UTF-8'); ?> (<?= htmlspecialchars($s->username, ENT_QUOTES, 'UTF-8'); ?>)
										</option>
									<?php endforeach; ?>
								</select>
								<div class="form-text text-muted"><i class="bi bi-info-circle me-1"></i> Menampilkan semua siswa yang belum memiliki kelas.</div>
							</div>
						</div>

						<div class="modal-footer border-top-0 bg-light">
							<button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
							<button type="submit" class="btn btn-primary px-4" id="btnSaveStudent">
								<i class="bi bi-save me-1"></i> Simpan
							</button>
						</div>
					</form>

				</div>
			</div>
		</div>
	<?php endif; ?>

	<script>
		window.BASE_URL = '<?= base_url() ?>';
		window.CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name(); ?>';

    // Konfigurasi Dinamis dari Controller
    window.CURRENT_CLASS_ID = '<?= $kelas->id; ?>';
    // Apakah user saat ini boleh menambah/menghapus siswa?
    window.CAN_MANAGE_STUDENTS = <?= (isset($can_manage_students) && $can_manage_students === true) ? 'true' : 'false' ?>;
    // Controller mana yang dipakai? 'admin' atau 'guru'
    window.ROLE_CONTROLLER = '<?= isset($role_controller) ? $role_controller : 'guru' ?>';
  </script>

  <script type="module" src="<?= base_url('assets/js/class_detail.js') ?>"></script>