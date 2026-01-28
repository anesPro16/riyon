import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {
    const EXAM_ID = window.EXAM_ID;
    const container = document.getElementById('questionsContainer'); // Container Modal Bulk
    const btnAddRow = document.getElementById('btnAddRow');
    const template = document.getElementById('questionRowTemplate');
    const bulkForm = document.getElementById('bulkForm');
    const csrfTokenName = window.CSRF_TOKEN_NAME;
    
    // ============================================
    // 1. LOGIKA MODAL BULK INSERT (TIDAK BERUBAH)
    // ============================================
    const addQuestionRow = () => {
        const clone = template.content.cloneNode(true);
        container.appendChild(clone);
        updateRowNumbers();
    };

    const updateRowNumbers = () => {
        const rows = container.querySelectorAll('.question-row');
        rows.forEach((row, index) => {
            row.querySelector('.row-number').textContent = index + 1;
        });
    };

    if (btnAddRow) {
        addQuestionRow(); 
        btnAddRow.addEventListener('click', () => {
            addQuestionRow();
            container.lastElementChild.scrollIntoView({ behavior: 'smooth' });
        });
    }

    if (container) {
        container.addEventListener('click', (e) => {
            if (e.target.closest('.btn-remove-row')) {
                const row = e.target.closest('.question-row');
                if (container.querySelectorAll('.question-row').length > 1) {
                    row.remove();
                    updateRowNumbers();
                } else {
                    Swal.fire('Info', 'Minimal harus ada satu soal.', 'info');
                }
            }
        });
    }

    if (bulkForm) {
        bulkForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(bulkForm);
            const currentCsrf = document.querySelector(`input[name="${csrfTokenName}"]`).value;
            formData.set(csrfTokenName, currentCsrf);

            try {
                const response = await fetch(`${window.BASE_URL}exam/save_questions_batch`, {
                    method: 'POST', body: formData
                });
                const result = await response.json();
                
                if (result.csrf_hash) document.querySelectorAll(`input[name="${csrfTokenName}"]`).forEach(el => el.value = result.csrf_hash);

                if (result.status === 'success') {
                    Swal.fire('Berhasil', result.message, 'success');
                    container.innerHTML = ''; 
                    addQuestionRow(); 
                    
                    const modalEl = document.getElementById('bulkQuestionModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    modalInstance.hide();

                    questionHandler.loadData(); 
                } else {
                    Swal.fire('Gagal', result.message, 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Terjadi kesalahan server', 'error');
            }
        });
    }

    // ============================================
    // 2. LOGIKA EDIT SATUAN (DIPERBAIKI)
    // ============================================
    
    // Inisialisasi Modal Edit & Form
    const editModalEl = document.getElementById('editQuestionModal');
    // Cek dulu apakah elemen modal ada untuk mencegah error JS
    let editModal = null;
    if (editModalEl) {
         editModal = new bootstrap.Modal(editModalEl);
    }
    const editForm = document.getElementById('editQuestionForm');

    // Handle Submit Edit
    if (editForm) {
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(editForm);
            const currentCsrf = document.querySelector(`input[name="${csrfTokenName}"]`).value;
            formData.set(csrfTokenName, currentCsrf);

            try {
                const response = await fetch(`${window.BASE_URL}exam/save_question`, {
                    method: 'POST', body: formData
                });
                const result = await response.json();

                if (result.csrf_hash) document.querySelectorAll(`input[name="${csrfTokenName}"]`).forEach(el => el.value = result.csrf_hash);

                if (result.status === 'success') {
                    Swal.fire('Berhasil', result.message, 'success');
                    if (editModal) editModal.hide();
                    questionHandler.loadData(); // Reload tabel
                } else {
                    Swal.fire('Gagal', result.message, 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Gagal menyimpan perubahan', 'error');
            }
        });
    }

    // ============================================
    // 3. CRUD HANDLER & EVENT DELEGATION (SOLUSI)
    // ============================================
    
    const questionConfig = {
        baseUrl: window.BASE_URL,
        entityName: 'Soal',
        tableId: 'questionTable',
        readOnly: false,
        csrf: { tokenName: csrfTokenName, tokenHash: document.querySelector(`input[name="${csrfTokenName}"]`)?.value || '' },
        
        urls: {
            load: `exam/get_questions/${EXAM_ID}`,
            delete: (id) => `exam/delete_question`
        },
        deleteMethod: 'POST',
        deleteNameField: 'question_preview', 

        dataMapper: (item, index) => {
            const shortQ = item.question.length > 60 ? item.question.substring(0, 60) + '...' : item.question;
            // Gunakan replace untuk kutip ganda agar aman di atribut HTML
            const safeQuestion = item.question.replace(/"/g, '&quot;');
            
            const btnEdit = `<button class="btn btn-sm btn-warning btn-edit-single me-1" 
                data-id="${item.id}"
                data-question="${safeQuestion}"
                data-option_a="${item.option_a}"
                data-option_b="${item.option_b}"
                data-option_c="${item.option_c}"
                data-option_d="${item.option_d}"
                data-correct="${item.correct_answer}">
                <i class="bi bi-pencil-square"></i>
            </button>`;

            const btnDelete = `<button class="btn btn-sm btn-danger btn-delete" 
                data-id="${item.id}" data-question_preview="${shortQ}">
                <i class="bi bi-trash"></i>
            </button>`;

            return [
                index + 1,
                `<div>${shortQ}</div>
                 <small class="text-muted" style="font-size:0.8em">
                    A: ${item.option_a} | B: ${item.option_b} <br>
                    C: ${item.option_c} | D: ${item.option_d}
                 </small>`,
                `<span class="badge bg-success fw-bold">${item.correct_answer}</span>`,
                `<div class="d-flex">${btnEdit} ${btnDelete}</div>`
            ];
        }
    };

    const questionHandler = new CrudHandler(questionConfig);
    questionHandler.init();
    
    // --- PERBAIKAN UTAMA DI SINI ---
    // Ubah target listener dari 'questionTable' menjadi 'document'
    // Ini memastikan listener tetap hidup meskipun tabel dihancurkan/dibuat ulang oleh library
    document.addEventListener('click', (e) => {
        // Cek apakah target yang diklik (atau parentnya) memiliki class 'btn-edit-single'
        const btn = e.target.closest('.btn-edit-single');
        
        // Pastikan tombol ditemukan dan form edit tersedia
        if (btn && editForm && editModal) {
            const d = btn.dataset;
            
            editForm.querySelector('#edit_id').value = d.id;
            // Browser otomatis decode entitas HTML (&quot;) saat dimasukkan ke .value
            editForm.querySelector('#edit_question').value = d.question; 
            editForm.querySelector('#edit_option_a').value = d.option_a;
            editForm.querySelector('#edit_option_b').value = d.option_b;
            editForm.querySelector('#edit_option_c').value = d.option_c;
            editForm.querySelector('#edit_option_d').value = d.option_d;
            editForm.querySelector('#edit_correct_answer').value = d.correct;

            editModal.show();
        }
    });

});