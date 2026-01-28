// Impor class CrudHandler
import CrudHandler from '../crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {
	
	// Ambil status hak akses dari PHP
	const IS_ADMIN_OR_GURU = window.IS_ADMIN_OR_GURU || false;
	const CURRENT_CLASS_ID = window.CURRENT_CLASS_ID || null;
	
	// Ambil CSRF token (hanya jika admin/guru, karena modalnya ada)
	const csrfTokenEl = IS_ADMIN_OR_GURU 
		? document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]') 
		: null;

	if (!CURRENT_CLASS_ID) {
		console.error('CLASS ID tidak ditemukan. Skrip dibatalkan.');
		return;
	}

	// Konfigurasi CrudHandler untuk TABEL SISWA
	const studentConfig = {
		baseUrl: window.BASE_URL,
		entityName: 'Siswa',

		// (BARU) Aktifkan mode readOnly jika BUKAN admin/guru
		readOnly: !IS_ADMIN_OR_GURU,

		// --- 1. Selektor DOM ---
		// Kirim ID-nya. Handler akan mengabaikannya jika readOnly.
		modalId: 'siswaModal',
		formId: 'studentForm',
		modalLabelId: 'siswaModalLabel',
		hiddenIdField: null, 
		tableId: 'siswaTable',
		btnAddId: 'btnAddStudent',
		tableParentSelector: '#siswaTableContainer',

		// --- 2. Konfigurasi CSRF ---
		csrf: {
			tokenName: window.CSRF_TOKEN_NAME,
			tokenHash: (IS_ADMIN_OR_GURU && csrfTokenEl) ? csrfTokenEl.value : ''
		},

		// --- 3. Endpoint URL ---
		urls: {
			load: window.IS_ADMIN_OR_GURU 
        ? `guru/dashboard/getStudentListForClass/${CURRENT_CLASS_ID}` 
        : `siswa/dashboard/getStudentListForClass/${CURRENT_CLASS_ID}`,
			// Handler akan mengabaikan 'save' & 'delete' jika readOnly
			save: 'guru/dashboard/add_student_to_class',
			delete: 'guru/dashboard/remove_student_from_class' 
		},
		deleteMethod: 'POST',

		// --- 4. Teks Spesifik ---
		modalTitles: {
			add: 'Tambah Siswa ke Kelas',
			edit: ''
		},
		deleteNameField: 'name',

		// Data Ekstra (Handler akan mengabaikannya jika readOnly)
		extraDeleteData: {
			class_id: CURRENT_CLASS_ID
		},

		// --- 5. Logika Spesifik (Callback) ---
		dataMapper: (student, index) => {
			
			// Kolom dasar (untuk semua role)
			const rowData = [
				index + 1,
				student.name,
				student.username,
				student.email || '-'
			];

			// (KONDISIONAL) Hanya tambahkan kolom Aksi jika admin/guru
			if (IS_ADMIN_OR_GURU) {
				rowData.push(`
					<button class="btn btn-danger btn-sm btn-delete" 
					data-id="${student.id}" 
					data-name="${student.name}">
					<i class="fas fa-user-minus"></i> Keluarkan
					</button>
				`);
			}
			
			// Kembalikan 4 kolom (Siswa) atau 5 kolom (Admin/Guru)
			// Ini akan cocok dengan <thead> kondisional di PHP
			return rowData;
		},

		formPopulator: (form, data) => {
			// Tidak ada implementasi
		},

		onAdd: (form) => {
			// Handler tidak akan memanggil ini jika readOnly
			form.reset(); 
			form.querySelector('#classIdHidden').value = CURRENT_CLASS_ID;
		}
	};

	// Inisialisasi handler
	const studentHandler = new CrudHandler(studentConfig);
	studentHandler.init(); // Ini sekarang aman untuk semua role
});