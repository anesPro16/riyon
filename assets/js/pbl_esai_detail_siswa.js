import CrudHandler from './crud_handler.js'; // Pastikan path relative benar sesuai folder structure

document.addEventListener('DOMContentLoaded', () => {

    const ESSAY_ID = document.getElementById('currentEssayId').value;
    const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');

    const csrfConfig = {
        tokenName: window.CSRF_TOKEN_NAME,
        tokenHash: csrfEl ? csrfEl.value : ''
    };

  // ==========================================
  // 1. INSTANCE CRUD: DAFTAR PERTANYAAN (READ ONLY)
  // ==========================================
  const questionConfig = {
    baseUrl: window.BASE_URL,
    entityName: 'Soal',
    // Mode Read Only: Tidak perlu modalId, formId, btnAddId untuk Create/Edit/Delete
    readOnly: true, 
    
    tableId: 'questionTable',
    tableParentSelector: '#questionTableContainer',
    
    csrf: csrfConfig,
    urls: {
      // Menggunakan endpoint siswa
      load: `siswa/pbl_esai/get_questions_json/${ESSAY_ID}`,
    },

    // Mapping Data JSON ke Tabel
    dataMapper: (q, i) => {
        // Tampilkan pertanyaan lengkap
        return [q.question_number, q.question_text];
    }
  };

  // Inisialisasi Tabel Soal
  new CrudHandler(questionConfig).init();

  // ==========================================
  // 2. HANDLER FORMULIR JAWABAN (MANUAL)
  // ==========================================
  const btnOpenModal = document.getElementById('btnOpenAnswerModal');
  const answerModalEl = document.getElementById('answerModal');
  const answerForm = document.getElementById('answerForm');
  
  // Inisialisasi Modal Bootstrap
  let answerModalInstance = null;
  if (answerModalEl) {
    answerModalInstance = new bootstrap.Modal(answerModalEl);
  }

  // A. Event Klik Tombol "Kerjakan/Edit"
  if (btnOpenModal) {
    btnOpenModal.addEventListener('click', () => {
        const id = btnOpenModal.getAttribute('data-id');
        const content = btnOpenModal.getAttribute('data-content');

      // Populate Form
      document.getElementById('submissionId').value = id;
      document.getElementById('submissionContent').value = content;

      // Buka Modal
      answerModalInstance.show();
    });
  }

  // B. Event Submit Form
  if (answerForm) {
    answerForm.addEventListener('submit', async (e) => {
        e.preventDefault();

      // Ambil tombol submit untuk loading state
      const submitBtn = answerForm.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengirim...';

      const formData = new FormData(answerForm);
      
      // Tambahkan CSRF Token manual karena ini fetch custom
      const currentCsrfToken = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]').value;
      formData.append(window.CSRF_TOKEN_NAME, currentCsrfToken);

      try {
        const response = await fetch(window.BASE_URL + 'siswa/pbl_esai/save_submission', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        // Update CSRF Token di halaman agar request berikutnya valid
        if (result.csrf_hash) {
            const tokens = document.querySelectorAll(`input[name="${window.CSRF_TOKEN_NAME}"]`);
            tokens.forEach(t => t.value = result.csrf_hash);
        }

        if (result.status === 'success') {
            answerModalInstance.hide();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload(); // Reload untuk update status UI
              });

        } else {
            Swal.fire('Gagal', result.message, 'error');
        }

      } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
      }
    });
  }
});