import CrudHandler from '../crud_handler.js';

/**
 * Class Custom untuk Menangani Tampilan Laporan Siswa
 * Mengubah data 1 Siswa menjadi Baris-baris Mata Pelajaran
 */
 class StudentReportHandler extends CrudHandler {

 	async loadData() {
 		const tbody = document.getElementById('nilaiTableBody');
 		const tfoot = document.getElementById('nilaiTableFoot');
 		const teacherRefEl = document.getElementById('viewTeacherReflection');
 		const studentFeedEl = document.getElementById('viewStudentFeedback');

 		tbody.innerHTML = '<tr><td colspan="7" class="p-3">Sedang memuat data...</td></tr>';

 		try {
 			const url = this.config.baseUrl + this.config.urls.load;
 			const response = await fetch(url);
            const data = await response.json(); // Data ini berupa Object (1 baris DB), bukan Array

            tbody.innerHTML = ''; 
            tfoot.innerHTML = '';

            if (!data) {
            	tbody.innerHTML = '<tr><td colspan="7" class="text-danger">Gagal memuat data.</td></tr>';
            	return;
            }

            // 1. Tampilkan Refleksi Guru (jika ada)
            if(teacherRefEl) teacherRefEl.textContent = data.teacher_reflection || '- Belum ada catatan -';
            if(studentFeedEl) studentFeedEl.textContent = data.student_feedback || '- Belum ada feedback -';

            // 2. Parsing Data String dari Database
            const examScores = this.parseExamScores(data.exam_data);
            const quizScores = this.calculateAverageBySubject(data.quiz_data);
            const obsScores  = this.calculateAverageBySubject(data.obs_data);
            const essayScores = this.calculateAverageBySubject(data.essay_data);

            // Variable untuk menghitung Grand Total Rata-rata
            let grandTotalSum = 0;
            let grandTotalCount = 0;

            // 3. Loop Mata Pelajaran (EXAM_SUBJECTS dari PHP) -> Menjadi Baris Tabel
            const subjects = window.EXAM_SUBJECTS || [];
            
            subjects.forEach(subject => {
            	const tr = document.createElement('tr');

                // Ambil nilai per komponen
                const valUTS   = (examScores[subject] && examScores[subject]['UTS']) ? parseFloat(examScores[subject]['UTS']) : 0;
                const valUAS   = (examScores[subject] && examScores[subject]['UAS']) ? parseFloat(examScores[subject]['UAS']) : 0;
                const valQuiz  = quizScores[subject] ? parseFloat(quizScores[subject]) : 0;
                const valObs   = obsScores[subject] ? parseFloat(obsScores[subject]) : 0;
                const valEssay = essayScores[subject] ? parseFloat(essayScores[subject]) : 0;

                // Array nilai valid (yang tidak 0/kosong) untuk hitung rata-rata baris
                // Asumsi: Jika nilai 0, tetap dihitung sebagai pembagi jika ingin ketat. 
                // Disini saya anggap jika 0 berarti belum dinilai, jadi tidak merusak rata-rata.
                // Jika ingin 0 tetap dihitung, hapus filter > 0.
                let components = [valUTS, valUAS, valQuiz, valObs, valEssay];
                let validComponents = components.filter(v => v > 0);
                
                let rowAvg = 0;
                if (validComponents.length > 0) {
                	let sum = validComponents.reduce((a, b) => a + b, 0);
                	rowAvg = (sum / validComponents.length);

                    // Masukkan ke Grand Total
                    grandTotalSum += rowAvg;
                    grandTotalCount++;
                  }

                // Helper display: Jika 0 tampilkan "-"
                const disp = (num) => num > 0 ? num : '-';
                const dispAvg = (num) => num > 0 ? num.toFixed(0) : '-'; // Bulatkan rata-rata

                tr.innerHTML = `
                <td class="text-left fw-bold">${subject}</td>
                <td>${disp(valUTS)}</td>
                <td>${disp(valUAS)}</td>
                <td>${disp(valQuiz)}</td>
                <td>${disp(valObs)}</td>
                <td>${disp(valEssay)}</td>
                <td class="fw-bold bg-light">${dispAvg(rowAvg)}</td>
                `;
                tbody.appendChild(tr);
              });

            // 4. Baris Footer (Total Rata-rata Keseluruhan)
            let finalAvg = 0;
            if (grandTotalCount > 0) {
            	finalAvg = (grandTotalSum / grandTotalCount).toFixed(0);
            }

            tfoot.innerHTML = `
            <tr class="bg-total">
            <td colspan="6" class="text-end">Rata-Rata Total</td>
            <td>${finalAvg}</td>
            </tr>
            `;

          } catch (error) {
          	console.error('Error loading data:', error);
          	tbody.innerHTML = '<tr><td colspan="7" class="text-danger p-3">Terjadi kesalahan sistem.</td></tr>';
          }
        }

    // --- Helper Parsing (Sama seperti Teacher View) ---
    
    calculateAverageBySubject(rawString) {
    	if (!rawString) return {};
    	let sums = {};
    	let counts = {};
    	rawString.split('||').forEach(entry => {
    		const parts = entry.split('::');
    		if (parts.length === 2) {
    			const [subj, score] = parts;
    			const val = parseFloat(score);
    			if (!isNaN(val)) {
    				if (!sums[subj]) { sums[subj] = 0; counts[subj] = 0; }
    				sums[subj] += val;
    				counts[subj]++;
    			}
    		}
    	});
    	let avgs = {};
        Object.keys(sums).forEach(k => avgs[k] = (sums[k] / counts[k]).toFixed(2)); // return string 2 decimal
        return avgs;
      }

      parseExamScores(rawString) {
      	if (!rawString) return {};
      	let exams = {};
      	rawString.split('||').forEach(entry => {
      		const parts = entry.split('::');
      		if (parts.length === 3) {
      			const [subj, type, score] = parts;
      			if (!exams[subj]) exams[subj] = {};
      			exams[subj][type] = score;
      		}
      	});
      	return exams;
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
    	const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
    	const CURRENT_CLASS_ID = window.CURRENT_CLASS_ID;

    	const csrfConfig = {
    		tokenName: window.CSRF_TOKEN_NAME,
    		tokenHash: csrfEl ? csrfEl.value : ''
    	};

    	const config = {
    		baseUrl: window.BASE_URL,
        // Config ini dummy karena kita override loadData sepenuhnya
        entityName: 'Laporan',
        modalId: null, formId: null, modalLabelId: null, tableId: nilaiTable,
        
        csrf: csrfConfig,
        urls: {
        	load: `siswa/laporan/get_my_recap/${CURRENT_CLASS_ID}`,
        	save: null, delete: null 
        },
        
        // Data Mapper tidak dipakai karena kita override loadData
        dataMapper: () => [], 
        formPopulator: () => {}
      };

      new StudentReportHandler(config).init();
    });