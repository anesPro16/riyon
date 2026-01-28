import CrudHandler from './crud_handler.js'; // Pastikan path ini benar

document.addEventListener('DOMContentLoaded', () => {

  const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
  const CURRENT_QUIZ_ID = window.CURRENT_QUIZ_ID;
  if (!CURRENT_QUIZ_ID) return;

  const csrfConfig = {
    tokenName: window.CSRF_TOKEN_NAME,
    tokenHash: csrfEl ? csrfEl.value : ''
  };

  const config = {
    baseUrl: window.BASE_URL,
    entityName: 'Pertanyaan',
    modalId: 'questionModal',
    formId: 'questionForm',
    modalLabelId: 'questionModalLabel',
    tableId: 'questionTable',
    btnAddId: 'btnAddQuestion',
    hiddenIdField: 'questionId', // ID field di form modal
    tableParentSelector: '#questionTableContainer', // Parent spesifik
    csrf: csrfConfig,
    urls: {
      load: `guru/pbl_kuis_evaluasi/get_questions/${CURRENT_QUIZ_ID}`,
      save: `guru/pbl_kuis_evaluasi/save_question`,
      delete: (id) => `guru/pbl_kuis_evaluasi/delete_question/${id}`
    },
    deleteMethod: 'POST',
    modalTitles: {
      add: 'Tambah Pertanyaan Baru',
      edit: 'Edit Pertanyaan'
    },
    deleteNameField: 'question', // data-question="..."

    /**
     * Memetakan data dari server ke format tabel simple-datatables
     */
    dataMapper: (q, i) => [
      i + 1,
      // Potong teks pertanyaan jika terlalu panjang
      q.question_text.length > 80 ? q.question_text.substring(0, 80) + '...' : q.question_text,
      `<strong>${q.correct_answer}</strong>`,
      `
        <button class="btn btn-sm btn-warning btn-edit"
          data-id="${q.id}"
          data-question_text="${q.question_text}"
          data-option_a="${q.option_a}"
          data-option_b="${q.option_b}"
          data-option_c="${q.option_c}"
          data-option_d="${q.option_d}"
          data-correct_answer="${q.correct_answer}">
          <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-sm btn-danger btn-delete"
          data-id="${q.id}"
          data-question="${q.question_text.substring(0, 20)}...">
          <i class="bi bi-trash"></i>
        </button>
      `
    ],

    /**
     * Mengisi form modal saat tombol edit diklik
     */
    formPopulator: (form, data) => {
      form.querySelector('#questionId').value = data.id;
      form.querySelector('#question_text').value = data.question_text;
      form.querySelector('#option_a').value = data.option_a;
      form.querySelector('#option_b').value = data.option_b;
      form.querySelector('#option_c').value = data.option_c;
      form.querySelector('#option_d').value = data.option_d;
      form.querySelector('#correct_answer').value = data.correct_answer;
    },

    /**
     * Dijalankan saat tombol 'Tambah' diklik
     */
    onAdd: (form) => {
      form.reset();
      form.querySelector('#questionId').value = '';
    }
  };

  // Inisialisasi CrudHandler
  const handler = new CrudHandler(config);
  handler.init();

});