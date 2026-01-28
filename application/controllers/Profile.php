<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
        // Pastikan helper dan library dimuat
		$this->load->model('User_model');
		$this->load->library(['form_validation', 'upload']);
        $this->load->helper(['text', 'file', 'security']); // load security helper
        
        // Cek login (sesuaikan dengan helper auth Anda)
        // is_logged_in(); 
        if (!$this->session->userdata('user_id')) {
        	redirect('auth');
        }
      }

      public function index()
      {
      	$user_id = $this->session->userdata('user_id');

      	$data['title'] = 'Profile Saya';
        // Ambil data terbaru dari DB, jangan hanya dari session untuk isian form
      	$data['user_db']  = $this->User_model->get_profile($user_id);

        // Data session untuk header
      	$data['user'] = $this->session->userdata();

      	$this->load->view('templates/header', $data);
      	$this->load->view('templates/sidebar');
      	$this->load->view('profile/index', $data);
      	$this->load->view('templates/footer');
      }

    // Method khusus melayani gambar (agar path asli tersembunyi & handle cache)
      public function photo()
      {
      	$user_id = $this->session->userdata('user_id');
        // Ambil nama file dari DB agar akurat
      	$user = $this->User_model->get($user_id);
      	$image = $user->image ?? 'foto.jpg';

      	$path = FCPATH . 'uploads/profile/' . $image;

        // Jika file tidak ada fisik, load default
      	if (!file_exists($path)) {
      		$path = FCPATH . 'uploads/profile/foto.jpg'; 
            // Pastikan Anda punya file default.png di folder uploads/profile/
      		if (!file_exists($path)) {
                 // Fallback terakhir jika default.png juga tidak ada
      			show_404(); 
      		}
      	}

      	$mime = mime_content_type($path);

        // Header agar browser tidak men-cache gambar secara agresif saat update
      	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
      	header("Cache-Control: post-check=0, pre-check=0", false);
      	header("Pragma: no-cache");
      	header('Content-Type: ' . $mime);
      	header('Content-Length: ' . filesize($path));

      	readfile($path);
      	exit;
      }

      public function update_ajax()
      {
        // 1. Cek Request Ajax
      	if (!$this->input->is_ajax_request()) {
      		exit('No direct script access allowed');
      	}

      	$user_id = $this->session->userdata('user_id');

        // 2. Validasi Input
      	$this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[4]|callback_username_check');

      	if ($this->input->post('password')) {
      		$this->form_validation->set_rules('password', 'Password', 'min_length[8]');
      		$this->form_validation->set_rules('password_confirm', 'Konfirmasi Password', 'matches[password]');
      	}

        // Jika validasi form gagal
      	if ($this->form_validation->run() == FALSE) {
      		echo json_encode([
      			'status' => false,
      			'message' => validation_errors('<div class="text-danger mb-1">', '</div>'),
                'csrf_token' => $this->security->get_csrf_hash() // Refresh Token
              ]);
      		return;
      	}

        // 3. Siapkan Data Update
      	$data = [
      		'username' => htmlspecialchars($this->input->post('username', true)),
      	];

      	if ($this->input->post('password')) {
      		$data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
      	}

        // 4. Handle Upload Image
      	if (!empty($_FILES['image']['name'])) {

            // ✅ Folder Writable Check
      		$upload_path = FCPATH . 'uploads/profile/';
      		if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true); // Buat folder jika belum ada
              }

              $config = [
              	'upload_path'   => $upload_path,
                'allowed_types' => 'jpg|jpeg|png|gif', // Tambahkan gif jika perlu
                'max_size'      => 2048, // 2MB
                'file_name'     => 'profile_' . $user_id . '_' . time(), // Unique name time() mencegah cache
                'overwrite'     => true
              ];

              $this->upload->initialize($config);

              if (!$this->upload->do_upload('image')) {
              	echo json_encode([
              		'status' => false,
              		'message' => $this->upload->display_errors('<p>', '</p>'),
              		'csrf_token' => $this->security->get_csrf_hash()
              	]);
              	return;
              }

            // Hapus file lama jika bukan default (Optional, good practice)
              $old_image = $this->session->userdata('image');
              if ($old_image && $old_image != 'foto.jpg') {
              	if (file_exists($upload_path . $old_image)) {
              		unlink($upload_path . $old_image);
              	}
              }

              $data['image'] = $this->upload->data('file_name');
            }

        // 5. Update Database
            $update = $this->User_model->update_profile($user_id, $data);

        // 6. Update Session (PENTING: Agar header berubah tanpa logout)
            $session_data = ['username' => $data['username']];
            if (isset($data['image'])) {
            	$session_data['image'] = $data['image'];
            }
            $this->session->set_userdata($session_data);

        // 7. Response Sukses
            echo json_encode([
            	'status'     => true,
            	'message'    => 'Profile berhasil diperbarui!',
            // Tambahkan timestamp agar browser mereload gambar baru
            	'image_url'  => base_url('profile/photo?t=' . time()), 
            'csrf_token' => $this->security->get_csrf_hash(), // Kirim token baru
            'new_name'   => html_escape($data['username']) // Untuk update teks nama di UI
          ]);
          }

          public function username_check($username)
          {
          	$user_id = $this->session->userdata('user_id');
          	if ($this->User_model->is_username_exist($username, $user_id)) {
          		$this->form_validation->set_message('username_check', '{field} sudah digunakan oleh pengguna lain.');
          		return false;
          	}
          	return true;
          }
        }