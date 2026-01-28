import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

    const CURRENT_CLASS_ID = window.CURRENT_CLASS_ID || null;
    const CSRF_TOKEN_NAME = window.CSRF_TOKEN_NAME || 'csrf_token_name';
    
    // Ambil token awal dari input hidden di halaman (jika ada)
    const csrfEl = document.querySelector(`input[name="${CSRF_TOKEN_NAME}"]`);
    const initialCsrfHash = csrfEl ? csrfEl.value : '';

    if (!CURRENT_CLASS_ID) {
        console.error('CLASS ID tidak ditemukan. Pastikan window.CURRENT_CLASS_ID diset di View.');
        return;
    }

    const csrfConfig = {
        tokenName: CSRF_TOKEN_NAME,
        tokenHash: initialCsrfHash
    };

    /**
     * Helper: Format Datetime MySQL (YYYY-MM-DD HH:mm:ss) 
     * ke format input HTML5 (YYYY-MM-DDTHH:mm)
     */
    const formatForInput = (dateTimeStr) => {
        if (!dateTimeStr) return '';
        // Ganti spasi dengan T dan ambil 16 karakter pertama (hapus detik jika ada)
        return dateTimeStr.replace(' ', 'T').substring(0, 16);
    };

    /**
     * Helper: Format Datetime untuk tampilan Tabel (Indonesian format)
     */
    const formatForDisplay = (dateTimeStr) => {
        if (!dateTimeStr) return '-';
        const date = new Date(dateTimeStr);
        return new Intl.DateTimeFormat('id-ID', {
            dateStyle: 'medium', timeStyle: 'short'
        }).format(date);
    };

    const setupTimeValidation = (form) => {
        const startInput = form.querySelector('#startTime');
        const endInput = form.querySelector('#endTime');

        // Dapatkan waktu sekarang dalam format ISO (YYYY-MM-DDTHH:mm)
        // Perlu penyesuaian timezone lokal
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        const minStr = now.toISOString().slice(0, 16);

        // Set minimal waktu input ke waktu sekarang
        startInput.min = minStr;
        endInput.min = minStr;

        // Listener: Jika Start berubah, End minimal harus sama dengan Start
        startInput.addEventListener('change', () => {
             endInput.min = startInput.value;
        });
    };

    // --- CRUD UJIAN ---
    const examConfig = {
        baseUrl: window.BASE_URL,
        entityName: 'Ujian',
        // ID elemen HTML sesuai crud_handler
        tableId: 'examTable',
        modalId: 'examModal',
        formId: 'examForm',
        btnAddId: 'btnAddExam',      // Perlu ditambahkan di HTML
        modalLabelId: 'examModalLabel', // Perlu ditambahkan di HTML
        hiddenIdField: 'exam_id',
        
        csrf: csrfConfig,

        // URL Endpoint (Sesuai Exam.php)
        urls: {
            load: `exam/get_exams/${CURRENT_CLASS_ID}`, 
            save: `exam/save`,
            delete: (id) => `exam/delete` // ID dikirim via POST body di crud_handler, url ini base-nya
        },

        deleteMethod: 'POST', // CodeIgniter biasanya POST untuk delete yg aman
        deleteNameField: 'exam_name', // Field untuk pesan konfirmasi "Hapus [Nama]?"
        modalTitles: { add: 'Tambah Ujian Baru', edit: 'Edit Data Ujian' },

        // 1. DATA MAPPER: Mengubah JSON dari server ke Baris Tabel
        dataMapper: (item, index) => {
            // URL Link
            const detailUrl = `${window.BASE_URL}exam/questions/${item.exam_id}`;
            const resultUrl = `${window.BASE_URL}exam/result/${item.exam_id}`; // URL ke Monitoring Hasil

            // --- A. LOGIKA STATUS ---
            // Cek is_active dari database (pastikan controller mengirim field ini)
            const isActive = parseInt(item.is_active) === 1; 
            const statusBadge = isActive 
                ? '<span class="badge bg-success">Aktif</span>' 
                : '<span class="badge bg-secondary">Non-Aktif</span>';

            // --- B. TOMBOL AKSI ---
            
            // 1. Tombol Soal (Detail)
            const btnDetail = `<a href="${detailUrl}" class="btn btn-sm btn-info text-white me-1" title="Kelola Soal">
                <i class="bi bi-list-task"></i>
            </a>`;

            // 2. Tombol Hasil (Monitoring) - BARU
            const btnResult = `<a href="${resultUrl}" class="btn btn-sm btn-primary me-1" title="Monitoring Nilai">
                <i class="bi bi-bar-chart-line"></i>
            </a>`;

            // 3. Tombol Edit
            const btnEdit = `<button type="button" class="btn btn-sm btn-warning btn-edit me-1" 
                data-id="${item.exam_id}"
                data-exam_name="${item.exam_name}"
                data-type="${item.type}"
                data-start_time="${item.start_time}"
                data-end_time="${item.end_time}"
                title="Edit Ujian">
                <i class="bi bi-pencil-square"></i>
            </button>`;

            // 4. Tombol Hapus
            const btnDelete = `<button type="button" class="btn btn-sm btn-danger btn-delete" 
                data-id="${item.exam_id}" 
                data-exam_name="${item.exam_name}"
                title="Hapus Ujian">
                <i class="bi bi-trash"></i>
            </button>`;

            // --- C. RETURN ARRAY (Urutan harus sama dengan <th> di HTML) ---
            return [
                index + 1,                                              // Kolom 1: #
                `<span class="fw-bold">${item.exam_name}</span>`,       // Kolom 2: Mata Pelajaran
                `<span class="badge bg-${item.type === 'UTS' ? 'primary' : 'success'}">${item.type}</span>`, // Kolom 3: Jenis
                `<small>Mulai: ${formatForDisplay(item.start_time)}<br>Selesai: ${formatForDisplay(item.end_time)}</small>`, // Kolom 4: Waktu
                statusBadge,                                            // Kolom 5: Status (BARU)
                `<div class="d-flex">${btnResult} ${btnDetail} ${btnEdit} ${btnDelete}</div>` // Kolom 6: Aksi (Ditambah btnResult)
            ];
        },

        // 2. FORM POPULATOR: Mengisi form saat tombol Edit ditekan
        formPopulator: (form, data) => {
            form.querySelector('#exam_id').value = data.id; // data-id dari tombol edit
            form.querySelector('select[name="exam_name"]').value = data.exam_name;
            form.querySelector('select[name="type"]').value = data.type;
            
            // Konversi format MySQL ke datetime-local input
            form.querySelector('input[name="start_time"]').value = formatForInput(data.start_time);
            form.querySelector('input[name="end_time"]').value = formatForInput(data.end_time);
        },

        // Reset class_id saat Add Mode
        onAdd: (form) => {
            form.reset();
            form.querySelector('input[name="class_id"]').value = CURRENT_CLASS_ID;
            setupTimeValidation(form);
        }
    };

    // Inisialisasi Handler
    new CrudHandler(examConfig).init();
});