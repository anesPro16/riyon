// Impor class CrudHandler
import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

    // Ambil nama dan hash token CSRF dari <input> yang di-render PHP
    const csrfTokenEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');

    // Konfigurasi spesifik untuk modul "Submenu"
    const submenuConfig = {
        baseUrl: window.BASE_URL, // Asumsi BASE_URL di-set di HTML/PHP
        entityName: 'Submenu',

        // --- 1. Selektor DOM ---
        modalId: 'submenuModal',
        formId: 'submenuForm',
        modalLabelId: 'submenuModalLabel',
        hiddenIdField: 'submenuId',
        tableId: 'submenuTable',
        btnAddId: 'btnAddSubmenu',

        // --- 2. Konfigurasi CSRF ---
        csrf: {
            tokenName: window.CSRF_TOKEN_NAME,
            tokenHash: csrfTokenEl ? csrfTokenEl.value : ''
        },
        
        // --- 3. Endpoint URL ---
        urls: {
            load: 'menu/getSubmenuList',
            save: 'menu/saveSubmenu',
            delete: (id) => `menu/deleteSubmenu/${id}`
        },
        deleteMethod: 'POST', // Sesuai kode asli Anda yang menggunakan POST

        // --- 4. Teks Spesifik ---
        modalTitles: {
            add: 'Tambah Submenu Baru',
            edit: 'Edit Submenu'
        },
        deleteNameField: 'title', // data-title="..."

        // --- 5. Logika Spesifik (Callback) ---

        /**
         * Callback untuk memetakan data JSON ke format array simple-datatable.
         */
        dataMapper: (sm, index) => {
            const badge = sm.is_active == 1
                ? `<span class="badge bg-success">Aktif</span>`
                : `<span class="badge bg-danger">Nonaktif</span>`;
            
            const buttons = `
                <button class="btn btn-warning btn-sm btn-edit" 
                    data-id="${sm.id}" 
                    data-title="${sm.title}" 
                    data-menu-id="${sm.menu_id}" 
                    data-url="${sm.url}" 
                    data-icon="${sm.icon}" 
                    data-is-active="${sm.is_active}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm btn-delete" 
                    data-id="${sm.id}" 
                    data-title="${sm.title}">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            `;

            return [
                index + 1,
                sm.title,
                sm.menu_name,
                sm.url,
                sm.icon,
                badge,
                buttons
            ];
        },

        /**
         * Callback untuk mengisi form saat tombol "Edit" diklik.
         */
        formPopulator: (form, data) => {
            form.querySelector('#submenuId').value = data.id;
            form.querySelector('#title').value = data.title;
            form.querySelector('#menu_id').value = data.menuId;
            form.querySelector('#url').value = data.url;
            form.querySelector('#icon').value = data.icon;
            form.querySelector('#is_active').checked = (data.isActive == 1);
        },

        /**
         * Hook opsional: Dipanggil saat modal "Tambah" dibuka.
         */
        onAdd: (form) => {
            form.querySelector('#is_active').checked = true; // Default aktif
        }
    };

    // Inisialisasi handler
    const submenuHandler = new CrudHandler(submenuConfig);
    submenuHandler.init();
});