// Impor class CrudHandler
import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {
	
	// Ambil konfigurasi dari View
	const CURRENT_CLASS_ID = window.CURRENT_CLASS_ID || null;
	const CAN_MANAGE = window.CAN_MANAGE_STUDENTS || false; // True = Admin, False = Guru
	const ROLE = window.ROLE_CONTROLLER || 'guru'; // 'admin' atau 'guru'

	if (!CURRENT_CLASS_ID) {
		console.error('CLASS ID tidak ditemukan. Skrip dibatalkan.');
		return;
	}

	// Tentukan Base URL endpoint berdasarkan Role
	const controllerUrl = ROLE === 'admin' ? 'admin/dashboard' : 'guru/dashboard';

	// Ambil CSRF token (hanya jika bisa manage, karena form ada di sana)
	const csrfTokenEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');

	// Konfigurasi CrudHandler untuk TABEL SISWA
	const studentConfig = {
		baseUrl: window.BASE_URL,
		entityName: 'Siswa',

		// Jika Guru -> Read Only (sesuai request: admin kelola, guru lihat)
		readOnly: !CAN_MANAGE,

		// --- 1. Selektor DOM ---
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
			tokenHash: csrfTokenEl ? csrfTokenEl.value : ''
		},

		// --- 3. Endpoint URL Dinamis ---
		urls: {
			load: `${controllerUrl}/getStudentListForClass/${CURRENT_CLASS_ID}`,
			save: `${controllerUrl}/add_student_to_class`,
			delete: `${controllerUrl}/remove_student_from_class` 
		},
		deleteMethod: 'POST',

		// --- 4. Teks Spesifik ---
		modalTitles: {
			add: 'Tambah Siswa ke Kelas',
			edit: ''
		},
		deleteNameField: 'name',

		// Data Ekstra untuk Delete
		extraDeleteData: {
			class_id: CURRENT_CLASS_ID
		},

		// --- 5. Data Mapper ---
		dataMapper: (student, index) => {
			
			// Kolom dasar
			const rowData = [
				index + 1,
				student.name,
				student.username,
				student.email || '-'
			];

			// Hanya tambahkan kolom Aksi jika boleh mengelola (Admin)
			if (CAN_MANAGE) {
				rowData.push(`
					<button class="btn btn-danger btn-sm btn-delete" 
					data-id="${student.id}" 
					data-name="${student.name}">
					<i class="fas fa-user-minus"></i> Keluarkan
					</button>
				`);
			}
			
			return rowData;
		},

		formPopulator: (form, data) => {},

		onAdd: (form) => {
			if (CAN_MANAGE) {
				form.reset(); 
				const hiddenField = form.querySelector('#classIdHidden');
				if (hiddenField) hiddenField.value = CURRENT_CLASS_ID;
			}
		}
	};

	// Inisialisasi handler
	const studentHandler = new CrudHandler(studentConfig);
	studentHandler.init(); 
});