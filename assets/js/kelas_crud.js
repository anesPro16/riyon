// assets/js/kelas_crud.js
import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

    // Ambil token CSRF
    const csrfTokenEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
    
    // Konfigurasi spesifik untuk modul "Kelas" (Admin)
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

        // --- 3. Endpoint URL (Ke Admin Dashboard) ---
        urls: {
            load: 'admin/dashboard/getClassList', // Load semua kelas
            save: 'admin/dashboard/class_save',
            delete: 'admin/dashboard/class_delete'
        },
        deleteMethod: 'POST',

        // --- 4. Teks Spesifik ---
        modalTitles: {
            add: 'Tambah Kelas Baru',
            edit: 'Edit Data Kelas'
        },
        deleteNameField: 'name', 

        // --- 5. Logika Spesifik (Callback) ---

        /**
         * Mapper data JSON (dari tabel 'classes' + join 'users') ke array simple-datatable.
         */
        dataMapper: (cls, index) => {
            return [
                index + 1,
                cls.name,
                `<span class="badge bg-secondary">${cls.code}</span>`, // Kode kelas
                cls.teacher_name || '<em class="text-muted">Belum ada guru</em>', // Nama Guru
                `
                <a href="${window.BASE_URL}admin/dashboard/class_detail/${cls.id}" 
                      class="btn btn-info btn-sm btn-detail" 
                      title="Lihat Siswa">
                      <i class="bi bi-eye"></i> Detail
                    </a>
                <button class="btn btn-warning btn-sm btn-edit" 
                    data-id="${cls.id}" 
                    data-name="${cls.name}"
                    data-teacher-id="${cls.teacher_id || ''}"
                    data-code="${cls.code || ''}">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm btn-delete" 
                    data-id="${cls.id}" 
                    data-name="${cls.name}">
                    <i class="bi bi-trash"></i> Hapus
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
            
            // Set Teacher di Dropdown
            const teacherSelect = form.querySelector('#teacherId');
            if(teacherSelect) {
                teacherSelect.value = data.teacherId;
            }
        },

        /**
         * Hook saat modal "Tambah" dibuka.
         */
        onAdd: (form) => {
            // Reset form dan pastikan dropdown guru bersih
            form.reset();
            form.querySelector('#classId').value = '';
            
            const teacherSelect = form.querySelector('#teacherId');
            if(teacherSelect) teacherSelect.value = "";
        }
    };

    // Inisialisasi handler
    const classHandler = new CrudHandler(classConfig);
    classHandler.init();
});