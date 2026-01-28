// Impor class CrudHandler
import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {
  // Ambil token CSRF
  const csrfTokenEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');

  // Cache elemen form yang akan di-disable/enable
  const usernameEl = document.getElementById('teacherUsername');
  const passwordEl = document.getElementById('teacherPassword');
  const passwordGroup = document.getElementById('passwordGroup');

  // Konfigurasi spesifik untuk modul "Guru"
  const teacherConfig = {
  	baseUrl: window.BASE_URL,
  	entityName: 'Guru',

    // --- 1. Selektor DOM ---
    modalId: 'teacherModal',
    formId: 'teacherForm',
    modalLabelId: 'teacherModalLabel',
    hiddenIdField: 'teacherId',
    tableId: 'teacherTable',
    btnAddId: 'btnAddTeacher',
    tableParentSelector: '.card-body',

    // --- 2. Konfigurasi CSRF ---
    csrf: {
    	tokenName: window.CSRF_TOKEN_NAME,
    	tokenHash: csrfTokenEl ? csrfTokenEl.value : ''
    },

    // --- 3. Endpoint URL ---
    urls: {
    	load: 'admin/dashboard/getTeacherList', 
    	save: 'admin/dashboard/teacher_save',
    	delete: 'admin/dashboard/teacher_delete' 
    },
    deleteMethod: 'POST',

    // --- 4. Teks Spesifik ---
    modalTitles: {
    	add: 'Tambah Guru Baru',
    	edit: 'Edit Data Guru'
    },
    deleteNameField: 'name', 

    // --- 5. Logika Spesifik (Callback) ---

    /**
     * Mapper data JSON ke array untuk simple-datatable.
     * REVISI: Menghapus index sekolah
     */
     dataMapper: (teacher, index) => {
     	return [
	     	index + 1,
	     	teacher.name,       
	     	teacher.username,   
	     	teacher.email || '-',
	        // Sekolah Dihapus
	        `
	        <button class="btn btn-warning btn-sm btn-edit" 
	        data-id="${teacher.id}" 
	        data-name="${teacher.name}"
	        data-username="${teacher.username}"
	        data-email="${teacher.email || ''}">
	        <i class="fas fa-edit"></i> Edit
	        </button>
	        <button class="btn btn-danger btn-sm btn-delete" 
	        data-id="${teacher.id}" 
	        data-name="${teacher.name}">
	        <i class="fas fa-trash"></i> Hapus
	        </button>
	        `
	      ];
    	},

    /**
     * Pengisi form saat tombol "Edit" diklik.
     * REVISI: Menghapus logika pengisian field sekolah
     */
     formPopulator: (form, data) => {
        // Isi data dasar
        form.querySelector('#teacherId').value = data.id;
        form.querySelector('#teacherName').value = data.name;
        form.querySelector('#teacherEmail').value = data.email;
        // Sekolah dihapus

        // Logika mode EDIT:
        usernameEl.value = data.username;
        usernameEl.readOnly = true; // Use readOnly property instead of attribute usually
        // Jika ingin konsisten dengan HTML attribute:
        usernameEl.setAttribute('readonly', true);
        
        usernameEl.required = false; 
        
        passwordEl.value = '';
        passwordEl.disabled = true;
        passwordEl.required = false;
        passwordGroup.style.display = 'none'; 
      },

    /**
     * Hook saat modal "Tambah" dibuka.
     */
     onAdd: (form) => {
     	usernameEl.removeAttribute('readonly');
     	usernameEl.disabled = false;
     	usernameEl.required = true; 

     	passwordEl.disabled = false;
     	passwordEl.required = false; 
     	passwordGroup.style.display = 'block'; 
     }
   };

  // Inisialisasi handler
  const teacherHandler = new CrudHandler(teacherConfig);
  teacherHandler.init();
});