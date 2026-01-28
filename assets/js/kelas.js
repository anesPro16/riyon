// assets/js/kelas_crud.js
import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

    // Ambil token CSRF
    const csrfTokenEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
    
    // Ambil School ID dari hidden input di form
    const schoolIdEl = document.getElementById('schoolId');
    const schoolId = schoolIdEl ? schoolIdEl.value : null;

    if (!schoolId) {
        console.error('School ID tidak ditemukan. CRUD tidak dapat diinisialisasi.');
        return;
    }

    // Konfigurasi spesifik untuk modul "Kelas"
    const classConfig = {
        baseUrl: window.BASE_URL,
        entityName: 'Kelas',

        // --- 1. Selektor DOM ---
        modalId: 'classModal',
        formId: 'classForm',
        modalLabelId: 'classModalLabel',
        hiddenIdField: 'classId',
        tableId: 'kelasTable',
        btnAddId: 'btnAddClass',
        tableParentSelector: '.card-body',

        // --- 2. Konfigurasi CSRF ---
        csrf: {
            tokenName: window.CSRF_TOKEN_NAME,
            tokenHash: csrfTokenEl ? csrfTokenEl.value : ''
        },

        // --- 3. Endpoint URL ---
        urls: {
            load: `guru/dashboard/getClassList/${schoolId}`, // URL dinamis dengan schoolId
            save: 'guru/dashboard/class_save',
            delete: 'guru/dashboard/class_delete'
        },
        deleteMethod: 'POST',

        // --- 4. Teks Spesifik ---
        modalTitles: {
            add: 'Tambah Kelas Baru',
            edit: 'Edit Kelas'
        },
        deleteNameField: 'name', // data-name="..."

        // --- 5. Logika Spesifik (Callback) ---

        /**
         * Mapper data JSON (dari tabel 'classes') ke array simple-datatable.
         */
        dataMapper: (cls, index) => {
            return [
                index + 1,
                cls.name,
                cls.code,
                `
                <a href="${window.BASE_URL}guru/dashboard/class_detail/${cls.id}" 
		              class="btn btn-info btn-sm btn-detail" 
		              title="Lihat Siswa">
		              <i class="fas fa-users"></i> Detail
		            </a>
                <button class="btn btn-warning btn-sm btn-edit" 
                    data-id="${cls.id}" 
                    data-name="${cls.name}"
                    data-code="${cls.code || ''}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm btn-delete" 
                    data-id="${cls.id}" 
                    data-name="${cls.name}">
                    <i class="fas fa-trash"></i> Hapus
                </button>
                `
            ];
        },

        /**
         * Pengisi form saat tombol "Edit" diklik.
         */
        formPopulator: (form, data) => {
            form.querySelector('#classId').value = data.id;
            form.querySelector('#className').value = data.name;
            form.querySelector('#classCode').value = data.code;
            // schoolId sudah ada di form dan tidak perlu diubah
        },

        /**
         * Hook saat modal "Tambah" dibuka.
         * Kita perlu memastikan schoolId tidak terhapus oleh form.reset().
         */
        onAdd: (form) => {
            // Ambil schoolId sebelum reset
            const schoolIdValue = form.querySelector('#schoolId').value;
            form.reset(); // Reset form
            // Setel kembali schoolId karena reset() mungkin mengosongkannya
            form.querySelector('#schoolId').value = schoolIdValue; 
            form.querySelector('#classId').value = ''; // Pastikan ID kosong
        }
    };

    // Inisialisasi handler
    const classHandler = new CrudHandler(classConfig);
    classHandler.init();
});