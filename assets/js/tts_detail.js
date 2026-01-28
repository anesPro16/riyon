import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {
  const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
  const ttsIdEl = document.getElementById('ttsIdHidden');
  const CURRENT_TTS_ID = ttsIdEl ? ttsIdEl.value : null;

  if (!CURRENT_TTS_ID) return;

  const GRID_SIZE = parseInt(document.getElementById('ttsGridSize')?.value || 15);
  const ROWS = GRID_SIZE;
  const COLS = GRID_SIZE;
  const BLOCKS = window.GRID_BLOCKS || [];

  const csrfConfig = {
    tokenName: window.CSRF_TOKEN_NAME,
    tokenHash: csrfEl ? csrfEl.value : ''
  };

  const gridContainer = document.getElementById('ttsGridPreview');
  const gridCells = [];

  // Buat grid sesuai ukuran
  gridContainer.innerHTML = '';
  gridContainer.style.gridTemplateColumns = `repeat(${COLS}, 30px)`;

  for (let y = 1; y <= ROWS; y++) {
    for (let x = 1; x <= COLS; x++) {
      const cell = document.createElement('div');
      cell.classList.add('tts-cell');
      cell.dataset.x = x;
      cell.dataset.y = y;
      cell.style.width = '30px';
      cell.style.height = '30px';
      cell.style.border = '1px solid #ccc';
      cell.style.position = 'relative';
      cell.style.userSelect = 'none';
      if (BLOCKS.some(b => b.x === x && b.y === y)) {
        cell.style.backgroundColor = '#222';
      }
      gridContainer.appendChild(cell);
      gridCells.push(cell);
    }
  }

  // Klik cell → isi koordinat form
  gridContainer.addEventListener('click', (e) => {
    const cell = e.target.closest('.tts-cell');
    if (!cell) return;
    const form = document.querySelector('#questionForm');
    if (!form) return;
    const x = cell.dataset.x;
    const y = cell.dataset.y;
    form.querySelector('[name="start_x"]').value = x;
    form.querySelector('[name="start_y"]').value = y;
    gridCells.forEach(c => c.classList.remove('selected-cell'));
    cell.classList.add('selected-cell');
  });

  // Helper ambil cell
  function getCell(x, y) {
    return gridCells.find(c => c.dataset.x == x && c.dataset.y == y);
  }

  // [PERBAIKAN] Reset grid menggunakan innerHTML
  function clearGrid() {
    gridCells.forEach(c => {
      // Cara terbersih untuk menghapus semua elemen anak (label dan span)
      c.innerHTML = ''; 
      
      // Reset warna latar belakang (jika bukan 'block')
      if (!BLOCKS.some(b => b.x == c.dataset.x && b.y == c.dataset.y)) {
        c.style.backgroundColor = '#fff';
      }
    });
  }

  // [PERBAIKAN] Render grid dalam dua pass (Numbers, lalu Letters)
  function renderGrid(data) {
    clearGrid(); // Panggil clearGrid yang sudah diperbaiki

    // PASS 1: Render semua nomor terlebih dahulu
    data.forEach(item => {
      const sx = parseInt(item.start_x);
      const sy = parseInt(item.start_y);
      const dir = item.direction;
      const num = item.number;

      const first = getCell(sx, sy);
      if (first) {
        // Cek apakah label dengan nomor & arah ini sudah ada
        // Ini untuk mengatasi bug jika ada 2 soal "Nomor 2 Menurun"
        const existingLabel = first.querySelector(`.num-label[data-num="${num}"][data-dir="${dir}"]`);
        
        if (!existingLabel) {
          const label = document.createElement('div');
          label.classList.add('num-label');
          label.dataset.num = num; // Tambahkan data untuk cek duplikat
          label.dataset.dir = dir;

          if (dir === 'down') {
            label.classList.add('down');
          }
          label.innerText = num;
          first.appendChild(label);
        }
      }
    });

    // PASS 2: Render semua huruf
    data.forEach(item => {
      const sx = parseInt(item.start_x);
      const sy = parseInt(item.start_y);
      const ans = item.answer || '';
      const dir = item.direction;
      
      for (let i = 0; i < ans.length; i++) {
        let cx = sx;
        let cy = sy;
        if (dir === 'across') cx += i;
        else cy += i;
        
        const cell = getCell(cx, cy);
        if (cell && !cell.style.backgroundColor.includes('black')) {
          
          // --- [LOGIKA INTI PERBAIKAN] ---
          // 1. Cari span huruf.
          let letterSpan = cell.querySelector('.letter-span');
          
          // 2. Jika tidak ada, buat.
          if (!letterSpan) {
            letterSpan = document.createElement('span');
            letterSpan.classList.add('letter-span');
            cell.appendChild(letterSpan);
          }
          
          // 3. Set/Ganti hurufnya.
          // Ini akan menimpa 'K' (MAKAN) dengan 'L' (MALAM) jika datanya konflik.
          // Ini adalah perilaku yang diinginkan.
          letterSpan.innerText = ans[i]; 
          // --- [AKHIR PERBAIKAN] ---

          cell.style.backgroundColor = '#eaf2ff';
        }
      }
    });
  }

  // Konfigurasi CRUD Pertanyaan
  const config = {
    baseUrl: window.BASE_URL,
    entityName: 'Pertanyaan TTS',
    modalId: 'questionModal',
    formId: 'questionForm',
    modalLabelId: 'questionModalLabel',
    tableId: 'questionTable',
    btnAddId: 'btnAddQuestion',
    csrf: csrfConfig,
    urls: {
      load: `guru/pbl_tts/get_questions/${CURRENT_TTS_ID}`,
      save: `guru/pbl_tts/save_question`,
      delete: `guru/pbl_tts/delete_question`
    },
    deleteMethod: 'POST',
    modalTitles: { add: 'Tambah Pertanyaan', edit: 'Edit Pertanyaan' },
    deleteNameField: 'question',
    dataMapper: (q, i) => [
      i + 1,
      q.number,
      q.direction === 'across' ? 'Mendatar' : 'Menurun',
      q.question,
      q.answer,
      `
        <button class="btn btn-sm btn-warning btn-edit"
          data-id="${q.id}" data-number="${q.number}"
          data-direction="${q.direction}" data-question="${q.question}"
          data-answer="${q.answer}" data-start_x="${q.start_x}" data-start_y="${q.start_y}">
          <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-sm btn-danger btn-delete"
          data-id="${q.id}" data-question="${q.question}">
          <i class="bi bi-trash"></i>
        </button>`
    ],
    formPopulator: (form, data) => {
      form.querySelector('#questionId').value = data.id;
      form.querySelector('[name="number"]').value = data.number;
      form.querySelector('[name="direction"]').value = data.direction;
      form.querySelector('[name="question"]').value = data.question;
      form.querySelector('[name="answer"]').value = data.answer;
      form.querySelector('[name="start_x"]').value = data.start_x;
      form.querySelector('[name="start_y"]').value = data.start_y;
    },
    onAdd: (form) => {
        form.reset();
        const selected = gridContainer.querySelector('.selected-cell');
        if (selected) {
            form.querySelector('[name="start_x"]').value = selected.dataset.x;
            form.querySelector('[name="start_y"]').value = selected.dataset.y;
        }
    },
    onDataLoaded: renderGrid // Memanggil renderGrid saat data dimuat
  };

  const handler = new CrudHandler(config);
  handler.init();
});