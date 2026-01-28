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
 * @version 1.0.0
 */
export default class CrudHandler {
    
    /**
     * @param {object} config - Objek konfigurasi untuk modul CRUD spesifik.
     */
    constructor(config) {
        this.config = config;
        this.dataTable = null;
        this.modalInstance = null;

        // Validasi konfigurasi dasar
        if (!config || !config.urls || !config.modalId || !config.formId || !config.tableId) {
            console.error("Konfigurasi CrudHandler tidak lengkap!");
            return;
        }

        // Cache elemen DOM
        this.modalEl = document.getElementById(config.modalId);
        this.form = document.getElementById(config.formId);
        this.modalLabel = document.getElementById(config.modalLabelId);
        this.hiddenIdField = document.getElementById(config.hiddenIdField);
        this.btnAdd = document.getElementById(config.btnAddId);
        this.tableEl = document.getElementById(config.tableId);
        this.tableParent = document.querySelector(config.tableParentSelector || '.card-body');

        // Inisialisasi instance Modal Bootstrap
        if (this.modalEl) {
            this.modalInstance = new bootstrap.Modal(this.modalEl);
        }
    }

    /**
     * Inisialisasi utama:
     * 1. Menyiapkan semua event listener.
     * 2. Memuat data awal untuk tabel.
     */
    init() {
        if (!this.form) {
            console.error(`Form dengan ID #${this.config.formId} tidak ditemukan.`);
            return;
        }
        
        // 1. Listener untuk tombol "Tambah"
        this.btnAdd?.addEventListener('click', () => this.#showAddModal());

        // 2. Listener untuk form "Submit" (Create & Update)
        this.form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.#saveData();
        });

        // 3. Listener untuk "Edit" & "Delete" (Event Delegation)
        this.tableParent?.addEventListener('click', (e) => {
            const btnEdit = e.target.closest('.btn-edit');
            if (btnEdit) {
                this.#showEditModal(btnEdit);
                return;
            }

            const btnDelete = e.target.closest('.btn-delete');
            if (btnDelete) {
                this.#handleDeleteClick(btnDelete);
                return;
            }
        });

        // 4. Muat data awal
        this.loadData();
    }

    // --- METODE PUBLIK ---

    /**
     * Memuat data dari server, menghancurkan tabel lama,
     * dan menginisialisasi tabel baru dengan data yang sudah dipetakan.
     */
    async loadData() {
        try {
            const list = await this.#fetchWrapper(this.config.baseUrl + this.config.urls.load);

            // Panggil callback onDataLoaded (untuk render grid) dengan data MENTAH (list)
            if (typeof this.config.onDataLoaded === 'function') {
                this.config.onDataLoaded(list); // 'list' adalah data mentah dari server
            }
            
            // Gunakan callback 'dataMapper' dari config untuk memformat data
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

    // --- METODE PRIVATE (Logika Internal) ---

    /**
     * Menangani submit form untuk create dan update.
     * (Sudah benar, tapi kita pastikan token CSRF diperbarui)
     */
    async #saveData() {
        const formData = new FormData(this.form);
        const url = this.config.baseUrl + this.config.urls.save;

        // Pastikan token CSRF di form adalah yang terbaru
        const csrfInput = this.form.querySelector(`input[name="${this.config.csrf.tokenName}"]`);
        if (csrfInput) {
            formData.set(this.config.csrf.tokenName, this.config.csrf.tokenHash);
        }

        try {
            const result = await this.#fetchWrapper(url, {
                method: 'POST',
                body: formData
            });

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

    /**
     * Menangani konfirmasi dan eksekusi penghapusan data.
     * @param {HTMLElement} btn - Tombol delete yang diklik.
     */
    #handleDeleteClick(btn) {
        const id = btn.dataset.id;
        // Ambil nama item dari 'data-attribute' yang dikonfigurasi
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

    /**
     * Mengeksekusi fetch request untuk delete.
     * (Disempurnakan untuk mengirim ID dan token CSRF via POST)
     * @param {string|number} id - ID dari item yang akan dihapus.
     */
    async #executeDelete(id) {
        // 'urls.delete' bisa jadi fungsi (untuk ID di URL) atau string (untuk ID di body)
        const urlConfig = this.config.urls.delete;
        const url = this.config.baseUrl + (typeof urlConfig === 'function' ? urlConfig(id) : urlConfig);
        
        const method = this.config.deleteMethod || 'DELETE';
        const fetchOptions = { method: method };

        // Jika metode POST, buat FormData untuk mengirim ID dan CSRF
        if (method.toUpperCase() === 'POST') {
            const formData = new FormData();
            
            // Tambahkan ID ke body
            // Asumsi key-nya adalah 'id' sesuai controller: $this->input->post('id', TRUE);
            formData.append('id', id); 
            
            // Tambahkan token CSRF jika ada di konfigurasi
            if (this.config.csrf) {
                formData.append(this.config.csrf.tokenName, this.config.csrf.tokenHash);
            }

            // 3. (BARU) Tambahkan data ekstra jika ada di config
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
            // fetchWrapper akan menangani pembaruan token
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

    /**
     * Menampilkan modal untuk tambah data baru.
     */
    #showAddModal() {
        this.form.reset();
        
        // --- PERBAIKAN ---
        // Hanya set value jika hiddenIdField ada (tidak null)
        if (this.hiddenIdField) {
            this.hiddenIdField.value = '';
        }
        // --- AKHIR PERBAIKAN ---

        this.modalLabel.textContent = this.config.modalTitles.add;
        
        if (typeof this.config.onAdd === 'function') {
            this.config.onAdd(this.form);
        }
        
        this.modalInstance.show();
    }

    /**
     * Menampilkan modal untuk edit data dan mengisinya.
     * @param {HTMLElement} btn - Tombol edit yang diklik.
     */
    #showEditModal(btn) {
        this.form.reset();
        
        // Gunakan callback 'formPopulator' dari config untuk mengisi form
        this.config.formPopulator(this.form, btn.dataset);
        
        this.modalLabel.textContent = this.config.modalTitles.edit;
        this.modalInstance.show();
    }
    
    // --- METODE HELPERS ---

    /**
     * Wrapper untuk Fetch API yang menangani error, parsing JSON,
     * dan auto-refresh token CSRF.
     */
    async #fetchWrapper(url, options = {}) {
        try {
            const response = await fetch(url, options);
            if (!response.ok) {
                throw new Error(`Network response was not ok (${response.status})`);
            }
            
            const result = await response.json();

            // Auto-refresh CSRF token jika server mengirimkannya
            if (result.csrf_hash) {
                this.#updateCsrfToken(result.csrf_hash);
            }

            return result;

        } catch (error) {
            console.error('Fetch Error:', error);
            throw error; // Lempar kembali agar bisa ditangkap oleh pemanggil
        }
    }

    /**
     * Helper untuk memperbarui token CSRF di config dan di semua
     * input CSRF yang ada di halaman.
     * @param {string} hash - Token hash baru dari server.
     */
    #updateCsrfToken(hash) {
        if (this.config.csrf && hash) {
            // 1. Update di config internal
            this.config.csrf.tokenHash = hash;

            // 2. Update semua input CSRF di DOM
            const tokens = document.querySelectorAll(`input[name="${this.config.csrf.tokenName}"]`);
            tokens.forEach(token => token.value = hash);
        }
    }

    /**
     * Inisialisasi (atau hancurkan & inisialisasi ulang) simple-datatables.
     */
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

    /**
     * Menampilkan notifikasi toast SweetAlert2.
     */
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