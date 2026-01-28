// assets/js/siswa_kelas.js
import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

    const csrfTokenEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
    
    // Ambil Class ID dari hidden input di halaman detail
    const classIdEl = document.getElementById('classId');
    const classId = classIdEl ? classIdEl.value : null;

    if (!classId) {
        console.error('Class ID tidak ditemukan.');
        return;
    }

    const siswaConfig = {
        baseUrl: window.BASE_URL,
        entityName: 'Siswa',

        // --- 1. Selektor DOM ---
        modalId: 'studentModal', // Modal untuk tambah siswa (pilih dari list)
        formId: 'studentForm',
        modalLabelId: 'studentModalLabel',
        hiddenIdField: null, // Tidak butuh hidden ID untuk insert relasi
        tableId: 'siswaTable',
        btnAddId: 'btnAddStudent', // Tombol "Tambah Siswa"
        tableParentSelector: '.card-body',

        // --- 2. Konfigurasi CSRF ---
        csrf: {
            tokenName: window.CSRF_TOKEN_NAME,
            tokenHash: csrfTokenEl ? csrfTokenEl.value : ''
        },

        // --- 3. Endpoint URL ---
        urls: {
            load: `guru/dashboard/getStudentListForClass/${classId}`,
            save: 'guru/dashboard/add_student_to_class',
            delete: 'guru/dashboard/remove_student_from_class'
        },
        deleteMethod: 'POST',
        
        // Kirim class_id saat delete agar aman
        extraDeleteData: {
            class_id: classId
        },

        // --- 4. Teks Spesifik ---
        modalTitles: {
            add: 'Tambahkan Siswa ke Kelas',
            edit: '' // Tidak ada edit siswa di sini
        },
        deleteNameField: 'name',

        // --- 5. Data Mapper ---
        dataMapper: (siswa, index) => {
            return [
                index + 1,
                siswa.name,
                siswa.username,
                siswa.email || '-',
                `
                <button class="btn btn-danger btn-sm btn-delete" 
                    data-id="${siswa.id}" 
                    data-name="${siswa.name}">
                    <i class="fas fa-user-minus"></i> Keluarkan
                </button>
                `
            ];
        },

        // --- 6. Form Populator ---
        // Tidak dipakai karena tidak ada Edit, tapi wajib ada di struktur class
        formPopulator: (form, data) => {},

        onAdd: (form) => {
            // Reset select option saat modal dibuka
            form.reset();
            // Pastikan class_id terisi
            const hiddenClassId = form.querySelector('input[name="class_id"]');
            if(hiddenClassId) hiddenClassId.value = classId;
        }
    };

    const siswaHandler = new CrudHandler(siswaConfig);
    siswaHandler.init();
});