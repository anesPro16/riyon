import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

  const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
  const IS_ADMIN_OR_GURU = window.IS_ADMIN_OR_GURU || false;
  const CURRENT_CLASS_ID = window.CURRENT_CLASS_ID || null;

  // Hapus tombol "Tambah" jika Murid
  if (!IS_ADMIN_OR_GURU) {
    ['btnAddEsai', 'btnAddKuisEvaluasi'].forEach(id => {
      const btn = document.getElementById(id);
      if (btn) btn.remove(); // Menghapus elemen dari DOM
    });
  }

  if (!CURRENT_CLASS_ID) {
    console.error('CLASS ID tidak ditemukan.');
    return;
  }

  const csrfConfig = {
    tokenName: window.CSRF_TOKEN_NAME,
    tokenHash: csrfEl ? csrfEl.value : ''
  };

  // --- Inisialisasi CRUD 1: Esai Solusi ---
  const esaiConfig = {
    baseUrl: window.BASE_URL,
    entityName: 'Esai',
    modalId: 'esaiModal',
    formId: 'esaiForm',
    modalLabelId: 'esaiModalLabel',
    hiddenIdField: 'esaiId',
    tableId: 'esaiTable',
    btnAddId: 'btnAddEsai',
    tableParentSelector: '#solusi', // Parent tab
    csrf: csrfConfig,
    urls: {
      load: IS_ADMIN_OR_GURU ? `guru/pbl/get_solution_essays/${CURRENT_CLASS_ID}` : `siswa/pbl/get_solution_essays/${CURRENT_CLASS_ID}`,
      save: `guru/pbl/save_solution_essay`,
      delete: (id) => `guru/pbl/delete_solution_essay/${id}`
    },
    deleteMethod: 'POST',
    modalTitles: { add: 'Tambah Esai', edit: 'Edit Esai' },
    deleteNameField: 'title', // (data-title dari tombol delete)

    dataMapper: (q, i) => {
      const detailBtn = `<a href="${window.BASE_URL}${window.URL_NAME}/pbl_esai/detail/${q.essay_id}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Detail</a>`;
      
      const actionBtns = IS_ADMIN_OR_GURU ? `
        <button class="btn btn-sm btn-warning btn-edit" data-id="${q.essay_id}" data-title="${q.title}" data-subjects="${q.subjects || ''}"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-danger btn-delete" data-id="${q.essay_id}" data-title="${q.title}"><i class="bi bi-trash"></i></button>
      ` : '';

      return [i + 1, q.title, q.subjects || '-', detailBtn + actionBtns];
    },

    formPopulator: (form, data) => {
      form.querySelector('#esaiId').value = data.id;
      form.querySelector('[name="title"]').value = data.title;
      form.querySelector('[name="subjects"]').value = data.subjects || '';
    }
  };

  // Inisialisasi handler
  new CrudHandler(esaiConfig).init();
  
});