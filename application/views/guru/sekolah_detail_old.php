<div class="container-fluid">

	<h1 class="h3 mb-4 text-gray-800">Daftar Kelas di <?= htmlspecialchars($sekolah->name, ENT_QUOTES, 'UTF-8'); ?></h1>

	<?php if (empty($kelas_list)): ?>

		<div class="alert alert-info" role="alert">
			Anda belum membuat kelas di sekolah ini.
		</div>
		<a href="<?= base_url('guru/kelas/tambah/' . $sekolah->id) ?>" class="btn btn-primary">
			<i class="fas fa-plus fa-sm text-white-50"></i> Buat Kelas Baru
		</a>

		<?php else: ?>

			<a href="<?= base_url('guru/kelas/tambah/' . $sekolah->id) ?>" class="btn btn-primary btn-icon-split mb-3">
				<span class="icon text-white-50">
					<i class="fas fa-plus"></i>
				</span>
				<span class="text">Buat Kelas Baru</span>
			</a>

			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary">Manajemen Kelas</h6>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
							<thead class="table-light">
								<tr>
									<th style="width: 5%;">No</th>
									<th>Nama Kelas</th>
									<th>Kode Kelas</th>
									<th style="width: 20%;">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($kelas_list as $key => $kelas): ?>
									<tr>
										<td><?= $key + 1; ?></td>
										<td><?= htmlspecialchars($kelas->name, ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?= htmlspecialchars($kelas->code, ENT_QUOTES, 'UTF-8'); ?></td>
										<td>
											<a href="<?= base_url('guru/kelas/manage/' . $kelas->id); ?>" class="btn btn-info btn-sm">
												<i class="fas fa-users"></i> Kelola Siswa
											</a>
											<a href="<?= base_url('guru/kelas/edit/' . $kelas->id); ?>" class="btn btn-warning btn-sm">
												<i class="fas fa-edit"></i> Edit
											</a>
											<a href="<?= base_url('guru/kelas/hapus/' . $kelas->id); ?>" 
												class="btn btn-danger btn-sm" 
												title="Hapus Kelas"
												onclick="return confirm('Anda yakin ingin menghapus kelas "<?= htmlspecialchars($kelas->name, ENT_QUOTES, 'UTF-8'); ?>" ini? Tindakan ini tidak dapat diurungkan.');">
												<i class="bi-trash"></i>
											</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

		<?php endif; ?>

	</div>