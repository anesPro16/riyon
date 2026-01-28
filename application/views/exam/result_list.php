<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold text-primary">Rekapitulasi Nilai</h4>
        <div>
            <a href="<?= base_url('exam/export_result/'.$exam->exam_id) ?>" target="_blank" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-primary text-white mb-3">
            <div class="card-body p-3">
                <small>Rata-Rata Kelas</small>
                <h3 class="fw-bold mb-0"><?= $stats['avg'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white mb-3">
            <div class="card-body p-3">
                <small>Nilai Tertinggi</small>
                <h3 class="fw-bold mb-0"><?= $stats['max'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white mb-3">
            <div class="card-body p-3">
                <small>Nilai Terendah</small>
                <h3 class="fw-bold mb-0"><?= $stats['min'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white mb-3">
            <div class="card-body p-3">
                <small>Partisipasi</small>
                <h3 class="fw-bold mb-0"><?= $stats['finished_count'] ?> / <?= $stats['total_students'] ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
	<div class="card-header d-flex justify-content-between align-items-center bg-white">
		<div>
			<h5 class="mb-0 text-primary"><?= $title ?></h5>
			<small class="text-muted">Kelas: <?= $exam->exam_name ?></small>
		</div>
		<a href="<?= base_url('exam/management/'.$exam->class_id) ?>" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-hover align-middle" id="resultTable">
				<thead class="table-light">
					<tr>
						<th width="5%">No</th>
						<th>Nama Siswa</th>
						<th>Status</th>
						<th>Waktu Mulai</th>
						<th>Waktu Selesai</th>
						<th class="text-center">Nilai</th>
						<th class="text-end">Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php if(empty($students)): ?>
						<tr><td colspan="7" class="text-center">Belum ada data siswa di kelas ini.</td></tr>
						<?php else: foreach($students as $i => $s): ?>
							<tr>
								<td><?= $i + 1 ?></td>
								<td class="fw-bold"><?= $s->full_name ?></td>
								<td>
									<?php if($s->attempt_status == 'finished'): ?>
										<span class="badge bg-success">Selesai</span>
										<?php elseif($s->attempt_status == 'ongoing'): ?>
											<span class="badge bg-warning text-dark">Sedang Mengerjakan</span>
											<?php else: ?>
												<span class="badge bg-secondary">Belum Mulai</span>
											<?php endif; ?>
										</td>
										<td><?= $s->start_time ? date('H:i d/m', strtotime($s->start_time)) : '-' ?></td>
										<td><?= $s->finished_time ? date('H:i d/m', strtotime($s->finished_time)) : '-' ?></td>
										<td class="text-center fw-bold fs-5 text-primary">
											<?= $s->score !== null ? round($s->score, 1) : '-' ?>
										</td>
										<td class="text-end">
											<?php if($s->attempt_id): ?>
												<a href="<?= base_url('exam/review_student/'.$s->attempt_id) ?>" class="btn btn-sm btn-info text-white" title="Lihat Jawaban">
													<i class="bi bi-eye"></i>
												</a>
												<button class="btn btn-sm btn-danger btn-reset" data-id="<?= $s->attempt_id ?>" data-name="<?= $s->full_name ?>" title="Reset Ujian">
													<i class="bi bi-arrow-counterclockwise"></i>
												</button>
												<?php else: ?>
													<button class="btn btn-sm btn-light border" disabled>No Action</button>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<script>
					document.addEventListener('DOMContentLoaded', () => {
    // Fitur Reset via AJAX
    document.querySelectorAll('.btn-reset').forEach(btn => {
    	btn.addEventListener('click', function() {
    		const id = this.dataset.id;
    		const name = this.dataset.name;

    		Swal.fire({
    			title: 'Reset Ujian?',
    			text: `Data pengerjaan siswa "${name}" akan dihapus permanen! Siswa harus mengerjakan ulang dari awal.`,
    			icon: 'warning',
    			showCancelButton: true,
    			confirmButtonColor: '#d33',
    			confirmButtonText: 'Ya, Reset!'
    		}).then((result) => {
    			if (result.isConfirmed) {
    				const formData = new FormData();
    				formData.append('attempt_id', id);
                    formData.append('<?= $this->security->get_csrf_token_name() ?>', '<?= $this->security->get_csrf_hash() ?>'); // Hash statis (ideally ambil dari meta/input)

                    fetch('<?= base_url("exam/reset_student_exam") ?>', {
                    	method: 'POST',
                    	body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                    	if(data.status === 'success') {
                    		Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
                    	} else {
                    		Swal.fire('Gagal', data.message, 'error');
                    	}
                    });
                  }
                });
    	});
    });
  });
</script>