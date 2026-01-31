import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

    // Ambil token CSRF awal
    const csrfTokenEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');

    const siswaConfig = {
    	baseUrl: window.BASE_URL,
    	entityName: 'Siswa',

        // --- 1. Selektor DOM ---
        modalId: 'activationModal',
        formId: 'activationForm',
        modalLabelId: 'activationModalLabel',
        hiddenIdField: 'userId', // ID input hidden di modal
        tableId: 'siswaTable',
        // btnAddId: 'btnAddSiswa', // Tidak ada tombol tambah (Registrasi dilakukan siswa sendiri)

        // --- 2. Konfigurasi CSRF ---
        csrf: {
        	tokenName: window.CSRF_TOKEN_NAME,
        	tokenHash: csrfTokenEl ? csrfTokenEl.value : ''
        },

        // --- 3. Endpoint URL (Sesuaikan dengan Controller) ---
        urls: {
        	load: 'admin/dashboard/get_siswa_json',
        	save: 'admin/dashboard/update_activation',
            // delete: (id) => `admin/dashboard/delete_siswa/${id}` // Opsional jika ingin fitur hapus
          },

        // --- 4. Teks Modal ---
        modalTitles: {
            add: 'Tambah Siswa', // Tidak dipakai
            edit: 'Aktivasi Akun Siswa'
          },

        // --- 5. Data Mapping (JSON -> Table Row) ---
        dataMapper: (row, index) => {
            // Logic Badge Status
            const badge = row.is_active == 1
            ? `<span class="badge bg-success"><i class="fas fa-check-circle"></i> Aktif</span>`
            : `<span class="badge bg-secondary"><i class="fas fa-times-circle"></i> Belum Aktif</span>`;

            // Tombol Aksi
            const btnEdit = `
            <button class="btn btn-primary btn-sm btn-edit" 
            data-id="${row.user_id}" 
            data-name="${row.name}" 
            data-is-active="${row.is_active}">
            <i class="fas fa-user-check"></i> Kelola
            </button>
            `;
            
            // Opsional: Tombol Hapus jika diperlukan
            /* const btnDelete = `
                <button class="btn btn-danger btn-sm btn-delete" data-id="${row.user_id}" data-name="${row.name}">
                    <i class="fas fa-trash"></i>
                </button>
            `; 
            */

            return [
            index + 1,
                row.name,       // Nama Siswa
                badge,          // Status (Badge)
                btnEdit         // Kolom Aksi
                ];
              },

        // --- 6. Form Populator (Table -> Modal Input) ---
        formPopulator: (form, data) => {
        	form.querySelector('#userId').value = data.id;
        	
            // Tampilkan nama (Readonly field)
            form.querySelector('#userName').value = data.name;

            // Set Checkbox/Switch
            form.querySelector('#is_active').checked = (data.isActive == 1);
          }
        };

    // Inisialisasi Handler
    const siswaHandler = new CrudHandler(siswaConfig);
    siswaHandler.init();
  });