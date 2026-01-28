<style>
  /* CSS Khusus Cetak */
  @media print {
      /* Sembunyikan elemen yang tidak perlu saat diprint */
      .sidebar, .navbar, .btn, .card-header-actions, footer {
          display: none !important;
      }
      
      /* Ubah layout kolom agar full width */
      .col-md-3, .col-md-9 {
          width: 100% !important;
          flex: 0 0 100% !important;
          max-width: 100% !important;
      }

      /* Styling Kartu Info agar hemat tinta */
      .card {
          border: 1px solid #000 !important;
          box-shadow: none !important;
          break-inside: avoid; /* Jangan potong kartu di tengah halaman */
      }
      
      .sticky-top {
          position: static !important; /* Matikan sticky saat print */
      }

      body {
          background-color: white !important;
          font-size: 12pt;
      }
  }
</style>

<div class="container-fluid py-4">
	<div class="row">
		<div class="col-md-3 mb-3">
			<div class="card shadow-sm sticky-top" style="top: 80px;">
				<div class="card-header bg-primary text-white">Info Pengerjaan</div>
				<div class="card-body">
					<button onclick="window.print()" class="btn btn-primary w-100 mb-2">
                <i class="bi bi-printer"></i> Cetak Hasil
            </button>
            <a href="javascript:history.back()" class="btn btn-outline-secondary w-100">Kembali</a>
            
					<h5 class="card-title"><?= $detail->full_name ?></h5>
					<p class="card-text text-muted"><?= $detail->exam_name ?></p>
					<hr>
					<div class="d-flex justify-content-between mb-2">
						<span>Nilai:</span>
						<span class="fw-bold fs-4 <?= $detail->score >= 75 ? 'text-success':'text-danger' ?>">
							<?= round($detail->score, 1) ?>
						</span>
					</div>
					<div class="d-flex justify-content-between mb-2">
						<span>Mulai:</span>
						<small><?= $detail->start_time ?></small>
					</div>
					<div class="d-flex justify-content-between">
						<span>Selesai:</span>
						<small><?= $detail->finished_time ?></small>
					</div>
					<hr>
					<a href="javascript:history.back()" class="btn btn-outline-secondary w-100">Kembali</a>
				</div>
			</div>
		</div>

		<div class="col-md-9">
		<?php foreach($answers as $i => $q): 
			$student_ans = $q->student_answer;
			$key = $q->correct_answer;
			$is_correct = ($student_ans == $key);
			$card_border = $is_correct ? 'border-success' : 'border-danger';
			$bg_header = $is_correct ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10';
			?>
			<div class="card mb-3 <?= $card_border ?> shadow-sm">
				<div class="card-header <?= $bg_header ?> d-flex justify-content-between">
					<span class="fw-bold">No. <?= $i + 1 ?></span>
					<?php if($is_correct): ?>
						<span class="badge bg-success"><i class="bi bi-check"></i> Benar</span>
						<?php elseif($student_ans == null): ?>
							<span class="badge bg-secondary">Tidak Dijawab</span>
							<?php else: ?>
								<span class="badge bg-danger"><i class="bi bi-x"></i> Salah</span>
							<?php endif; ?>
						</div>
						<div class="card-body">
							<p class="mb-3"><?= nl2br($q->question) ?></p>

							<ul class="list-group">
								<?php foreach(['A','B','C','D'] as $opt): 
									$opt_val = strtoupper($opt);
									$opt_text = $q->{'option_'.strtolower($opt)};
									
									$class = '';
									$icon = '';
									
                          // Logika Pewarnaan
									if ($opt_val == $key) {
                              // Ini Kunci Jawaban
										$class = 'list-group-item-success fw-bold';
										$icon = '<i class="bi bi-check-circle-fill float-end"></i>';
									} else if ($opt_val == $student_ans && !$is_correct) {
                              // Ini Jawaban Siswa yg Salah
										$class = 'list-group-item-danger';
										$icon = '<i class="bi bi-x-circle-fill float-end"></i>';
									} else if ($opt_val == $student_ans && $is_correct) {
                              // Ini Jawaban Siswa yg Benar (Sebenarnya sudah tercover if pertama, tapi untuk safety)
										$class = 'list-group-item-success fw-bold'; 
									}
									?>
									<li class="list-group-item <?= $class ?>">
										<span class="fw-bold me-2"><?= $opt_val ?>.</span> <?= $opt_text ?>
										<?= $icon ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>