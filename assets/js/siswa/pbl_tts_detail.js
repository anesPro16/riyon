import CrudHandler from '../crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {
    const TTS_ID = window.TTS_ID;
    const GRID_SIZE = window.GRID_SIZE;
    const CELL_SIZE = 30; // Pixel

    if (!TTS_ID) return;

    // State
    let questions = [];
    
    // Elements
    const gridContainer = document.getElementById('ttsGridContainer');
    const listAcross = document.getElementById('listAcross');
    const listDown = document.getElementById('listDown');
    const btnSubmit = document.getElementById('btnSubmitTTS');

    // 1. Fetch Data Soal
    fetch(`${window.BASE_URL}siswa/pbl_tts/get_game_data/${TTS_ID}`)
        .then(res => res.json())
        .then(data => {
            questions = data;
            initGame();
        })
        .catch(err => console.error(err));

    // 2. Inisialisasi Game
    function initGame() {
        renderEmptyGrid();
        mapQuestionsToGrid();
        renderClues();
        setupInputNavigation();
    }

    // 3. Render Grid Kosong (Hitam)
    function renderEmptyGrid() {
        gridContainer.style.gridTemplateColumns = `repeat(${GRID_SIZE}, ${CELL_SIZE}px)`;
        gridContainer.style.width = 'fit-content';

        for (let y = 1; y <= GRID_SIZE; y++) {
            for (let x = 1; x <= GRID_SIZE; x++) {
                const cell = document.createElement('div');
                cell.classList.add('tts-cell');
                cell.dataset.x = x;
                cell.dataset.y = y;
                // Default id untuk cell, nanti bisa ditimpa jika aktif
                cell.id = `cell_${x}_${y}`;
                gridContainer.appendChild(cell);
            }
        }
    }

    // 4. Petakan Soal ke Grid (Jadi Putih + Input)
    function mapQuestionsToGrid() {
        questions.forEach(q => {
            const len = parseInt(q.ans_length);
            const startX = parseInt(q.start_x);
            const startY = parseInt(q.start_y);
            const isAcross = q.direction === 'across';
            const firstChar = q.first_char; // Ambil huruf pertama

            // --- [PERBAIKAN LOGIKA NOMOR] ---
            const startCell = document.querySelector(`.tts-cell[data-x="${startX}"][data-y="${startY}"]`);
            if (startCell) {
                // Cek apakah label untuk arah ini sudah ada (mencegah duplikasi jika refresh data)
                const existingLabel = startCell.querySelector(`.num-label[data-dir="${q.direction}"]`);
                
                if (!existingLabel) {
                    const numSpan = document.createElement('span');
                    numSpan.className = 'num-label'; // Default: Kiri (Mendatar)
                    numSpan.innerText = q.number;
                    numSpan.dataset.dir = q.direction; // Penanda arah

                    // Jika Menurun, tambahkan class 'down' agar pindah ke Kanan
                    if (q.direction === 'down') {
                        numSpan.classList.add('down');
                    }

                    startCell.appendChild(numSpan);
                }
            }
            // --------------------------------

            // Loop panjang jawaban untuk mengaktifkan cell
            for (let i = 0; i < len; i++) {
                let cx = startX + (isAcross ? i : 0);
                let cy = startY + (isAcross ? 0 : i);

                const cell = document.querySelector(`.tts-cell[data-x="${cx}"][data-y="${cy}"]`);
                if (cell) {
                    // Cek apakah input sudah ada (untuk persimpangan)
                    let input = cell.querySelector('input');

                    if (!cell.classList.contains('active-cell')) {
                        cell.classList.add('active-cell');
                        cell.style.backgroundColor = '#fff';
                        
                        // Buat Input jika belum ada
                        input = document.createElement('input');
                        input.type = 'text';
                        input.maxLength = 1;
                        input.dataset.x = cx;
                        input.dataset.y = cy;
                        cell.appendChild(input);
                    }
                    
                    // Tampilkan Huruf Pertama (Hint)
                    if (i === 0 && input) {
                        input.value = firstChar;
                        input.classList.add('hint-char');
                        // input.readOnly = true; // Opsional
                    }
                    
                    // Tambahkan metadata soal ke cell
                    const existingQIds = cell.dataset.questionIds ? cell.dataset.questionIds.split(',') : [];
                    if (!existingQIds.includes(q.id)) {
                        existingQIds.push(q.id);
                        cell.dataset.questionIds = existingQIds.join(',');
                    }
                }
            }
        });
    }

    // 5. Render Daftar Pertanyaan di Kanan
    function renderClues() {
        questions.forEach(q => {
            const item = document.createElement('div');
            item.className = 'clue-item';
            item.dataset.id = q.id;
            item.innerHTML = `<strong>${q.number}.</strong> ${q.question}`;
            
            // Event: Klik clue -> Focus ke cell pertama
            item.addEventListener('click', () => {
                const input = document.querySelector(`.tts-cell[data-x="${q.start_x}"][data-y="${q.start_y}"] input`);
                if (input) input.focus();
                
                highlightClue(q.id);
            });

            if (q.direction === 'across') {
                listAcross.appendChild(item);
            } else {
                listDown.appendChild(item);
            }
        });
    }

    function highlightClue(questionId) {
        document.querySelectorAll('.clue-item').forEach(el => el.classList.remove('active-clue'));
        if (questionId) {
            const clueEl = document.querySelector(`.clue-item[data-id="${questionId}"]`);
            if (clueEl) {
                clueEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                clueEl.classList.add('active-clue');
            }
        }
    }

    // 6. Navigasi Keyboard
    function setupInputNavigation() {
        const inputs = document.querySelectorAll('.tts-cell input');
        
        inputs.forEach(input => {
            input.addEventListener('focus', (e) => {
                const cell = e.target.closest('.tts-cell');
                const qIds = cell.dataset.questionIds.split(',');
                if (qIds.length > 0) highlightClue(qIds[0]);
            });

            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1) {
                    const next = Array.from(inputs).indexOf(e.target) + 1;
                    if (next < inputs.length) {
                        inputs[next].focus();
                    }
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value === '') {
                    const prev = Array.from(inputs).indexOf(e.target) - 1;
                    if (prev >= 0) {
                        inputs[prev].focus();
                    }
                }
            });
        });
    }

    // 7. Submit Jawaban
    btnSubmit.addEventListener('click', () => {
        Swal.fire({
            title: 'Kirim Jawaban?',
            text: "Pastikan semua kotak terisi.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim!'
        }).then((result) => {
            if (result.isConfirmed) {
                processSubmission();
            }
        });
    });

    function processSubmission() {
        const answers = {};
        
        questions.forEach(q => {
            const len = parseInt(q.ans_length);
            const startX = parseInt(q.start_x);
            const startY = parseInt(q.start_y);
            const isAcross = q.direction === 'across';
            
            let word = '';
            for (let i = 0; i < len; i++) {
                let cx = startX + (isAcross ? i : 0);
                let cy = startY + (isAcross ? 0 : i);
                
                const input = document.querySelector(`.tts-cell[data-x="${cx}"][data-y="${cy}"] input`);
                if (input) {
                    word += input.value;
                }
            }
            answers[q.id] = word;
        });

        const form = document.getElementById('ttsSubmissionForm');
        const formData = new FormData(form); 
        
        for (const [qid, val] of Object.entries(answers)) {
            formData.append(`answers[${qid}]`, val);
        }

        fetch(`${window.BASE_URL}siswa/pbl_tts/submit_tts`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.csrf_hash) {
                document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`).value = data.csrf_hash;
            }
            
            if (data.status === 'success') {
                Swal.fire('Selesai!', `Nilai Anda: ${data.score}`, 'success')
                .then(() => window.location.reload());
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Gagal mengirim data.', 'error');
        });
    }
});