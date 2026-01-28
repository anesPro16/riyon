import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {
  
  const ESSAY_ID = document.getElementById('currentEssayId').value;
  const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
  
  const csrfConfig = {
    tokenName: window.CSRF_TOKEN_NAME,
    tokenHash: csrfEl ? csrfEl.value : ''
  };

  // ==========================================
  // FUNGSI BANTUAN DOM (DYNAMIC INPUT)
  // ==========================================
  const container = document.getElementById('dynamicQuestionContainer');
  const btnAddRowWrapper = document.getElementById('btnAddRowWrapper');
  const btnAddRow = document.getElementById('btnAddRow');

  /**
   * Fungsi Membuat Baris Input Soal dengan Desain Baru (Clean Layout)
   */
  const createInputRow = (value = '', isRemovable = true) => {
    // 1. Wrapper Utama (Memberikan jarak antar soal)
    const wrapper = document.createElement('div');
    wrapper.className = 'question-row mb-3'; // mb-3 memberi jarak ke bawah
    
    // 2. Input Group (Menggabungkan Icon, Textarea, dan Tombol Hapus)
    const inputGroup = document.createElement('div');
    inputGroup.className = 'input-group shadow-sm'; // shadow-sm agar terlihat modern

    // 3. Icon Indikator di Kiri
    const iconSpan = document.createElement('span');
    iconSpan.className = 'input-group-text bg-white text-primary border-end-0';
    iconSpan.innerHTML = '<i class="bi bi-question-circle"></i>';

    // 4. Textarea Input
    const textarea = document.createElement('textarea');
    textarea.name = 'question_text[]'; 
    textarea.className = 'form-control border-start-0'; // border-start-0 agar menyatu dengan icon
    textarea.rows = 2; // Tinggi default
    textarea.placeholder = 'Tuliskan pertanyaan esai di sini...';
    textarea.value = value;
    textarea.required = true;
    textarea.style.resize = 'vertical'; // User bisa resize vertikal saja

    // Rakit elemen
    inputGroup.appendChild(iconSpan);
    inputGroup.appendChild(textarea);

    // 5. Tombol Hapus (Hanya muncul jika isRemovable = true)
    if (isRemovable) {
      const btnDel = document.createElement('button');
      btnDel.type = 'button';
      btnDel.className = 'btn btn-outline-danger border-start-0';
      btnDel.title = 'Hapus baris ini';
      btnDel.innerHTML = '<i class="bi bi-trash"></i>';
      
      // Event Hapus dengan konfirmasi visual sederhana (fade out)
      btnDel.onclick = () => {
        wrapper.style.transition = 'opacity 0.3s ease';
        wrapper.style.opacity = '0';
        setTimeout(() => wrapper.remove(), 300);
      };
      
      inputGroup.appendChild(btnDel);
    }

    wrapper.appendChild(inputGroup);
    return wrapper;
  };

  // Event Listener Tombol Tambah Baris
  if(btnAddRow) {
    btnAddRow.addEventListener('click', () => {
      const newRow = createInputRow('', true);
      container.appendChild(newRow);
      
      // Auto focus ke input baru
      const newInput = newRow.querySelector('textarea');
      if(newInput) newInput.focus();
    });
  }

  // ==========================================
  // 1. INSTANCE CRUD: DAFTAR PERTANYAAN
  // ==========================================
  const questionConfig = {
    baseUrl: window.BASE_URL,
    entityName: 'Soal',
    modalId: 'questionModal',
    formId: 'questionForm',
    modalLabelId: 'questionModalLabel',
    hiddenIdField: 'questionId',
    tableId: 'questionTable',
    btnAddId: 'btnAddQuestion',
    tableParentSelector: '#questionTableContainer', 
    csrf: csrfConfig,
    urls: {
      load: `guru/pbl_esai/get_questions_json/${ESSAY_ID}`,
      save: `guru/pbl_esai/save_question`,
      delete: (id) => `guru/pbl_esai/delete_question/${id}`
    },
    deleteMethod: 'POST',
    modalTitles: { add: 'Editor Soal', edit: 'Edit Soal' },
    deleteNameField: 'text', 

    dataMapper: (q, i) => {
      // Memotong teks jika terlalu panjang untuk tabel
      const shortText = q.question_text.length > 80 
        ? q.question_text.substring(0, 80) + '...' 
        : q.question_text;
      
      const btns = `
      <div class="d-flex justify-content-end gap-1">
        <button class="btn btn-sm btn-outline-warning btn-edit" 
          data-id="${q.id}" 
          data-question_text="${encodeURIComponent(q.question_text)}">
          <i class="bi bi-pencil-square"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger btn-delete" 
          data-id="${q.id}" 
          data-text="Soal No. ${q.question_number}">
          <i class="bi bi-trash"></i>
        </button>
      </div>
      `;
      return [q.question_number, shortText, btns];
    },

    // Dijalankan saat tombol "Tambah Soal" diklik
    onAdd: (form) => {
        container.innerHTML = ''; // Reset container
        // Tambah 1 baris default (tidak bisa dihapus agar minimal ada 1 input)
        container.appendChild(createInputRow('', false));
        
        // Tampilkan tombol "Tambah Baris" karena ini mode tambah banyak
        if(btnAddRowWrapper) btnAddRowWrapper.style.display = 'block';
    },

    // Dijalankan saat tombol "Edit" diklik (Single Edit Mode)
    formPopulator: (form, data) => {
        container.innerHTML = ''; // Reset container
        
        // Decode text karena kita menggunakan encodeURIComponent di dataMapper
        const decodedText = decodeURIComponent(data.question_text);

        // Masukkan data yang ada ke input
        // isRemovable = false karena mode edit per satu soal
        container.appendChild(createInputRow(decodedText, false));

        form.querySelector('#questionId').value = data.id;
        
        // Sembunyikan tombol "Tambah Baris" saat mode edit single
        if(btnAddRowWrapper) btnAddRowWrapper.style.display = 'none';
    }
  };

  // ==========================================
  // 2. INSTANCE CRUD: PENILAIAN (GRADING)
  // ==========================================
  const gradingConfig = {
    baseUrl: window.BASE_URL,
    entityName: 'Nilai',
    modalId: 'gradeModal',
    formId: 'gradeForm',
    modalLabelId: 'gradeModalLabel', 
    tableId: 'gradingTable',
    tableParentSelector: '#gradingTableContainer',
    
    csrf: csrfConfig,
    urls: {
      load: `guru/pbl_esai/get_grading_json/${ESSAY_ID}`,
      save: `guru/pbl_esai/save_grade`,
      delete: null 
    },
    modalTitles: { edit: 'Penilaian Siswa' },

    dataMapper: (s, i) => {
      let dateText = '<span class="text-muted small">Belum dikirim</span>';
      let gradeText = '-';
      let btnClass = 'btn-outline-secondary disabled';
      let btnIcon = 'bi-dash-circle';
      let btnText = 'Nilai';
      let isDisabled = 'disabled';

      if (s.submission_id) {
        // Format tanggal
        const dateObj = new Date(s.submitted_at);
        const dateStr = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        const timeStr = dateObj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        dateText = `<div class="d-flex flex-column"><span class="fw-bold text-dark">${dateStr}</span><span class="text-muted small">${timeStr}</span></div>`;
        
        // Badge Nilai
        if (s.grade !== null) {
            gradeText = `<span class="badge bg-success fs-6">${s.grade}</span>`;
            btnClass = 'btn-success btn-edit text-white'; 
            btnIcon = 'bi-pencil-square';
            btnText = 'Ubah Nilai';
        } else {
            gradeText = `<span class="badge bg-secondary">Belum Dinilai</span>`;
            btnClass = 'btn-primary btn-edit'; 
            btnIcon = 'bi-check2-circle';
            btnText = 'Beri Nilai';
        }
        isDisabled = '';
      }

      // Encode content agar aman saat dimasukkan ke atribut data
      const safeContent = s.submission_content ? encodeURIComponent(s.submission_content) : '';

      const actionBtn = `
      <div class="text-end">
        <button class="btn btn-sm ${btnClass}" ${isDisabled}
        data-id="${s.submission_id}" 
        data-student_name="${s.student_name}"
        data-content="${safeContent}"
        data-grade="${s.grade || ''}"
        data-feedback="${s.feedback || ''}">
        <i class="bi ${btnIcon} me-1"></i> ${btnText}
        </button>
      </div>
      `;

      return [i + 1, s.student_name, dateText, gradeText, actionBtn];
    },

    formPopulator: (form, data) => {
      form.querySelector('#submissionId').value = data.id; 
      
      const labelEl = document.getElementById('gradeModalLabel');
      if(labelEl) labelEl.innerHTML = `<i class="bi bi-person-check me-2"></i>Penilaian: <strong>${data.student_name}</strong>`;

      const content = data.content ? decodeURIComponent(data.content) : '<p class="text-muted fst-italic">Tidak ada jawaban.</p>';
      
      // Update area baca jawaban
      document.getElementById('studentAnswerContent').innerHTML = content.replace(/\n/g, '<br>');

      form.querySelector('#gradeInput').value = data.grade;
      form.querySelector('#feedbackInput').value = data.feedback;
    }
  };

  new CrudHandler(questionConfig).init();
  new CrudHandler(gradingConfig).init();
});