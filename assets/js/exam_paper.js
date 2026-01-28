document.addEventListener('DOMContentLoaded', () => {
    
    const CSRF_NAME = window.CSRF_TOKEN_NAME;
    let csrfHash = document.querySelector(`input[name="${CSRF_NAME}"]`).value;
    const ATTEMPT_ID = document.querySelector('input[name="attempt_id"]').value;
    const TOTAL_Q = window.TOTAL_QUESTIONS;
    
    let currentIndex = 0;

    // --- 1. TIMER COUNTDOWN ---
    const timerDisplay = document.getElementById('timerDisplay');
    const endTime = new Date(window.END_TIME).getTime();

    const updateTimer = () => {
        const now = new Date().getTime(); // Sebaiknya sinkron dengan waktu server via AJAX jika production serius
        const distance = endTime - now;

        if (distance < 0) {
            clearInterval(timerInterval);
            timerDisplay.innerHTML = "WAKTU HABIS";
            timerDisplay.classList.add('bg-danger', 'text-white');
            finishExam(true); // Auto submit force
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        timerDisplay.innerHTML = 
            (hours < 10 ? "0" + hours : hours) + ":" +
            (minutes < 10 ? "0" + minutes : minutes) + ":" +
            (seconds < 10 ? "0" + seconds : seconds);
            
        // Peringatan 5 menit terakhir
        if(distance < 300000) timerDisplay.classList.add('text-danger');
    };

    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer(); // Run immediately

    // --- 2. NAVIGASI SOAL ---
    const showQuestion = (index) => {
        // Hide all
        document.querySelectorAll('.question-container').forEach(el => el.classList.add('d-none'));
        // Show target
        const target = document.getElementById(`q_${index}`);
        target.classList.remove('d-none');
        
        currentIndex = index;
        renderNavigation(); // Update warna tombol navigasi
    };

    // Tombol Next & Prev
    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentIndex < TOTAL_Q - 1) showQuestion(currentIndex + 1);
        });
    });
    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentIndex > 0) showQuestion(currentIndex - 1);
        });
    });

    // --- 3. RENDER NOMOR NAVIGASI ---
    const renderNavigation = () => {
        const desktopContainer = document.getElementById('navContainerDesktop');
        const mobileContainer = document.getElementById('navContainerMobile');
        
        let html = '';
        for(let i=0; i<TOTAL_Q; i++) {
            // Cek apakah soal ini sudah dijawab
            const qContainer = document.getElementById(`q_${i}`);
            const isAnswered = qContainer.querySelector('input:checked') !== null;
            
            let classes = 'nav-btn-outline';
            if (i === currentIndex) classes += ' active';
            if (isAnswered) classes += ' answered';

            html += `<div class="${classes}" onclick="jumpTo(${i})">${i + 1}</div>`;
        }

        desktopContainer.innerHTML = html;
        mobileContainer.innerHTML = html;
        
        // Expose function to global scope for onclick attribute
        window.jumpTo = (idx) => showQuestion(idx);
    };

    // Initial Render
    renderNavigation();

    // --- 4. AUTO SAVE JAWABAN ---
    document.querySelectorAll('.answer-radio').forEach(radio => {
        radio.addEventListener('change', async (e) => {
            const questionId = e.target.closest('.question-container').dataset.qid;
            const answer = e.target.value;

            // Update UI navigasi (langsung hijau)
            renderNavigation();

            // Kirim ke server
            const formData = new FormData();
            formData.append('attempt_id', ATTEMPT_ID);
            formData.append('question_id', questionId);
            formData.append('answer', answer);
            formData.append(CSRF_NAME, csrfHash);

            try {
                // Gunakan fetch tanpa await blocking agar user tidak lag
                fetch(`${window.BASE_URL}exam/save_answer`, {
                    method: 'POST',
                    body: formData
                });
                // Kita tidak update CSRF hash di sini karena fetch concurrent bisa bikin mismatch.
                // CodeIgniter 3 default CSRF regenerate mungkin perlu diset FALSE di config 
                // atau gunakan teknik khusus. Untuk PBL sekolah sederhana, abaikan regenerate per request.
            } catch (error) {
                console.error('Gagal menyimpan', error);
            }
        });
    });

    // --- 5. FINISH EXAM ---
    const finishExam = (force = false) => {
        const submitLogic = () => {
            const formData = new FormData();
            formData.append('attempt_id', ATTEMPT_ID);
            formData.append(CSRF_NAME, csrfHash);

            fetch(`${window.BASE_URL}exam/finish_exam`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    window.location.href = data.redirect;
                }
            });
        };

        if (force) {
            Swal.fire({
                title: 'Waktu Habis!',
                text: 'Jawaban Anda akan dikirim otomatis.',
                icon: 'warning',
                timer: 3000,
                showConfirmButton: false
            }).then(() => submitLogic());
        } else {
            // Hitung soal terjawab
            const answered = document.querySelectorAll('.answer-radio:checked').length;
            const unanswered = TOTAL_Q - answered;

            Swal.fire({
                title: 'Kumpulkan Ujian?',
                html: `Anda telah menjawab <b>${answered}</b> dari <b>${TOTAL_Q}</b> soal.<br>
                       ${unanswered > 0 ? '<span class="text-danger">Masih ada soal kosong!</span>' : ''}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Kumpulkan!',
                cancelButtonText: 'Periksa Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitLogic();
                }
            });
        }
    };

    document.querySelector('.btn-finish').addEventListener('click', () => finishExam(false));

});