// Impor class CrudHandler
import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

    // Ambil token CSRF
    const csrfTokenEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');

    // Cache elemen form yang akan di-disable/enable
    const usernameEl = document.getElementById('studentUsername');
    const passwordEl = document.getElementById('studentPassword');
    const usernameGroup = document.getElementById('usernameGroup');
    const passwordGroup = document.getElementById('passwordGroup');

    // Konfigurasi spesifik untuk modul "Siswa"
    const studentConfig = {
        baseUrl: window.BASE_URL,
        entityName: 'Siswa',

        // --- 1. Selektor DOM ---
        modalId: 'studentModal',
        formId: 'studentForm',
        modalLabelId: 'studentModalLabel',
        hiddenIdField: 'studentId', // merujuk ke <input id="studentId">
        tableId: 'studentTable',
        btnAddId: 'btnAddStudent',
        tableParentSelector: '.card-body',

        // --- 2. Konfigurasi CSRF ---
        csrf: {
            tokenName: window.CSRF_TOKEN_NAME,
            tokenHash: csrfTokenEl ? csrfTokenEl.value : ''
        },

        // --- 3. Endpoint URL ---
        urls: {
            load: 'admin/dashboard/getStudentList', 
            save: 'admin/dashboard/student_save',
            delete: 'admin/dashboard/student_delete' // string, karena ID dikirim via POST body
        },
        deleteMethod: 'POST', // Sesuai controller_delete()

        // --- 4. Teks Spesifik ---
        modalTitles: {
            add: 'Tambah Siswa Baru',
            edit: 'Edit Data Siswa'
        },
        deleteNameField: 'name', // data-name="..."

        // --- 5. Logika Spesifik (Callback) ---

        /**
         * Mapper data JSON (dari tabel 'users') ke array simple-datatable.
         */
        dataMapper: (user, index) => {
            return [
                index + 1,
                user.name,
                user.username,
                user.email || '-',
                `
                <button class="btn btn-warning btn-sm btn-edit" 
                    data-id="${user.id}" 
                    data-name="${user.name}"
                    data-username="${user.username}"
                    data-email="${user.email || ''}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm btn-delete" 
                    data-id="${user.id}" 
                    data-name="${user.name}">
                    <i class="fas fa-trash"></i> Hapus
                </button>
                `
            ];
        },

        /**
         * Pengisi form saat tombol "Edit" diklik.
         * Username dan Password disembunyikan/dinonaktifkan.
         */
        formPopulator: (form, data) => {
            // Isi data
            form.querySelector('#studentId').value = data.id;
            form.querySelector('#studentName').value = data.name;
            form.querySelector('#studentEmail').value = data.email;

            // Logika mode EDIT:
            usernameEl.value = ''; // Kosongkan
            usernameEl.disabled = true; 
            usernameEl.required = false; // Tidak wajib
            usernameGroup.style.display = 'none'; // Sembunyikan

            passwordEl.value = '';
            passwordEl.disabled = true;
            passwordEl.required = false;
            passwordGroup.style.display = 'none'; // Sembunyikan
        },

        /**
         * Hook opsional: Dipanggil saat modal "Tambah" dibuka.
         * Pastikan username & password terlihat dan bisa diisi.
         */
        onAdd: (form) => {
            // Logika mode TAMBAH:
            usernameEl.disabled = false;
            usernameEl.required = true; // Wajib
            usernameGroup.style.display = 'block'; // Tampilkan
            
            passwordEl.disabled = false;
            passwordEl.required = false; // Tidak wajib (controller punya default)
            passwordGroup.style.display = 'block'; // Tampilkan
        }
    };

    // Inisialisasi handler
    const studentHandler = new CrudHandler(studentConfig);
    studentHandler.init();
});