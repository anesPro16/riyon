import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

  const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
  const CURRENT_REFLECTION_ID = window.CURRENT_REFLECTION_ID;
  
  if (!CURRENT_REFLECTION_ID) return;

  const csrfConfig = {
    tokenName: window.CSRF_TOKEN_NAME,
    tokenHash: csrfEl ? csrfEl.value : ''
  };

  // --- 1. Konfigurasi CRUD Prompt (Pertanyaan) ---
  const promptConfig = {
    baseUrl: window.BASE_URL,
    entityName: 'Pertanyaan Refleksi',
    modalId: 'promptModal',
    formId: 'promptForm',
    modalLabelId: 'promptModalLabel',
    tableId: 'promptsTable',
    btnAddId: 'btnAddPrompt',
    hiddenIdField: 'promptId', 
    tableParentSelector: '#promptsTableContainer', 
    csrf: csrfConfig,
    urls: {
      load: `guru/pbl_refleksi_akhir/get_prompts/${CURRENT_REFLECTION_ID}`,
      save: `guru/pbl_refleksi_akhir/save_prompt`,
      delete: (id) => `guru/pbl_refleksi_akhir/delete_prompt/${id}`
    },
    deleteMethod: 'POST',
    modalTitles: { add: 'Tambah Pertanyaan', edit: 'Edit Pertanyaan' },
    deleteNameField: 'prompt', 

    dataMapper: (p, i) => [
      i + 1,
      p.prompt_text.length > 100 ? p.prompt_text.substring(0, 100) + '...' : p.prompt_text,
      `
        <button class="btn btn-sm btn-warning btn-edit"
          data-id="${p.id}"
          data-prompt_text="${p.prompt_text}">
          <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-sm btn-danger btn-delete"
          data-id="${p.id}"
          data-prompt="${p.prompt_text.substring(0, 20)}...">
          <i class="bi bi-trash"></i>
        </button>
      `
    ],
    formPopulator: (form, data) => {
      form.querySelector('#promptId').value = data.id;
      form.querySelector('#prompt_text').value = data.prompt_text;
    },
    onAdd: (form) => {
      form.reset();
      form.querySelector('#promptId').value = '';
    }
  };

  // --- 2. Konfigurasi Read-Only Submissions (Jawaban Siswa) ---
  const submissionConfig = {
    baseUrl: window.BASE_URL,
    entityName: 'Jawaban Siswa',
    modalId: 'viewSubmissionModal', 
    // ID Dummy ini harus kita buatkan elemennya via JS di bawah
    formId: 'dummyFormSubmission', 
    modalLabelId: 'dummyLabelSubmission',
    hiddenIdField: 'dummyIdSubmission',
    tableId: 'submissionsTable',
    
    tableParentSelector: '#submissionsTableContainer',
    csrf: csrfConfig,
    urls: {
      load: `guru/pbl_refleksi_akhir/get_submissions/${CURRENT_REFLECTION_ID}`,
      save: ``, 
      delete: `` 
    },
    modalTitles: { edit: 'Lihat Jawaban' }, 

    dataMapper: (item, i) => {
      // Format Tanggal
      const date = new Date(item.updated_at).toLocaleString('id-ID', {
        dateStyle: 'medium', timeStyle: 'short'
      });

      // Sanitize JSON string untuk mencegah error HTML attribute
      const safeContent = item.submission_content.replace(/'/g, "&apos;");

      const viewBtn = `
        <button class="btn btn-sm btn-info text-white btn-edit"
          data-id="${item.id}"
          data-student_name="${item.student_name}"
          data-submission_content='${safeContent}'>
          <i class="bi bi-eye"></i> Lihat Jawaban
        </button>
      `;

      return [
        i + 1,
        `<strong>${item.student_name}</strong>`,
        date,
        viewBtn
      ];
    },

    formPopulator: (form, data) => {
      // 1. Set Judul Modal (Manual, karena CrudHandler set dummyLabel)
      document.getElementById('studentNameTitle').textContent = data.student_name;

      // 2. Ambil Data Prompt
      const promptsEl = document.getElementById('promptsData');
      const prompts = JSON.parse(promptsEl.dataset.prompts || '[]');

      // 3. Parse Jawaban JSON Siswa
      let answers = {};
      try {
        answers = JSON.parse(data.submission_content);
      } catch (e) {
        console.error("Gagal parse jawaban:", e);
      }

      // 4. Render HTML
      const container = document.getElementById('submissionContentArea');
      let html = '';

      if (prompts.length === 0) {
        html = '<p class="text-muted text-center">Tidak ada pertanyaan untuk ditampilkan.</p>';
      } else {
        prompts.forEach((p, idx) => {
          const ans = answers[p.id] ? answers[p.id] : '<em class="text-muted">Tidak dijawab</em>';
          html += `
            <div class="mb-4 pb-3 border-bottom">
              <h6 class="fw-bold text-primary mb-2">
                ${idx + 1}. ${p.prompt_text}
              </h6>
              <div class="p-3 bg-light rounded border">
                ${ans.replace(/\n/g, '<br>')}
              </div>
            </div>
          `;
        });
      }
      
      container.innerHTML = html;
    },
    
    onAdd: () => {}
  };

  // Init Handler Prompt
  const promptHandler = new CrudHandler(promptConfig);
  promptHandler.init();

  // --- [PERBAIKAN UTAMA] Inject Dummy Elements ---
  // Membuat elemen form, label, dan input palsu agar CrudHandler tidak error
  const dummyElements = [
      { id: 'dummyFormSubmission', tag: 'form' },
      { id: 'dummyLabelSubmission', tag: 'span' }, // Label judul palsu
      { id: 'dummyIdSubmission', tag: 'input' }
  ];

  dummyElements.forEach(el => {
      if (!document.getElementById(el.id)) {
          const domEl = document.createElement(el.tag);
          domEl.id = el.id;
          domEl.style.display = 'none'; // Sembunyikan
          document.body.appendChild(domEl);
      }
  });

  // Init Handler Submission (Sekarang aman karena elemen dummy sudah ada)
  const submissionHandler = new CrudHandler(submissionConfig);
  submissionHandler.init();

});