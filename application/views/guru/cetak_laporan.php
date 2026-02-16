<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<title><?= $title ?></title>
	<style>
		body { font-family: sans-serif; font-size: 10px; } /* Font kecil agar muat */
		.header { text-align: center; margin-bottom: 20px; }
		.header h2, .header h4 { margin: 2px 0; }
		
		table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
		th, td { border: 1px solid #333; padding: 4px; text-align: center; vertical-align: middle; }
		
		/* Styling Header Tabel */
		.bg-grey { background-color: #f2f2f2; }
		.bg-blue-light { background-color: #e3f2fd; }
		.bg-orange-light { background-color: #fff3e0; }
		
		.text-left { text-align: left !important; }
		.fw-bold { font-weight: bold; }
		
		/* Page Break agar print rapi jika data banyak */
		tr { page-break-inside: avoid; }
	</style>
</head>
<body>

	<div class="header">
		<h2>Laporan Hasil Belajar Siswa (Rekapitulasi)</h2>
		<h4>Sistem Pembelajaran PBL - RiyonClass</h4>
		<small>Dicetak pada: <?= date('d F Y H:i') ?></small>
	</div>

	<table>
		<thead>
			<tr class="bg-grey">
				<th rowspan="2" width="20">No</th>
				<th rowspan="2" width="120">Nama Siswa</th>
				
				<?php $i = 0; foreach($exam_subjects as $subject): $i++; 
				$bgClass = ($i % 2 == 0) ? 'bg-orange-light' : 'bg-blue-light';
				?>
				<th colspan="5" class="<?= $bgClass ?>"><?= $subject ?></th>
				<?php endforeach; ?>
				
				<th rowspan="2" width="30">Total</th>
			</tr>

			<tr class="bg-grey">
				<?php $i = 0; foreach($exam_subjects as $subject): $i++; 
				$bgClass = ($i % 2 == 0) ? 'bg-orange-light' : 'bg-blue-light';
				?>
				<th class="<?= $bgClass ?>">UTS</th>
				<th class="<?= $bgClass ?>">UAS</th>
				<th class="<?= $bgClass ?>">Kuis</th>
				<th class="<?= $bgClass ?>">Obs</th>
				<th class="<?= $bgClass ?>">Esai</th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php if(empty($students)): ?>
			<tr><td colspan="100%">Belum ada data siswa.</td></tr>
			<?php else: ?>
			<?php $no=1; foreach($students as $s): ?>
			<tr>
				<td><?= $no++ ?></td>
				<td class="text-left"><?= $s['student_name'] ?></td>

				<?php $i = 0; foreach($exam_subjects as $subj): $i++; 
				$bgClass = ($i % 2 == 0) ? 'bg-orange-light' : 'bg-blue-light';
				
				// Helper function untuk menampilkan '-' jika 0 atau kosong
				$val = function($v) { return ($v && $v != 0) ? $v : '-'; };
				
				$vUTS   = $s['parsed_exam'][$subj]['UTS'] ?? 0;
				$vUAS   = $s['parsed_exam'][$subj]['UAS'] ?? 0;
				$vQuiz  = $s['parsed_quiz'][$subj] ?? 0;
				$vObs   = $s['parsed_obs'][$subj] ?? 0;
				$vEssay = $s['parsed_essay'][$subj] ?? 0;
				?>
				<td class="<?= $bgClass ?>"><?= $val($vUTS) ?></td>
				<td class="<?= $bgClass ?>"><?= $val($vUAS) ?></td>
				<td class="<?= $bgClass ?>"><?= $val($vQuiz) ?></td>
				<td class="<?= $bgClass ?>"><?= $val($vObs) ?></td>
				<td class="<?= $bgClass ?>"><?= $val($vEssay) ?></td>
				<?php endforeach; ?>

				<td class="fw-bold"><?= $s['grand_total'] ?></td>
			</tr>
			<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>

	<div style="margin-top: 30px; width: 100%;">
		<table style="border: none;">
			<tr style="border: none;">
				<td style="border: none; width: 70%;"></td>
				<td style="border: none; text-align: center;">
					Bekasi, <?= date('d F Y') ?> <br>
					Guru Kelas,
					<br><br><br><br>
					<strong>( ..................................... )</strong>
				</td>
			</tr>
		</table>
	</div>

</body>
</html>