<style>
	/* Style Sederhana */
	.profile-img-preview {
		width: 120px;
		height: 120px;
		object-fit: cover;
		border-radius: 50%;
		border: 3px solid #dee2e6;
	}
</style>

<div class="container-fluid">
	<div class="row">
		<div class="col-xl-4">
			<div class="card">
				<div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
					
					<img src="<?= base_url('profile/photo'); ?>" 
					class="profile-img-preview mb-3" 
					id="mainProfileImg">

					<h2 id="displayName"><?= html_escape($user_db->name ?? $user['name']) ?></h2>
					<h3><?= html_escape($user_db->role) ?></h3>
				</div>
			</div>
		</div>

		<div class="col-xl-8">
			<div class="card">
				<div class="card-body pt-3">

					<ul class="nav nav-tabs nav-tabs-bordered">
						<li class="nav-item">
							<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview">Overview</button>
						</li>
						<li class="nav-item">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#edit">Edit Profile</button>
						</li>
					</ul>

					<div class="tab-content pt-2">

						<div class="tab-pane fade show active" id="overview">
							<div class="row mb-2">
								<label class="col-lg-3 col-md-4 label fw-bold">Nama</label>
								<div class="col-lg-9"><?= html_escape($user_db->name) ?></div>
							</div>
							<div class="row mb-2">
								<label class="col-lg-3 col-md-4 label fw-bold">Email</label>
								<div class="col-lg-9"><?= html_escape($user_db->email) ?></div>
							</div>
							<div class="row mb-2">
								<label class="col-lg-3 col-md-4 label fw-bold">Username</label>
								<div class="col-lg-9" id="displayUsername"><?= html_escape($user_db->username) ?></div>
							</div>
						</div>

						<div class="tab-pane fade" id="edit">
							<form id="formProfile" enctype="multipart/form-data">
								
								<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" 
								value="<?= $this->security->get_csrf_hash(); ?>" 
								id="csrfToken">

								<div class="row mb-3">
									<label class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
									<div class="col-md-8 col-lg-9">
										<img src="<?= base_url('profile/photo'); ?>" id="formProfileImg" class="profile-img-preview mb-2">
										<div class="pt-2">
											<input type="file" name="image" class="form-control" accept="image/*" onchange="previewFile(this)">
											<small class="text-muted d-block mt-1">Max 2MB (JPG/PNG)</small>
										</div>
									</div>
								</div>

								<div class="row mb-3">
									<label class="col-md-4 col-lg-3 col-form-label">Username</label>
									<div class="col-md-8 col-lg-9">
										<input type="text" name="username" class="form-control" value="<?= html_escape($user_db->username); ?>" required readonly>
									</div>
								</div>

								<div class="row mb-3">
									<label class="col-md-4 col-lg-3 col-form-label">Password Baru</label>
									<div class="col-md-8 col-lg-9">
										<div class="input-group">
											<input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
											<span class="input-group-text" onclick="togglePassword()" style="cursor:pointer">
												<i class="bi bi-eye"></i>
											</span>
										</div>
										<small class="text-muted">Kosongkan jika tidak ingin mengganti password</small>
									</div>
								</div>

								<div class="row mb-3">
									<label class="col-md-4 col-lg-3 col-form-label">Konfirmasi Password</label>
									<div class="col-md-8 col-lg-9">
										<input type="password" name="password_confirm" class="form-control" autocomplete="new-password">
									</div>
								</div>

								<div class="text-center">
									<button type="submit" class="btn btn-primary">Simpan Perubahan</button>
								</div>
							</form>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?= base_url('assets/js/jquery-3.6.0.min.js') ?>"></script>

<script>
// 1. Preview Gambar Lokal (Sebelum Upload)
function previewFile(input) {
	const file = input.files[0];
	if (file) {
        // Validasi ukuran simpel di JS (2MB)
        if (file.size > 2097152) {
        	Swal.fire('Error', 'Ukuran file terlalu besar (Maks 2MB)', 'error');
            input.value = ''; // Reset input
            return;
          }
          
          const reader = new FileReader();
          reader.onload = function(e) {
            // Update preview di form saja
            $('#formProfileImg').attr('src', e.target.result); 
          }
          reader.readAsDataURL(file);
        }
      }

// 2. Toggle Password Visibility
function togglePassword() {
	const pass = document.getElementById('password');
	pass.type = pass.type === 'password' ? 'text' : 'password';
}

// 3. AJAX Submission
$('#formProfile').on('submit', function(e) {
	e.preventDefault();

	const formData = new FormData(this);
	const csrfName = '<?= $this->security->get_csrf_token_name(); ?>';

	Swal.fire({
		title: 'Simpan perubahan?',
		text: "Data profile akan diperbarui",
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: 'Ya, simpan',
		cancelButtonText: 'Batal'
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: "<?= base_url('profile/update_ajax'); ?>",
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				dataType: "json",
				beforeSend: function() {
					Swal.fire({
						title: 'Menyimpan...',
						allowOutsideClick: false,
						didOpen: () => Swal.showLoading()
					});
				},
				success: function(res) {
          // Update CSRF Token untuk request berikutnya (SANGAT PENTING)
          $('#csrfToken').val(res.csrf_token); 
          $('input[name="'+csrfName+'"]').val(res.csrf_token); // Update hidden input global jika ada

          if (res.status) {
          	Swal.fire({
          		icon: 'success',
          		title: 'Berhasil',
          		text: res.message,
          		timer: 1500,
          		showConfirmButton: false
          	});

              // Update Tampilan Secara Realtime
              if (res.image_url) {
                  // Update gambar sidebar & form dengan cache buster
                  $('#mainProfileImg').attr('src', res.image_url);
                  $('#formProfileImg').attr('src', res.image_url);
                  
                  // Jika ada gambar di header navbar (opsional)
                  $('.header-profile-user').attr('src', res.image_url);
                }

                if (res.new_name) {
                  // Update text username di overview
                  $('#displayUsername').text(res.new_name);
                }
                
              // Reset field password
              $('input[type="password"]').val('');

            } else {
              // Tampilkan error (validasi dll)
              Swal.fire({
              	icon: 'error',
              	title: 'Gagal',
              	html: res.message
              });
            }
          },
          error: function(xhr, status, error) {
          	Swal.fire('Error', 'Terjadi kesalahan server: ' + status, 'error');
          }
        });
		}
	});
});
</script>