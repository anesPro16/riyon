// Impor class CrudHandler
import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

    // Ambil nama dan hash token CSRF dari <input> yang di-render PHP
    const csrfTokenEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
    
    // Konfigurasi spesifik untuk modul "School"
    const schoolConfig = {
        baseUrl: window.BASE_URL,
        entityName: 'Sekolah',

        // --- 1. Selektor DOM ---
        modalId: 'schoolModal',
        formId: 'schoolForm',
        modalLabelId: 'schoolModalLabel',
        hiddenIdField: 'schoolId',
        tableId: 'schoolTable',
        btnAddId: 'btnAddSchool',
        tableParentSelector: '.card-body', // Wrapper tabel untuk event delegation

        // --- 2. Konfigurasi CSRF ---
        csrf: {
            tokenName: window.CSRF_TOKEN_NAME,
            tokenHash: csrfTokenEl ? csrfTokenEl.value : ''
        },

        // --- 3. Endpoint URL (Sesuaikan dengan controller Anda) ---
        // Asumsi controller Anda di-route sebagai 'admin/dashboard/...'
        urls: {
            load: 'admin/dashboard/getSchoolList',
            save: 'admin/dashboard/school_save',
            delete: (id) => `admin/dashboard/school_delete/${id}`
        },
        
        // Controller 'school_delete' Anda menggunakan ID dari segment, 
        // jadi kita harus menggunakan POST (karena CSRF) ke URL dengan ID.
        deleteMethod: 'POST',

        // --- 4. Teks Spesifik ---
        modalTitles: {
            add: 'Tambah Sekolah Baru',
            edit: 'Edit Sekolah'
        },
        deleteNameField: 'name', // data-name="..."

        // --- 5. Logika Spesifik (Callback) ---

        /**
         * Mapper data JSON ke array untuk simple-datatable.
         */
        dataMapper: (school, index) => {
            return [
                index + 1,
                school.name,
                school.address, // Tambahkan 'address' jika ada di JSON
                `
                <button class="btn btn-warning btn-sm btn-edit" 
                    data-id="${school.id}" 
                    data-name="${school.name}"
                    data-address="${school.address || ''}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm btn-delete" 
                    data-id="${school.id}" 
                    data-name="${school.name}">
                    <i class="fas fa-trash"></i> Hapus
                </button>
                `
            ];
        },

        /**
         * Pengisi form saat tombol "Edit" diklik.
         */
        formPopulator: (form, data) => {
            form.querySelector('#schoolId').value = data.id;
            form.querySelector('#schoolName').value = data.name;
            form.querySelector('#schoolAddress').value = data.address;
        }
    };

    // Inisialisasi handler
    const schoolHandler = new CrudHandler(schoolConfig);
    schoolHandler.init();
});