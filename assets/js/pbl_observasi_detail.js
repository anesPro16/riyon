import CrudHandler from './crud_handler.js';

document.addEventListener('DOMContentLoaded', () => {

	const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
	const SLOT_ID = window.SLOT_ID;

	if (!SLOT_ID) return;

	const csrfConfig = {
		tokenName: window.CSRF_TOKEN_NAME,
		tokenHash: csrfEl ? csrfEl.value : ''
	};

    // --- KONFIGURASI TABEL (Uploads + Nilai) ---
    const uploadConfig = {
    	baseUrl: window.BASE_URL,
    	entityName: 'Data',
    	readOnly: false, 
    	
    	tableId: 'uploadsTable',
        tableParentSelector: '#observasiTableContainer', // Event delegation parent
        
        csrf: csrfConfig,
        urls: {
        	load: `guru/pbl_observasi/get_uploads/${SLOT_ID}`,
            delete: (id) => `guru/pbl_observasi/delete_upload/${id}` // Delete File
          },
          deleteMethod: 'POST', 
          deleteNameField: 'original_name', 

        // Mapper
        dataMapper: (item, i) => {
        	const uploadDate = new Date(item.created_at).toLocaleString('id-ID', {
        		dateStyle: 'medium', timeStyle: 'short'
        	});
        	
        	const fileUrl = `${window.BASE_URL}file/observasi/${item.file_name}`;
        	
            // Logika Tombol Nilai
            let gradeBtnHtml = '';
            let gradeStatusHtml = '';
            let deleteGradeBtn = '';

            // Jika nilai sudah ada (score tidak null)
            if (item.score !== null && item.score !== undefined) {
                // Tampilan Status Nilai
                let badgeColor = item.score >= 75 ? 'bg-success' : (item.score >= 60 ? 'bg-warning text-dark' : 'bg-danger');
                gradeStatusHtml = `
                <span class="badge ${badgeColor}" style="font-size: 0.9em;">${item.score}</span>
                ${item.feedback ? `<br><small class="text-muted"><i class="bi b-chat-left-text"></i> ${item.feedback.substring(0, 20)}...</small>` : ''}
                `;

                // Tombol Edit Nilai
                gradeBtnHtml = `
                <button class="btn btn-sm btn-warning btn-grade" 
                title="Edit Nilai"
                data-user_id="${item.user_id}" 
                data-student_name="${item.student_name}"
                data-grade_id="${item.grade_id}"
                data-score="${item.score}"
                data-feedback="${item.feedback || ''}">
                <i class="bi bi-pencil-square"></i> Edit Nilai
                </button>
                `;

                // Tombol Hapus Nilai (Hanya muncul jika sudah dinilai)
                /*deleteGradeBtn = `
                <button class="btn btn-sm btn-outline-danger btn-delete-grade" 
                title="Hapus Nilai"
                data-id="${item.grade_id}" 
                data-student_name="${item.student_name}">
                Hapus Nilai
                </button>
                `;*/

              } else {
                // Belum Dinilai
                gradeStatusHtml = `<span class="badge bg-secondary">0</span>`;
                
                // Tombol Beri Nilai
                gradeBtnHtml = `
                <button class="btn btn-sm btn-primary btn-grade" 
                title="Beri Nilai"
                data-user_id="${item.user_id}" 
                data-student_name="${item.student_name}"
                data-grade_id=""
                data-score=""
                data-feedback="">
                Beri Nilai
                </button>
                `;
              }

              return [
              i + 1,
              `<div>
              <strong>${item.student_name}</strong><br>
              <small class="text-muted"><i class="bi bi-file-earmark"></i> ${item.original_name}</small><br>
              <small class="text-muted">${item.description || ''}</small>
              </div>`,
              `<a href="${fileUrl}" target="_blank" class="btn btn-sm btn-info text-dark"><i class="bi bi-download"></i> Unduh</a>`,
              
                // Kolom Status Nilai
                gradeStatusHtml,

                // Kolom Aksi (Nilai, Hapus Nilai, Hapus File)
                `<div class="d-flex justify-content-center gap-2">
                ${gradeBtnHtml}
                ${deleteGradeBtn}
                <button class="btn btn-sm btn-danger btn-delete" 
                title="Hapus File"
                data-id="${item.id}" 
                data-original_name="${item.student_name} - ${item.original_name}">
                <i class="bi bi-trash"></i> Hapus File
                </button>
                </div>`
                ];
              },
              formPopulator: () => {}, 
              onAdd: () => {}
            };

            const handler = new CrudHandler(uploadConfig);
            handler.init();


    // ============================================================
    // LOGIKA MANUAL UNTUK MODAL PENILAIAN
    // ============================================================
    
    const gradeModalEl = document.getElementById('gradeModal');
    const gradeModal = new bootstrap.Modal(gradeModalEl);
    const gradeForm = document.getElementById('gradeForm');

    // 1. Event Listener Tombol "Beri Nilai" / "Edit Nilai"
    document.querySelector('#observasiTableContainer').addEventListener('click', (e) => {
    	const btn = e.target.closest('.btn-grade');
    	if (btn) {
            // Isi Form Modal
            gradeForm.reset();
            document.getElementById('gradeId').value = btn.dataset.grade_id || '';
            document.getElementById('userIdInput').value = btn.dataset.user_id;
            document.getElementById('studentNameDisplay').value = btn.dataset.student_name;
            document.getElementById('scoreInput').value = btn.dataset.score || '';
            document.getElementById('feedbackInput').value = btn.dataset.feedback || '';
            
            gradeModal.show();
          }
        });

    // 2. Event Listener Submit Form Nilai
    gradeForm.addEventListener('submit', async (e) => {
    	e.preventDefault();
    	
    	const formData = new FormData(gradeForm);
    	
    	try {
    		const response = await fetch(`${window.BASE_URL}guru/pbl_observasi/save_grade`, {
    			method: 'POST',
    			body: formData
    		});
    		const result = await response.json();

            // Update CSRF
            if (result.csrf_hash) {
            	document.querySelectorAll(`input[name="${window.CSRF_TOKEN_NAME}"]`).forEach(el => el.value = result.csrf_hash);
                csrfConfig.tokenHash = result.csrf_hash; // Update config handler juga
              }

              if (result.status === 'success') {
              	gradeModal.hide();
              	Swal.fire({ icon: 'success', title: 'Berhasil', text: result.message, timer: 1500, showConfirmButton: false });
                handler.loadData(); // Reload tabel utama
              } else {
              	Swal.fire('Gagal', result.message, 'error');
              }
            } catch (error) {
            	console.error(error);
            	Swal.fire('Error', 'Terjadi kesalahan server', 'error');
            }
          });

    // 3. Event Listener Tombol "Hapus Nilai"
    document.querySelector('#observasiTableContainer').addEventListener('click', async (e) => {
    	const btn = e.target.closest('.btn-delete-grade');
    	if (btn) {
    		const id = btn.dataset.id;
    		const name = btn.dataset.student_name;

    		const confirm = await Swal.fire({
    			title: 'Hapus Nilai?',
    			text: `Nilai untuk ${name} akan dihapus.`,
    			icon: 'warning',
    			showCancelButton: true,
    			confirmButtonColor: '#d33',
    			confirmButtonText: 'Ya, Hapus'
    		});

    		if (confirm.isConfirmed) {
    			const formData = new FormData();
    			formData.append('id', id);
    			formData.append(window.CSRF_TOKEN_NAME, csrfConfig.tokenHash);

    			try {
    				const response = await fetch(`${window.BASE_URL}guru/pbl_observasi/delete_grade`, {
    					method: 'POST',
    					body: formData
    				});
    				const result = await response.json();

    				if (result.csrf_hash) {
    					document.querySelectorAll(`input[name="${window.CSRF_TOKEN_NAME}"]`).forEach(el => el.value = result.csrf_hash);
    					csrfConfig.tokenHash = result.csrf_hash;
    				}

    				if (result.status === 'success') {
    					Swal.fire({ icon: 'success', title: 'Terhapus', text: result.message, timer: 1000, showConfirmButton: false });
    					handler.loadData();
    				} else {
    					Swal.fire('Gagal', result.message, 'error');
    				}
    			} catch (error) {
    				Swal.fire('Error', 'Gagal menghapus nilai', 'error');
    			}
    		}
    	}
    });

  });