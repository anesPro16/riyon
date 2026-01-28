import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {
    const CURRENT_CLASS_ID = window.CURRENT_CLASS_ID;

    // Helper: Format Tanggal
    const formatTime = (dateString) => {
        if(!dateString) return '-';
        const options = { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    };

    const studentExamConfig = {
        baseUrl: window.BASE_URL,
        entityName: 'Ujian',
        tableId: 'studentExamTable',
        readOnly: true, // Tidak ada fitur tambah/hapus di tabel siswa
        csrf: { 
            tokenName: window.CSRF_TOKEN_NAME, 
            tokenHash: document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`)?.value || '' 
        },
        urls: {
            load: `exam/get_student_exams/${CURRENT_CLASS_ID}`,
        },

        // Custom Mapper: Mengatur Tampilan Baris
        dataMapper: (item, index) => {
            
            // 1. LOGIKA TOMBOL & STATUS
            let actionBtn = '';
            let statusBadge = '';
            let rowClass = ''; // Untuk mewarnai baris jika perlu

            // Cek Status Percobaan (attempt_status dari Left Join)
            // LOGIKA TAMPILAN BERDASARKAN RESPON BACKEND
            switch (item.status_label) {
                case 'finished':
                    statusBadge = `<span class="badge bg-success"><i class="bi bi-check-circle"></i> Selesai</span>`;
                    actionBtn = `<button class="btn btn-secondary btn-sm w-100" disabled>Sudah Dikerjakan</button>`;
                    if(item.score) actionBtn += `<div class="mt-1 fw-bold text-success small">Nilai: ${parseFloat(item.score)}</div>`;
                    break;

                case 'ongoing':
                    statusBadge = `<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Berjalan</span>`;
                    actionBtn = `<a href="${window.BASE_URL}exam/start_attempt" 
                                    onclick="event.preventDefault(); document.getElementById('form-start-${item.exam_id}').submit();" 
                                    class="btn btn-warning btn-sm w-100 fw-bold">
                                    <i class="bi bi-play-fill"></i> Lanjutkan
                                 </a>`;
                    break;

                case 'upcoming':
                    // INI YANG DIMINTA: Tampil di list tapi tombol disable/hidden
                    // Kita format tanggal mulai agar user tahu kapan mulainya
                    const startTimeFormatted = new Date(item.start_time).toLocaleString('id-ID', {
                        day: 'numeric', month: 'short', hour: '2-digit', minute:'2-digit'
                    });
                    
                    statusBadge = `<span class="badge bg-info text-dark">Akan Datang</span>`;
                    actionBtn = `<div class="d-grid gap-1">
                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                        <i class="bi bi-lock"></i> Belum Dimulai
                                    </button>
                                    <small class="text-muted text-center" style="font-size:0.75rem;">
                                        Buka: ${startTimeFormatted}
                                    </small>
                                 </div>`;
                    break;

                case 'expired':
                    statusBadge = `<span class="badge bg-danger">Terlewat</span>`;
                    actionBtn = `<button class="btn btn-light btn-sm w-100 border" disabled>Waktu Habis</button>`;
                    break;

                case 'available':
                default:
                    // Tombol Kerjakan hanya muncul jika backend bilang OK (Available)
                    statusBadge = `<span class="badge bg-primary">Tersedia</span>`;
                    actionBtn = `<a href="${window.BASE_URL}exam/confirmation/${item.exam_id}" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-pencil"></i> Kerjakan
                                 </a>`;
                    break;
            }

            // Hidden form untuk resume (hanya dirender jika ongoing)
            const hiddenForm = item.status_label === 'ongoing' ? `
            <form id="form-start-${item.exam_id}" action="${window.BASE_URL}exam/start_attempt" method="POST" style="display:none;">
                <input type="hidden" name="exam_id" value="${item.exam_id}">
                <input type="hidden" name="class_id" value="${CURRENT_CLASS_ID}">
                <input type="hidden" name="${window.CSRF_TOKEN_NAME}" value="${studentExamConfig.csrf.tokenHash}"> 
            </form>` : '';

            return [
                index + 1,
                `<div>
                    <div class="fw-bold text-dark">${item.exam_name}</div>
                    <small class="text-muted">${item.type}</small>
                </div>`,
                `<div style="font-size: 0.9em">
                    <div><i class="bi bi-calendar-event"></i> Selesai: ${formatTime(item.end_time)}</div>
                </div>`,
                statusBadge,
                `<div>${actionBtn} ${hiddenForm}</div>`
            ];
        }
    };

    new CrudHandler(studentExamConfig).init();
});