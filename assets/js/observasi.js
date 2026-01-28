import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

  const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
  const IS_ADMIN_OR_GURU = window.IS_ADMIN_OR_GURU || false;
  const CURRENT_CLASS_ID = window.CURRENT_CLASS_ID || null;

  // Hapus tombol "Tambah" jika Murid
  if (!IS_ADMIN_OR_GURU) {
    ['btnAddObservasi', 'btnAddDiskusi'].forEach(id => {
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

  // --- Inisialisasi CRUD 1: Ruang Observasi ---
  const observasiConfig = {
    baseUrl: window.BASE_URL,
    entityName: 'Ruang Observasi',
    readOnly: !IS_ADMIN_OR_GURU,
    modalId: 'observasiModal',
    formId: 'observasiForm',
    modalLabelId: 'observasiModalLabel',
    hiddenIdField: 'observasiId',
    tableId: 'observasiTable',
    btnAddId: 'btnAddObservasi',
    tableParentSelector: '#observasiContainer', // Tentukan parent tab
    csrf: csrfConfig,
    urls: {
      load: IS_ADMIN_OR_GURU ? `guru/pbl/get_observations/${CURRENT_CLASS_ID}` : `siswa/pbl/get_observations/${CURRENT_CLASS_ID}`,
      save: `guru/pbl/save_observation`,
      delete: (id) => `guru/pbl/delete_observation/${id}`
    },
    deleteMethod: 'POST',
    modalTitles: { add: 'Tambah Ruang Observasi', edit: 'Edit Ruang Observasi' },
    deleteNameField: 'title',

    dataMapper: (q, i) => {
      const detailBtn = `<a href="${window.BASE_URL}${window.URL_NAME}/Pbl_observasi/detail/${q.observation_id}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Detail</a>`;
      
      const actionBtns = IS_ADMIN_OR_GURU ? `
        <button class="btn btn-sm btn-warning btn-edit" data-id="${q.observation_id}" data-title="${q.title}" data-subjects="${q.subjects || ''}" data-instruction="${q.instruction}"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-danger btn-delete" data-id="${q.observation_id}" data-title="${q.title}"><i class="bi bi-trash"></i></button>
      ` : '';

      return [i + 1, q.title, q.subjects || '-', q.instruction, detailBtn + actionBtns];
    },

    formPopulator: (form, data) => {
      form.querySelector('#observasiId').value = data.id;
      form.querySelector('[name="title"]').value = data.title;
      form.querySelector('[name="subjects"]').value = data.subjects || '';
      form.querySelector('[name="instruction"]').value = data.instruction || '';
    },
    onAdd: (form) => {
        form.reset();
    }
  };

  // Ini akan memasang event listener untuk 'btnAddObservasi'.
  new CrudHandler(observasiConfig).init();
  
});