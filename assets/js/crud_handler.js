/**
 * CrudHandler.js
 * * Class generik untuk menangani operasi CRUD (Create, Read, Update, Delete)
 * yang umum ditemukan pada halaman admin.
 * * Menggunakan:
 * - ES6+ Class & async/await
 * - Bootstrap 5 Modal
 * - simple-datatables
 * - SweetAlert2
 * - Fetch API
 * @version 1.2.0
 */
export default class CrudHandler {
    
    constructor(config) {
        this.config = config;
        this.dataTable = null;
        this.modalInstance = null;
        
        // Set mode readOnly
        this.isReadOnly = config.readOnly || false;

        // Validasi yang disesuaikan
        // Wajib ada untuk SEMUA mode (termasuk read-only)
        if (!config || !config.urls || !config.urls.load || !config.tableId) {
            console.error("Konfigurasi CrudHandler (urls.load / tableId) tidak lengkap!");
            return;
        }

        // Wajib ada HANYA jika TIDAK read-only
        /*if (!this.isReadOnly && (!config.modalId || !config.formId)) {
            console.error("Konfigurasi CrudHandler (modalId / formId) tidak lengkap untuk mode CRUD!");
            return; 
        }*/

        // Cache elemen DOM yang selalu ada
        this.tableEl = document.getElementById(config.tableId);
        this.tableParent = document.querySelector(config.tableParentSelector || '.card-body');

        // Hanya cache elemen CRUD jika TIDAK read-only
        // Initialization untuk Mode Write (Create/Edit/Delete)
        if (!this.isReadOnly) {
            // Optional: Form & Modal hanya dibutuhkan jika fitur Create/Edit aktif
            this.form = config.formId ? document.getElementById(config.formId) : null;
            this.modalEl = config.modalId ? document.getElementById(config.modalId) : null;
            this.btnAdd = config.btnAddId ? document.getElementById(config.btnAddId) : null;
            
            this.modalLabel = config.modalLabelId ? document.getElementById(config.modalLabelId) : null;
            this.hiddenIdField = config.hiddenIdField ? document.getElementById(config.hiddenIdField) : null;

            // Inisialisasi Modal hanya jika elemen modal ditemukan
            if (this.modalEl) {
                this.modalInstance = new bootstrap.Modal(this.modalEl);
            }
        }
    }

    /**
     * Inisialisasi utama:
     * Disesuaikan untuk mode readOnly
     */
    init() {
        // Jika readOnly, load data dan stop.
        if (this.isReadOnly) {
            this.loadData();
            return;
        }

        // --- Logika Write (Create/Update/Delete) ---

        // 1. Listener Tombol Tambah (Hanya jika tombol ada)
        if (this.btnAdd) {
            this.btnAdd.addEventListener('click', () => this.#showAddModal());
        }

        // 2. Listener Submit Form (Hanya jika form ada)
        if (this.form) {
            this.form.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.#saveData();
            });
        }

        // 3. Listener Edit & Delete (Delegation pada Table Parent)
        // Ini tetap berjalan meskipun Form/Modal tidak ada (untuk kasus Delete Only)
        if (this.tableParent) {
            this.tableParent.addEventListener('click', (e) => {
                // Handle Edit (Hanya jika form & modal tersedia)
                const btnEdit = e.target.closest('.btn-edit');
                if (btnEdit && this.form && this.modalInstance) {
                    this.#showEditModal(btnEdit);
                    return;
                }

                // Handle Delete
                const btnDelete = e.target.closest('.btn-delete');
                if (btnDelete) {
                    this.#handleDeleteClick(btnDelete);
                    return;
                }
            });
        }

        // 4. Muat data awal
        this.loadData();
    }

    // ... (Sisa file crud_handler.js Anda: loadData, #saveData, #handleDeleteClick, dll. TETAP SAMA) ...
    // ... (Tidak perlu mengubah fungsi-fungsi private lainnya) ...
    
    // ... (loadData) ...
    async loadData() {
        try {
            const list = await this.#fetchWrapper(this.config.baseUrl + this.config.urls.load);

            if (typeof this.config.onDataLoaded === 'function') {
                this.config.onDataLoaded(list); 
            }
            
            const data = list.map(this.config.dataMapper);

            this.#initDataTable();

            if (data.length > 0) {
                this.dataTable.insert({ data: data });
            }

        } catch (error) {
            console.error('Failed to load data:', error);
            Swal.fire('Error', `Gagal memuat ${this.config.entityName || 'data'}.`, 'error');
        }
    }
    
    // ... (#saveData) ...
    async #saveData() {
        // ... (fungsi sama)
        const formData = new FormData(this.form);
        const url = this.config.baseUrl + this.config.urls.save;
        const csrfInput = this.form.querySelector(`input[name="${this.config.csrf.tokenName}"]`);
        if (csrfInput) {
            formData.set(this.config.csrf.tokenName, this.config.csrf.tokenHash);
        }
        try {
            const result = await this.#fetchWrapper(url, { method: 'POST', body: formData });
            if (result.status === 'success') {
                this.modalInstance.hide();
                this.#showToast('success', result.message);
                await this.loadData();
            } else {
                Swal.fire('Gagal!', result.message, 'error');
            }
        } catch (error) {
            console.error('Failed to save data:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat menyimpan.', 'error');
        }
    }

    // ... (#handleDeleteClick) ...
    #handleDeleteClick(btn) {
        // ... (fungsi sama)
        const id = btn.dataset.id;
        const dataName = btn.dataset[this.config.deleteNameField] || '';
        const entity = this.config.entityName || 'Data';
        const text = `${entity} "${dataName}" akan dihapus permanen!`;
        Swal.fire({
            title: 'Anda Yakin?',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.#executeDelete(id);
            }
        });
    }

    // ... (#executeDelete) ...
    async #executeDelete(id) {
        // ... (fungsi sama)
        const urlConfig = this.config.urls.delete;
        const url = this.config.baseUrl + (typeof urlConfig === 'function' ? urlConfig(id) : urlConfig);
        const method = this.config.deleteMethod || 'DELETE';
        const fetchOptions = { method: method };
        if (method.toUpperCase() === 'POST') {
            const formData = new FormData();
            formData.append('id', id); 
            if (this.config.csrf) {
                formData.append(this.config.csrf.tokenName, this.config.csrf.tokenHash);
            }
            if (this.config.extraDeleteData) {
                const extraData = this.config.extraDeleteData;
                for (const key in extraData) {
                    if (Object.hasOwnProperty.call(extraData, key)) {
                        formData.append(key, extraData[key]);
                    }
                }
            }
            fetchOptions.body = formData;
        }
        try {
            const result = await this.#fetchWrapper(url, fetchOptions);
            if (result.status === 'success') {
                this.#showToast('success', result.message);
                await this.loadData();
            } else {
                Swal.fire('Gagal!', result.message, 'error');
            }
        } catch (error) {
            console.error('Failed to delete data:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat menghapus.', 'error');
        }
    }

    // ... (#showAddModal) ...
    #showAddModal() {
        this.form.reset();
        if (this.hiddenIdField) {
            this.hiddenIdField.value = '';
        }
        this.modalLabel.textContent = this.config.modalTitles.add;
        if (typeof this.config.onAdd === 'function') {
            this.config.onAdd(this.form);
        }
        this.modalInstance.show();
    }
    
    // ... (#showEditModal) ...
    #showEditModal(btn) {
        this.form.reset();
        this.config.formPopulator(this.form, btn.dataset);
        this.modalLabel.textContent = this.config.modalTitles.edit;
        this.modalInstance.show();
    }

    // ... (#fetchWrapper, #updateCsrfToken, #initDataTable, #showToast) ...
    // ... (Semua helper ini tetap sama persis seperti file asli Anda) ...
    async #fetchWrapper(url, options = {}) {
        try {
            const response = await fetch(url, options);
            if (!response.ok) {
                throw new Error(`Network response was not ok (${response.status})`);
            }
            const result = await response.json();
            if (result.csrf_hash) {
                this.#updateCsrfToken(result.csrf_hash);
            }
            return result;
        } catch (error) {
            console.error('Fetch Error:', error);
            throw error; 
        }
    }
    #updateCsrfToken(hash) {
        if (this.config.csrf && hash) {
            this.config.csrf.tokenHash = hash;
            const tokens = document.querySelectorAll(`input[name="${this.config.csrf.tokenName}"]`);
            tokens.forEach(token => token.value = hash);
        }
    }
    #initDataTable() {
        if (this.dataTable) {
            this.dataTable.destroy();
        }
        this.dataTable = new simpleDatatables.DataTable(`#${this.config.tableId}`, {
            searchable: true,
            fixedHeight: false,
            labels: {
                placeholder: "Cari...",
                perPage: "",
                noRows: "Tidak ada data ditemukan",
                noResults: "Tidak ada data ditemukan",
                info: "Menampilkan {start} sampai {end} dari {rows} data",
            }
        });
    }
    #showToast(icon, title) {
        Swal.fire({
            icon: icon,
            title: title,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
}