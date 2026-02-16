import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

  const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
  const CURRENT_QUIZ_ID = window.QUIZ_ID;
  if (!CURRENT_QUIZ_ID) return;

  const csrfConfig = {
    tokenName: window.CSRF_TOKEN_NAME,
    tokenHash: csrfEl ? csrfEl.value : ''
  };

  // ============================================================
  // 1. KONFIGURASI TABEL SOAL (CRUD Lengkap)
  // ============================================================
  const questionConfig = {
    baseUrl: window.BASE_URL,
    entityName: 'Soal',
    modalId: 'questionModal',
    formId: 'questionForm',
    modalLabelId: 'questionModalLabel',
    tableId: 'questionTable',
    tableParentSelector: '.kuisContainer',
    btnAddId: 'btnAddQuestion',
    hiddenIdField: 'questionId',
    csrf: csrfConfig,
    urls: {
      load: `guru/pbl_kuis/get_quiz_questions/${CURRENT_QUIZ_ID}`,
      save: `guru/pbl_kuis/save_quiz_question`,
      delete: `guru/pbl_kuis/delete_quiz_question`
    },
    deleteMethod: 'POST',
    modalTitles: {
      add: 'Tambah Soal Baru',
      edit: 'Edit Soal'
    },
    deleteNameField: 'question', 

    // Mapper Soal
    dataMapper: (q, i) => [
      i + 1,
      q.question_text.length > 60 ? q.question_text.substring(0, 60) + '...' : q.question_text,
      `<strong>${q.correct_answer}</strong>`,
      `
        <button class="btn btn-sm btn-warning btn-edit"
          data-question_id="${q.question_id}"
          data-question_text="${q.question_text}"
          data-option_a="${q.option_a}"
          data-option_b="${q.option_b}"
          data-option_c="${q.option_c}"
          data-option_d="${q.option_d}"
          data-correct_answer="${q.correct_answer}">
          <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-sm btn-danger btn-delete"
          data-id="${q.question_id}"
          data-question="${q.question_text.substring(0, 20)}...">
          <i class="bi bi-trash"></i>
        </button>
      `
    ],

    formPopulator: (form, data) => {
      form.querySelector('#questionId').value = data.question_id;
      form.querySelector('#question_text').value = data.question_text;
      form.querySelector('#option_a').value = data.option_a;
      form.querySelector('#option_b').value = data.option_b;
      form.querySelector('#option_c').value = data.option_c;
      form.querySelector('#option_d').value = data.option_d;
      form.querySelector('#correct_answer').value = data.correct_answer;
    },

    onAdd: (form) => {
      form.reset();
      form.querySelector('#questionId').value = '';
    }
  };

  // Init Handler Soal
  const questionHandler = new CrudHandler(questionConfig);
  questionHandler.init();


  // ============================================================
  // 2. KONFIGURASI TABEL NILAI SISWA (Read & Delete Only)
  // ============================================================
  const submissionConfig = {
    baseUrl: window.BASE_URL,
    entityName: 'Nilai Siswa',
    tableId: 'submissionsTable',
    tableParentSelector: '#submissionsTableContainer', // Opsional, default .card-body
    
    // Tidak butuh modalId/formId/btnAddId karena tidak ada fitur tambah/edit manual
    
    csrf: csrfConfig,
    urls: {
      load: `guru/pbl_kuis/get_quiz_submissions/${CURRENT_QUIZ_ID}`,
      // save: Tidak dipakai
      delete: `guru/pbl_kuis/delete_quiz_submission`
    },
    deleteMethod: 'POST',
    deleteNameField: 'student_name', // Nama field untuk konfirmasi hapus

    // Mapper Nilai
    dataMapper: (res, i) => {
        // Format Tanggal
        const date = new Date(res.created_at).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
        });

        // Warna badge nilai
        let badgeClass = 'bg-danger';
        if(res.score >= 80) badgeClass = 'bg-success';
        else if(res.score >= 60) badgeClass = 'bg-warning text-dark';

        return [
            i + 1,
            `<strong>${res.student_name}</strong><br><small class="text-muted">${res.username}</small>`,
            `<span class="badge ${badgeClass}" style="font-size:1em">${res.score}</span> <br> <small>(${res.total_correct}/${res.total_questions} Benar)</small>`,
            `<small>${date}</small>`,
            `
            <button class="btn btn-sm btn-outline-danger btn-delete"
              data-id="${res.result_id}"
              data-student_name="${res.student_name} (Nilai: ${res.score})">
              <i class="bi bi-trash"></i> Reset
            </button>
            `
        ];
    },
    
    // Dummy functions karena tidak ada form edit/add
    formPopulator: () => {},
    onAdd: () => {}
  };

  // Init Handler Nilai
  const submissionHandler = new CrudHandler(submissionConfig);
  submissionHandler.init();

});