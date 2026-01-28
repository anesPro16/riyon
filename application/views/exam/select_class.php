<div class="container-fluid">
	<div class="row">
		<?php if (!empty($kelas_list)): ?>
			<?php foreach ($kelas_list as $kelas): ?>
			<div class="col-lg-6 col-xl-4 mb-4">

				<div class="card shadow h-100 py-2 border-left-success">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-success text-uppercase mb-1">
									Kode: <span class="badge bg-secondary text-white"><?= htmlspecialchars($kelas->code, ENT_QUOTES, 'UTF-8'); ?></span>
								</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">
									<?= htmlspecialchars($kelas->name, ENT_QUOTES, 'UTF-8'); ?>
								</div>
								<div class="mt-2 text-xs text-muted">
									<i class="fas fa-calendar-alt"></i> Dibuat: <?= date('d M Y', strtotime($kelas->created_at)) ?>
								</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-chalkboard fa-2x text-gray-300"></i>
							</div>
						</div>
					</div>

					<div class="card-footer bg-white d-flex justify-content-end align-items-center">
						<a href="<?= base_url('exam/management/' . $kelas->id) ?>" class="btn btn-sm btn-primary shadow-sm">
							<i class="fas fa-edit mr-1"></i> Kelola Ujian
						</a>
					</div>
				</div>

			</div>
		<?php endforeach; ?>
		<?php else: ?>
			<div class="col-12">
				<div class="text-center py-5">
					<img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 20rem; opacity: 0.6;" src="<?= base_url('assets/img/undraw_empty.svg') ?>" alt="...">
					<p class="lead text-gray-800 mb-4">Anda belum ditugaskan ke kelas manapun.</p>
					<div class="alert alert-info d-inline-block">
						Silakan hubungi Admin untuk penugasan kelas.
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>