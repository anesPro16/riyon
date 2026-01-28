// Impor class CrudHandler
import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

    // Ambil nama dan hash token CSRF dari <input> yang di-render PHP
    const csrfTokenEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
    
    // Konfigurasi spesifik untuk modul "Menu"
    const menuConfig = {
        baseUrl: window.BASE_URL, // Asumsi BASE_URL di-set di HTML/PHP
        entityName: 'Menu',

        // --- 1. Selektor DOM ---
        modalId: 'menuModal',
        formId: 'menuForm',
        modalLabelId: 'menuModalLabel',
        hiddenIdField: 'menuId',       // Input <hidden> untuk ID
        tableId: 'menuTable',
        btnAddId: 'btnAddMenu',

        // --- 2. Konfigurasi CSRF ---
        csrf: {
            tokenName: window.CSRF_TOKEN_NAME,
            tokenHash: csrfTokenEl ? csrfTokenEl.value : ''
        },
        
        // --- 3. Endpoint URL ---
        urls: {
            load: 'menu/getMenuList',
            save: 'menu/saveMenu',
            delete: (id) => `menu/deleteMenu/${id}` // Fungsi untuk URL delete dinamis
        },
        deleteMethod: 'DELETE', // Metode HTTP untuk delete

        // --- 4. Teks Spesifik ---
        modalTitles: {
            add: 'Tambah Menu',
            edit: 'Edit Menu'
        },
        deleteNameField: 'menu', // Nama field di 'data-attribute' untuk konfirmasi (data-menu="...")

        // --- 5. Logika Spesifik (Callback) ---

        /**
         * Callback untuk memetakan data JSON ke format array simple-datatable.
         */
        dataMapper: (menu, index) => {
            return [
                index + 1,
                menu.menu,
                `
                <button class="btn btn-warning btn-sm btn-edit" 
                    data-id="${menu.id}" 
                    data-menu="${menu.menu}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm btn-delete" 
                    data-id="${menu.id}" 
                    data-menu="${menu.menu}">
                    <i class="fas fa-trash"></i> Hapus
                </button>
                `
            ];
        },

        /**
         * Callback untuk mengisi form saat tombol "Edit" diklik.
         * @param {HTMLElement} form - Elemen <form> modal.
         * @param {DOMStringMap} data - Objek 'dataset' dari tombol edit.
         */
        formPopulator: (form, data) => {
            form.querySelector('#menuId').value = data.id;
            form.querySelector('#menuName').value = data.menu;
        }
    };

    // Inisialisasi handler
    const menuHandler = new CrudHandler(menuConfig);
    menuHandler.init();
});