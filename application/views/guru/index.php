<style>
.stat-card {
    border: 0;
    border-radius: 14px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, .06);
    transition: all .25s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(0, 0, 0, .08);
}

/* ICON */
.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

/* WARNA SOFT */
.bg-primary-soft {
    background: rgba(13, 110, 253, .15);
}

.bg-danger-soft {
    background: rgba(220, 53, 69, .15);
}

.bg-success-soft {
    background: rgba(25, 135, 84, .15);
}

/* MOBILE */
@media (max-width: 576px) {
    .stat-icon {
        width: 46px;
        height: 46px;
        font-size: 1.25rem;
    }
}
</style>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <small class="text-muted">Ringkasan aktivitas pembelajaran PBL</small>
    </div>

    <div class="row g-3 mb-2">

        <!-- TOTAL Murid -->
        <div class="col-xl-4 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted">Total Murid Ajar</small>
                        <h4 class="fw-bold mb-0" id="card-students">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                        </h4>
                    </div>
                    <div class="stat-icon bg-primary-soft">
                        <i class="bi bi-people-fill text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- ESAI PERLU DINILAI -->
        <div class="col-xl-4 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted">Esai Perlu Dinilai</small>
                        <h4 class="fw-bold mb-0" id="card-pending">
                            <div class="spinner-border spinner-border-sm text-danger"></div>
                        </h4>
                    </div>
                    <div class="stat-icon bg-danger-soft">
                        <i class="bi bi-journal-text text-danger"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- RATA-RATA NILAI -->
        <div class="col-xl-4 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted">Rata-rata Nilai Kuis</small>
                        <h4 class="fw-bold mb-0" id="card-avg">
                            <div class="spinner-border spinner-border-sm text-success"></div>
                        </h4>
                    </div>
                    <div class="stat-icon bg-success-soft">
                        <i class="bi bi-graph-up-arrow text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRAFIK -->
    <div class="row g-4 mb-4">

        <!-- BAR CHART -->
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bar-chart-fill text-primary fs-5"></i>
                        <h6 class="mb-0 fw-bold text-primary">
                            Sebaran Nilai Kuis (Seluruh Kelas)
                        </h6>
                    </div>
                </div>

                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="quizBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- PIE / DOUGHNUT -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-pie-chart-fill text-primary fs-5"></i>
                        <h6 class="mb-0 fw-bold text-primary">
                            Status Pemeriksaan Esai
                        </h6>
                    </div>
                </div>

                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="chart-pie" style="height: 250px;">
                        <canvas id="essayPieChart"></canvas>
                    </div>

                    <!-- Legend Manual -->
                    <div class="d-flex justify-content-center gap-4 mt-3 small">
                        <div class="d-flex align-items-center gap-2">
                            <span class="legend-dot bg-success"></span>
                            <span>Sudah Dinilai</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="legend-dot bg-warning"></span>
                            <span>Menunggu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-warning text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Prioritas: 5 Tugas
                        Esai Terbaru (Belum Dinilai)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="priorityTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nama Murid</th>
                                    <th>Judul Tugas</th>
                                    <th>Tanggal Kirim</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center py-3">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<script src="<?= base_url('assets/js/jquery-3.6.0.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/chart.js/chart.umd.js'); ?>"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    // Konfigurasi Font Global Chart.js (Menyesuaikan template SB Admin 2 biasanya)
    Chart.defaults.font.family =
        'Nunito, -apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.color = '#858796';

    const baseUrl = "<?= base_url(); ?>";

    // Fungsi Fetch Data
    function loadDashboardStats() {
        $.ajax({
            url: baseUrl + 'guru/dashboard/dashboard_stats',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    updateCards(response.cards);
                    initBarChart(response.charts.quiz_dist);
                    initPieChart(response.charts.essay_stats);
                    updatePriorityTable(response.priority_list);
                } else {
                    console.log('No class data available');
                    $('#card-students').text('0');
                    $('#card-pending').text('0');
                    $('#card-avg').text('0');
                }
            },
            error: function(xhr, status, error) {
                console.error("Gagal mengambil data statistik:", error);
            }
        });
    }

    // 1. Update Info Cards
    function updateCards(cards) {
        $('#card-students').text(cards.students);
        $('#card-pending').text(cards.pending);
        $('#card-avg').text(cards.avg_quiz);
    }

    // 2. Chart Bar: Sebaran Nilai
    function initBarChart(dataValues) {
        const ctx = document.getElementById("quizBarChart").getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["0-50 (Remedial)", "51-70 (Cukup)", "71-85 (Baik)", "86-100 (Sempurna)"],
                datasets: [{
                    label: "Jumlah Murid",
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    data: dataValues, // [Range E, Range C, Range B, Range A]
                    borderRadius: 5,
                    barPercentage: 0.6
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    },
                    y: {
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2]
                        },
                        ticks: {
                            padding: 10,
                            precision: 0
                        } // precision 0 agar tidak ada desimal untuk jumlah orang
                    },
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleColor: '#6e707e',
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    }
                }
            }
        });
    }

    // 3. Chart Doughnut: Status Essay
    function initPieChart(dataValues) {
        const ctx = document.getElementById("essayPieChart").getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["Sudah Dinilai", "Menunggu Pemeriksaan"],
                datasets: [{
                    data: dataValues,
                    backgroundColor: ['#1cc88a', '#f6c23e'],
                    hoverBackgroundColor: ['#17a673', '#dda20a'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    cutout: '75%', // Membuat lubang tengah lebih besar
                }
            },
        });
    }

    // 4. Update Table Priority
    function updatePriorityTable(data) {
        const tbody = $('#priorityTable tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.append(
                '<tr><td colspan="4" class="text-center py-3 text-muted">Tidak ada tugas esai yang perlu diperiksa saat ini.</td></tr>'
            );
            return;
        }

        data.forEach(item => {
            // Link ke halaman penilaian essay (sesuaikan URL dengan routing Anda)
            // Asumsi: URLnya adalah guru/pbl/esai_detail/{class_id}
            const link = `${baseUrl}guru/Pbl_esai/detail/${item.essay_id}`;

            // Format Tanggal
            const date = new Date(item.created_at).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const row = `
            <tr>
            <td class="font-weight-bold text-gray-800">${item.student_name}</td>
            <td>${item.task_title}</td>
            <td><small>${date}</small></td>
            <td class="text-center">
            <a href="${link}" class="btn btn-warning btn-sm btn-icon-split">
            <span class="icon text-white-50"><i class="fas fa-pen"></i></span>
            <span class="text">Nilai</span>
            </a>
            </td>
            </tr>
            `;
            tbody.append(row);
        });
    }

    // Jalankan
    loadDashboardStats();
});
</script>



<!-- <h5 class="h5 text-gray-800 mb-3 ml-2 border-bottom pb-2">Daftar Kelas Anda</h5>
<div class="row">
	<?php if (!empty($kelas_list)): ?>
		<?php foreach ($kelas_list as $kelas): ?>
			<div class="col-lg-6 col-xl-4 mb-4">
				<div class="card shadow h-100 py-2 border-left-info">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-info text-uppercase mb-1">
									<?= htmlspecialchars($kelas->code, ENT_QUOTES, 'UTF-8'); ?>
								</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">
									<?= htmlspecialchars($kelas->name, ENT_QUOTES, 'UTF-8'); ?>
								</div>
							</div>
							<div class="col-auto">
								<a href="<?= base_url('guru/dashboard/class_detail/' . $kelas->id) ?>" class="btn btn-sm btn-info shadow-sm">
									Masuk Kelas <i class="fas fa-arrow-right ml-1"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		<?php else: ?>
			<div class="col-12 text-center text-muted">Belum ada kelas yang ditugaskan.</div>
		<?php endif; ?>
	</div> -->