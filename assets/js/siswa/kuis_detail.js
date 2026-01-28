import CrudHandler from '../crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {
    const QUIZ_ID = window.QUIZ_ID;
    const IS_DONE = window.IS_DONE;

    if (!QUIZ_ID) return;

    let loadUrl, dataMapperFn;

    if (IS_DONE) {
        // --- MODE REVIEW (Selesai Mengerjakan) ---
        loadUrl = `siswa/pbl_kuis/get_review/${QUIZ_ID}`;
        
        dataMapperFn = (q, i) => {
            const num = i + 1;
            const isCorrect = q.is_correct == 1;

            // Class untuk border kiri warna hijau/merah
            let cardClass = isCorrect ? 'review-correct' : 'review-wrong';
            
            // Ikon di pojok kanan atas
            let statusIcon = isCorrect 
                ? '<i class="bi bi-check-circle-fill text-success emote-badge" title="Jawaban Benar"></i>' 
                : '<i class="bi bi-x-circle-fill text-danger emote-badge" title="Jawaban Salah"></i>';

            // Helper badge opsi
            const getBadge = (optKey) => {
                // Kunci Jawaban
                if (q.correct_answer === optKey) {
                    return '<span class="badge bg-success float-end"><i class="bi bi-check-lg"></i> Kunci Jawaban</span>';
                }
                // Jawaban Siswa (Jika Salah)
                if (q.selected_option === optKey && !isCorrect) {
                    return '<span class="badge bg-danger float-end"><i class="bi bi-x-lg"></i> Jawabanmu</span>';
                }
                // Jawaban Siswa (Jika Benar - opsional, biasanya overlap dengan kunci)
                if (q.selected_option === optKey && isCorrect) {
                     return '<span class="badge bg-success float-end"><i class="bi bi-check-lg"></i> Jawabanmu</span>';
                }
                return '';
            };

            // Style list item
            const getListClass = (optKey) => {
                if (q.correct_answer === optKey) return 'list-group-item-success border-success text-success fw-bold';
                if (q.selected_option === optKey && !isCorrect) return 'list-group-item-danger border-danger text-danger';
                return 'bg-light border-0';
            };

            const html = `
            <div class="question-card ${cardClass}">
                ${statusIcon}
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-secondary me-2">No. ${num}</span>
                </div>
                <p class="lead fw-normal text-dark mb-4" style="font-size: 1.1rem;">${q.question_text}</p>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item p-3 ${getListClass('A')}">
                        <span class="fw-bold me-2">A.</span> ${q.option_a} ${getBadge('A')}
                    </li>
                    <li class="list-group-item p-3 ${getListClass('B')}">
                        <span class="fw-bold me-2">B.</span> ${q.option_b} ${getBadge('B')}
                    </li>
                    <li class="list-group-item p-3 ${getListClass('C')}">
                        <span class="fw-bold me-2">C.</span> ${q.option_c} ${getBadge('C')}
                    </li>
                    <li class="list-group-item p-3 ${getListClass('D')}">
                        <span class="fw-bold me-2">D.</span> ${q.option_d} ${getBadge('D')}
                    </li>
                </ul>
                
                ${!isCorrect && !q.selected_option ? '<div class="mt-3 text-warning small"><i class="bi bi-exclamation-triangle"></i> Anda tidak menjawab soal ini.</div>' : ''}
            </div>
            `;
            return [html];
        };

    } else {
        // --- MODE MENGERJAKAN (Belum Selesai) ---
        loadUrl = `siswa/pbl_kuis/get_questions/${QUIZ_ID}`;
        
        dataMapperFn = (q, i) => {
            const num = i + 1;
            
            // Opsi Jawaban dalam loop
            const optionsHtml = ['A', 'B', 'C', 'D'].map(opt => `
                <div class="col-md-6 mb-2">
                    <div class="form-check p-0">
                        <input class="form-check-input d-none" type="radio" name="answers[${q.question_id}]" id="q${q.question_id}_${opt}" value="${opt}">
                        <label class="form-check-label w-100 p-3 border option-hover d-flex align-items-center" for="q${q.question_id}_${opt}">
                            <span class="badge bg-light text-dark border me-3">${opt}</span>
                            <span>${q['option_'+opt.toLowerCase()]}</span>
                        </label>
                    </div>
                </div>
            `).join('');

            const html = `
            <div class="question-card">
                <h5 class="fw-bold text-secondary mb-3">Soal No. ${num}</h5>
                <p class="mb-4 fs-5 text-dark" style="line-height: 1.6;">${q.question_text}</p>
                
                <div class="row">
                    ${optionsHtml}
                </div>
            </div>
            `;
            return [html];
        };
    }

    // --- Inisialisasi CrudHandler (LOGIKA TETAP) ---
    const csrfEl = document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`);
    const config = {
        baseUrl: window.BASE_URL,
        entityName: 'Soal',
        readOnly: true, 
        tableId: 'questionsTable',
        tableParentSelector: '#questionsTableContainer',
        csrf: {
            tokenName: window.CSRF_TOKEN_NAME,
            tokenHash: csrfEl ? csrfEl.value : ''
        },
        urls: {
            load: loadUrl,
            save: '', delete: '' 
        },
        dataMapper: dataMapperFn
    };

    const handler = new CrudHandler(config);
    handler.init();

    // --- Logic Submit (LOGIKA TETAP) ---
    const form = document.getElementById('quizSubmissionForm');
    if (form && !IS_DONE) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Cek apakah ada radio button yang belum dipilih (Optional UI enhancement)
            // const totalQuestions = document.querySelectorAll('.question-card').length;
            // const answered = document.querySelectorAll('input[type="radio"]:checked').length;
            
            Swal.fire({
                title: 'Kirim Jawaban?',
                text: "Apakah Anda yakin ingin menyelesaikan kuis ini? Jawaban tidak dapat diubah setelah dikirim.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#858796',
                confirmButtonText: '<i class="bi bi-send"></i> Ya, Kirim!',
                cancelButtonText: 'Periksa Lagi'
            }).then((result) => {
                if (result.isConfirmed) submitQuizData();
            });
        });
    }

    function submitQuizData() {
        const formData = new FormData(form);
        formData.append('quiz_id', QUIZ_ID);

        // Tampilkan loading
        Swal.fire({
            title: 'Mengirim Jawaban...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`${window.BASE_URL}siswa/pbl_kuis/submit_quiz`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.csrf_hash) {
                document.querySelectorAll(`input[name="${window.CSRF_TOKEN_NAME}"]`).forEach(el => el.value = data.csrf_hash);
            }
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Selesai!',
                    text: `Nilai Anda: ${data.score || 0}`,
                    allowOutsideClick: false,
                    confirmButtonText: 'Lihat Hasil'
                }).then(() => {
                    window.location.reload(); 
                });
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        })
        .catch(err => Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error'));
    }
});