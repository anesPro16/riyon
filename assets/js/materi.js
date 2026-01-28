import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

	const csrfTokenEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
	const classIdEl = document.getElementById('classIdHidden');
	const CURRENT_CLASS_ID = classIdEl ? classIdEl.value : null;

	if (!CURRENT_CLASS_ID) {
		console.error('CLASS ID tidak ditemukan. CRUD PBL dibatalkan.');
		return;
	}

  // --- LOGIKA PREVIEW FILE ---
const modalPreviewEl = document.getElementById('previewModal');
const modalPreviewInstance = modalPreviewEl ? new bootstrap.Modal(modalPreviewEl) : null;
const previewContainer = document.getElementById('previewContainer');
const btnDownload = document.getElementById('btnDownload'); // Referensi ini tetap dipakai terus
const labelFilename = document.getElementById('previewFilename');

/**
 * Fungsi untuk membuka modal preview berdasarkan tipe file
 * @param {string} url - URL lengkap file
 */
const openPreview = (url) => {
    if (!modalPreviewInstance) return;

    // 1. Bersihkan konten sebelumnya
    previewContainer.innerHTML = '<div class="spinner-border text-primary" role="status"></div>';

    // 2. Setup Label Nama File
    const filename = url.split('/').pop();
    labelFilename.textContent = filename;

    // 3. Setup Tombol Download
    // PERBAIKAN: Gunakan .onclick langsung.
    // Tidak perlu cloneNode atau replaceChild. 
    // Setiap kali fungsi ini dipanggil, aksi klik akan diperbarui ke URL yang baru.
    if (btnDownload) {
        btnDownload.onclick = (e) => {
            e.preventDefault();
            // Trik download tersembunyi
            const link = document.createElement('a');
            link.href = url;
            link.download = filename; 
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };
    }

    // 4. Logika Tampilan Konten
    const ext = url.split('.').pop().toLowerCase();
    let content = '';

    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
        content = `<img src="${url}" class="img-fluid rounded shadow-sm" style="max-height: 70vh;" alt="Preview">`;
    } else if (ext === 'pdf') {
        content = `<iframe src="${url}" width="100%" height="500px" style="border:none;"></iframe>`;
    } else if (['mp4', 'webm', 'ogg'].includes(ext)) {
        content = `
        <video controls width="100%" style="max-height: 70vh; outline:none;" class="rounded shadow-sm">
            <source src="${url}" type="video/${ext}">
            Browser Anda tidak mendukung tag video.
        </video>`;
    } else {
        content = `
        <div class="p-5 text-center">
            <i class="bi bi-file-earmark-text display-1 text-secondary"></i>
            <p class="mt-3">Preview tidak tersedia untuk format <b>.${ext}</b>.</p>
            <button class="btn btn-primary" onclick="window.open('${url}', '_blank')">Buka File di Tab Baru</button>
        </div>`;
    }

    // Render konten dengan delay
    setTimeout(() => {
        previewContainer.innerHTML = content;
    }, 200);

    modalPreviewInstance.show();
};


  // --- KONFIGURASI CRUD HANDLER ---
  const pblConfig = {
  	baseUrl: window.BASE_URL,
  	entityName: 'Materi',
  	readOnly: !window.IS_ADMIN_OR_GURU,

    // DOM SELECTORS
    modalId: 'pblModal',
    formId: 'pblForm',
    modalLabelId: 'pblModalLabel',
    hiddenIdField: 'pblId',
    tableId: 'pblTable',
    btnAddId: 'btnAddPbl',
    tableParentSelector: '.card-body', // Area event delegation

    // CSRF CONFIG
    csrf: {
    	tokenName: window.CSRF_TOKEN_NAME,
    	tokenHash: csrfTokenEl ? csrfTokenEl.value : ''
    },

    // API ENDPOINTS
    urls: {
    	load: window.IS_ADMIN_OR_GURU 
    	? `guru/pbl/get_data/${CURRENT_CLASS_ID}` 
    	: `siswa/pbl/get_data/${CURRENT_CLASS_ID}`,
    	save: `guru/pbl/save`,
    	delete: (id) => `guru/pbl/delete/${id}`
    },
    deleteMethod: 'POST',

    // UI TEXTS
    modalTitles: {
    	add: 'Tambah Materi',
    	edit: 'Edit Materi'
    },
    deleteNameField: 'title',

    // RENDER TABLE ROW (Custom Data Mapper)
    dataMapper: (item, index) => {
      // Logika Tombol File
      let fileButton = '<span class="text-muted fst-italic small">- Tidak ada file -</span>';
      
      if (item.file_path) {
      	const fullUrl = `${window.BASE_URL}${item.file_path}`;
      	fileButton = `
      	<button class="btn btn-sm btn-info btn-preview" 
      	data-url="${fullUrl}" 
      	title="Lihat Materi">
      	<i class="bi bi-eye-fill"></i> Lihat
      	</button>
      	`;
      }

      // Kolom Dasar
      const rowData = [
      `<div class="text-center fw-bold">${index + 1}</div>`,
      `<div class="fw-bold text-dark">${item.title}</div>`,
      `<div class="text-muted small">${item.reflection}</div>`,
      `<div class="text-center">${fileButton}</div>`
      ];

      // Kolom Aksi (Hanya Guru/Admin)
      if (window.IS_ADMIN_OR_GURU) {
      	rowData.push(`
      		<div class="text-center">
      		<button class="btn btn-sm btn-warning btn-edit me-1"
      		data-id="${item.id}"
      		data-title="${item.title}"
      		data-reflection="${item.reflection}"
          title="Ubah Materi">
      		<i class="bi bi-pencil-square"></i>
      		</button>
      		<button class="btn btn-sm btn-danger btn-delete"
      		data-id="${item.id}"
      		data-title="${item.title}"
          title="Hapus Materi">
      		<i class="bi bi-trash"></i>
      		</button>
      		</div>
      	`);
      }

      return rowData;
    },

    // POPULATE FORM EDIT
    formPopulator: (form, data) => {
    	form.querySelector('#pblId').value = data.id;
    	form.querySelector('#pblTitle').value = data.title;
    	form.querySelector('#pblReflection').value = data.reflection;
      // Reset input file karena file tidak bisa di-set valuenya secara programatis
      form.querySelector('#pblFile').value = ''; 
    },

    // RESET FORM SAAT TAMBAH
    onAdd: (form) => {
    	form.reset();
    	form.querySelector('input[name="class_id"]').value = CURRENT_CLASS_ID;
    }
  };

  // INIT CRUD HANDLER
  const pblHandler = new CrudHandler(pblConfig);
  pblHandler.init();


  // --- EVENT LISTENER TAMBAHAN (DI LUAR CRUD HANDLER) ---
  
  // Listener untuk tombol Preview (Event Delegation pada Table Parent)
  const tableParent = document.querySelector(pblConfig.tableParentSelector);
  if (tableParent) {
  	tableParent.addEventListener('click', (e) => {
  		const btnPreview = e.target.closest('.btn-preview');
  		if (btnPreview) {
  			e.preventDefault();
  			const url = btnPreview.dataset.url;
  			if (url) openPreview(url);
  		}
  	});
  }
});