import CrudHandler from './crud_handler.js';

class ManualTableHandler extends CrudHandler {
	
	async loadData() {
		const tbody = document.getElementById('rekapTableBody');
		tbody.innerHTML = '<tr><td colspan="100%" class="p-3">Sedang memuat data...</td></tr>';

		try {
			const url = this.config.baseUrl + this.config.urls.load;
			const response = await fetch(url);
			const data = await response.json();

			tbody.innerHTML = ''; 

			if (!data || data.length === 0) {
				tbody.innerHTML = '<tr><td colspan="100%" class="p-3">Belum ada data murid.</td></tr>';
				return;
			}

			data.forEach((student, index) => {
				const tr = document.createElement('tr');
				const columns = this.config.dataMapper(student, index);
				
				columns.forEach((colContent, colIndex) => {
					const td = document.createElement('td');
					if (colIndex === 1) td.style.textAlign = 'left'; 
					td.innerHTML = colContent; 
					tr.appendChild(td);
				});

				tbody.appendChild(tr);
			});

		} catch (error) {
			console.error('Error loading data:', error);
			tbody.innerHTML = '<tr><td colspan="100%" class="text-danger p-3">Gagal memuat data.</td></tr>';
		}
	}
}

// === HELPER FUNCTION: Hitung Rata-rata per Mapel ===
// Input: "Matematika::80||Matematika::100||IPA::90"
// Output: { Matematika: 90, IPA: 90 }
function calculateAverageBySubject(rawString) {
	if (!rawString) return {};

	let sums = {};
	let counts = {};

	const entries = rawString.split('||');
	entries.forEach(entry => {
        // Format string dari Model: Subject::Score
        const parts = entry.split('::');
        
        let subject = '';
        let score = 0;

        // Cek format data
        if (parts.length === 2) { 
            // Format PBL: "Matematika::80"
            subject = parts[0];
            score = parseFloat(parts[1]);
          } else if (parts.length === 3) {
            // Format Ujian: "Matematika::UTS::80" (Ini dihandle terpisah logicnya, tapi fungsi ini fleksibel)
            // abaikan tipe (UTS/UAS) disini karena UTS/UAS tidak dirata-rata gabungan
            return; 
          }

          if (subject && !isNaN(score)) {
          	if (!sums[subject]) {
          		sums[subject] = 0;
          		counts[subject] = 0;
          	}
          	sums[subject] += score;
          	counts[subject] += 1;
          }
        });

    // Hitung Average
    let averages = {};
    Object.keys(sums).forEach(sub => {
        averages[sub] = (sums[sub] / counts[sub]).toFixed(0); // Bulatkan tanpa koma
      });

    return averages;
  }

// === HELPER FUNCTION: Parse Ujian (UTS/UAS) ===
// Input: "Matematika::UTS::80||Matematika::UAS::90"
// Output: { Matematika: { UTS: 80, UAS: 90 } }
function parseExamScores(rawString) {
	if (!rawString) return {};
	let exams = {};
	rawString.split('||').forEach(entry => {
		const parts = entry.split('::');
		if (parts.length === 3) {
			const [subject, type, score] = parts;
			if (!exams[subject]) exams[subject] = {};
			exams[subject][type] = parseFloat(score).toFixed(0);
		}
	});
	return exams;
}


document.addEventListener('DOMContentLoaded', () => {
	const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
	const CURRENT_CLASS_ID = window.CURRENT_CLASS_ID;
	const EXAM_SUBJECTS = window.EXAM_SUBJECTS || [];

	const csrfConfig = {
		tokenName: window.CSRF_TOKEN_NAME,
		tokenHash: csrfEl ? csrfEl.value : ''
	};

	const config = {
		baseUrl: window.BASE_URL,
		entityName: 'Refleksi',
		modalId: 'refleksiModal',
		formId: 'refleksiForm',
		modalLabelId: 'refleksiModalLabel',
		tableId: 'rekapTable', 
		tableParentSelector: '.card-body',
		
		csrf: csrfConfig,
		urls: {
			load: `guru/laporan/get_student_recap/${CURRENT_CLASS_ID}`,
			save: `guru/laporan/save_reflection`,
			delete: null 
		},
		modalTitles: { edit: 'Buat Catatan & Feedback' },

        // === MAPPING DATA ===
        dataMapper: (student, index) => {
        	
            // 1. Proses Data dari String ke Object
            const examData = parseExamScores(student.exam_data);
            const quizData = calculateAverageBySubject(student.quiz_data);
            const obsData  = calculateAverageBySubject(student.obs_data);
            const essayData = calculateAverageBySubject(student.essay_data);

            let allScores = []; // Untuk hitung Grand Total per murid
            let columns = [];

            // 2. Loop Mapel untuk membuat 5 Kolom (UTS, UAS, Kuis, Obs, Esai)
            EXAM_SUBJECTS.forEach(subject => {
                // Helper ambil nilai atau '-'
                const valUTS = (examData[subject] && examData[subject]['UTS']) ? examData[subject]['UTS'] : '-';
                const valUAS = (examData[subject] && examData[subject]['UAS']) ? examData[subject]['UAS'] : '-';
                const valQuiz = quizData[subject] ? quizData[subject] : '-';
                const valObs  = obsData[subject] ? obsData[subject] : '-';
                const valEssay = essayData[subject] ? essayData[subject] : '-';

                // Simpan ke array hitung total jika angka
                if(valUTS !== '-') allScores.push(parseFloat(valUTS));
                if(valUAS !== '-') allScores.push(parseFloat(valUAS));
                if(valQuiz !== '-') allScores.push(parseFloat(valQuiz));
                if(valObs !== '-') allScores.push(parseFloat(valObs));
                if(valEssay !== '-') allScores.push(parseFloat(valEssay));

                columns.push(valUTS, valUAS, valQuiz, valObs, valEssay);
              });

            // 3. Hitung Grand Total (Rata-rata dari semua nilai yang ada)
            let grandTotal = 0;
            if (allScores.length > 0) {
            	const sum = allScores.reduce((a, b) => a + b, 0);
            	grandTotal = (sum / allScores.length).toFixed(0);
            }

            // 4. Tombol Aksi
            const hasReflection = (student.teacher_reflection || student.student_feedback);
            const btnClass = hasReflection ? 'btn-warning' : 'btn-primary';
            const btnText = hasReflection ? 'Edit' : 'Buat';
            
            const btn = `
            <button type="button" class="btn btn-sm ${btnClass} btn-edit"
            data-id="${student.user_id}"
            data-name="${student.student_name}"
            data-teacher_reflection="${student.teacher_reflection || ''}" 
            data-student_feedback="${student.student_feedback || ''}">
            <i class="bi bi-pencil-square"></i> ${btnText}
            </button>
            `;

            // RETURN ARRAY (Urutan sesuai TH HTML)
            // No, Nama, [Loop 5 kolom per mapel], Total, Aksi
            return [
            index + 1,
            `<span class="fw-bold">${student.student_name}</span>`,
            ...columns,
            `<span class="fw-bold text-primary">${grandTotal}</span>`,
            btn
            ];
          },

          formPopulator: (form, data) => {
          	form.querySelector('#modalUserId').value = data.id;
          	form.querySelector('#modalStudentName').value = data.name;
          	form.querySelector('[name="teacher_reflection"]').value = data.teacher_reflection || '';
          	form.querySelector('[name="student_feedback"]').value = data.student_feedback || '';
          }
        };

        new ManualTableHandler(config).init();
      });