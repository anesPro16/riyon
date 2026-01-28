import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {
  
  const IS_ADMIN_OR_GURU = window.IS_ADMIN_OR_GURU || false;
  const CURRENT_CLASS_ID = window.CURRENT_CLASS_ID || null;

  //  Hapus tombol "Tambah" jika Murid
  if (!IS_ADMIN_OR_GURU) {
    ['btnAddQuiz'].forEach(id => {
      const btn = document.getElementById(id);
      if (btn) btn.remove(); // Menghapus elemen dari DOM
    });
  }

  const csrfTokenEl = IS_ADMIN_OR_GURU 
    ? document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]') 
    : null;

  if (!CURRENT_CLASS_ID) return console.error('CLASS ID tidak ditemukan.');

  const csrfConfig = {
    tokenName: window.CSRF_TOKEN_NAME,
    tokenHash: (IS_ADMIN_OR_GURU && csrfTokenEl) ? csrfTokenEl.value : ''
  };

  // --- CRUD KUIS ---
  const quizConfig = {
    baseUrl: window.BASE_URL,
    entityName: 'Kuis',
    readOnly: !IS_ADMIN_OR_GURU,
    modalId: 'quizModal', formId: 'quizForm', modalLabelId: 'quizModalLabel', hiddenIdField: 'quizId',
    tableId: 'quizTable', btnAddId: 'btnAddQuiz', tableParentSelector: '.quizTableContainer',
    csrf: csrfConfig,
    urls: {
      load: IS_ADMIN_OR_GURU ? `guru/pbl/get_quizzes/${CURRENT_CLASS_ID}` : `siswa/pbl/get_quizzes/${CURRENT_CLASS_ID}`,
      save: IS_ADMIN_OR_GURU ? `guru/pbl/save_quiz` : null,
      delete: IS_ADMIN_OR_GURU ? (id) => `guru/pbl/delete_quiz/${id}` : null
    },
    deleteMethod: 'POST',
    modalTitles: { add: 'Tambah Kuis', edit: 'Edit Kuis' },
    deleteNameField: 'title',
    
    // [DIPERSINGKAT] Data Mapper Kuis
    dataMapper: (q, i) => {
      const detailBtn = `<a href="${window.BASE_URL}${window.URL_NAME}/pbl_kuis/kuis_detail/${q.quiz_id}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Detail</a>`;
      
      const actionBtns = IS_ADMIN_OR_GURU ? `
        <button class="btn btn-sm btn-warning btn-edit" data-quiz_id="${q.quiz_id}" data-title="${q.title}" data-subjects="${q.subjects || ''}"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-danger btn-delete" data-id="${q.quiz_id}" data-title="${q.title}"><i class="bi bi-trash"></i></button>
      ` : '';

      return [i + 1, q.title, q.subjects || '-', detailBtn + actionBtns];
    },

    formPopulator: IS_ADMIN_OR_GURU ? (form, data) => {
      form.querySelector('#quizId').value = data.quiz_id;
      form.querySelector('#quizTitle').value = data.title;
      // form.querySelector('#quizDescription').value = data.description || '';
      form.querySelector('select[name="subjects"]').value = data.subjects;
    } : null,
    onAdd: IS_ADMIN_OR_GURU ? (form) => { form.reset(); form.querySelector('#quizClassId').value = CURRENT_CLASS_ID; } : null
  };

  new CrudHandler(quizConfig).init();
});