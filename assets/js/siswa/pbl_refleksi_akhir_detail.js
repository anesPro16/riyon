// import CrudHandler from './crud_handler.js';
// assets/js/siswa/pbl_refleksi_akhir_detail.js

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('reflectionForm');
  const btnSubmit = document.getElementById('btnSubmitReflection');

  if (!form) return;

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    // Konfirmasi sebelum kirim
    Swal.fire({
      title: 'Simpan Refleksi?',
      text: "Jawaban Anda akan disimpan. Anda masih bisa mengeditnya nanti.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Simpan!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        submitData();
      }
    });
  });

  function submitData() {
    // Disable tombol agar tidak double submit
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

    const formData = new FormData(form);

    fetch(`${window.BASE_URL}siswa/pbl_refleksi_akhir/submit_reflection`, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      // Update token CSRF
      if (data.csrf_hash) {
        document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`).value = data.csrf_hash;
      }

      if (data.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: data.message,
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          // Optional: Reload untuk melihat status update, atau biarkan di halaman ini
          window.location.reload();
        });
      } else {
        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-send"></i> Simpan Refleksi';
      }
    })
    .catch(error => {
      console.error('Error:', error);
      Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
      btnSubmit.disabled = false;
      btnSubmit.innerHTML = '<i class="bi bi-send"></i> Simpan Refleksi';
    });
  }
});