import CrudHandler from '../crud_handler.js'; // Sesuaikan path relatif ke crud_handler.js

document.addEventListener('DOMContentLoaded', () => {

    const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
    const SLOT_ID = window.SLOT_ID;

    if (!SLOT_ID) return;

    const csrfConfig = {
        tokenName: window.CSRF_TOKEN_NAME,
        tokenHash: csrfEl ? csrfEl.value : ''
    };

    // Konfigurasi CRUD
    const config = {
        baseUrl: window.BASE_URL,
        entityName: 'File',
        modalId: 'uploadModal',
        formId: 'uploadForm',
        modalLabelId: 'uploadModalLabel',
        tableId: 'myUploadsTable',
        btnAddId: 'btnAddUpload',
        
        // Dummy ID untuk menghindari error init CrudHandler
        hiddenIdField: 'dummyId', 
        tableParentSelector: '#uploadContainer',
        
        csrf: csrfConfig,
        urls: {
            load: `siswa/pbl_observasi/get_my_uploads/${SLOT_ID}`,
            save: `siswa/pbl_observasi/upload_file`,
            delete: (id) => `siswa/pbl_observasi/delete_upload/${id}`
        },
        deleteMethod: 'POST',
        deleteNameField: 'name', 
        
        modalTitles: { add: 'Upload Hasil Observasi', edit: '' },

        // Data Mapper
        dataMapper: (item, i) => {
            const uploadDate = new Date(item.created_at).toLocaleString('id-ID', {
                dateStyle: 'medium',
                timeStyle: 'short'
            });

            const fileUrl = `${window.BASE_URL}uploads/observasi/${item.file_name}`;
            
            // Tombol Download
            const downloadBtn = `
                <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-info text-white me-1" title="Unduh">
                    <i class="bi bi-download"></i>
                </a>
            `;

            const deleteBtn = `
                <button class="btn btn-sm btn-danger btn-delete" 
                    data-id="${item.id}" 
                    data-name="${item.original_name}">
                    <i class="bi bi-trash"></i>
                </button>
            `;

            return [
                i + 1,
                item.original_name,
                item.description || '-',
                uploadDate,
                downloadBtn + deleteBtn
            ];
        },

        // Form Populator (Kosong)
        formPopulator: (form, data) => {},

        // Reset form saat tombol tambah diklik
        onAdd: (form) => {
            form.reset();
        },
        onDataLoaded: (data) => {
            const btnAdd = document.getElementById('btnAddUpload');
            if (btnAdd) {
              // Logic: Tombol tambah hilang jika ada file
              if (data && data.length > 0) {
                btnAdd.classList.add('d-none');
                  // Tampilkan pesan limit jika perlu
                  document.getElementById('uploadLimitMsg')?.classList.remove('d-none');
                } else {
                    btnAdd.classList.remove('d-none');
                    document.getElementById('uploadLimitMsg')?.classList.add('d-none');
                }
              }
            }
    };

    const handler = new CrudHandler(config);
    handler.init();
});