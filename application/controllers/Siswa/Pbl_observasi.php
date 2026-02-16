<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_observasi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
    is_logged_in(); // Pastikan helper login aktif
		$this->load->model('Pbl_observasi_model');
		$this->load->library('form_validation');
		$this->load->helper(['security', 'string', 'file']);
	}

  // Halaman Detail untuk Siswa
	public function detail($slot_id = null)
  {
    if (!$slot_id) redirect('siswa/pbl');

    $slot = $this->Pbl_observasi_model->get_slot_by_id($slot_id);
    if (!$slot) show_404();

    $user_id = $this->session->userdata('user_id');
    $result = $this->Pbl_observasi_model->get_student_result($slot_id, $user_id);

    $data['title'] = 'Halaman Detail Observasi';
    $data['slot'] = $slot;
    $data['result'] = $result;
    $data['class_id'] = $slot->class_id;
    $data['user'] = $this->session->userdata();
    $data['url_name'] = 'siswa';

    $this->load->view('templates/header', $data);
      $this->load->view('siswa/pbl_observasi_detail', $data); // View khusus siswa
      $this->load->view('templates/footer');
  }

  // Get Data (Hanya milik siswa login)
  public function get_my_uploads($slot_id)
  {
  	$user_id = $this->session->userdata('user_id');
  	$data = $this->Pbl_observasi_model->get_uploads_by_slot_and_user($slot_id, $user_id);
  	$this->output
  	->set_content_type('application/json')
  	->set_output(json_encode($data));
  }

  // Proses Upload File
  public function upload_file()
  {
  	$this->form_validation->set_rules('description', 'Keterangan', 'trim');

    // Cek apakah file dipilih (CodeIgniter file upload check workaround)
  	if (empty($_FILES['file_upload']['name'])) {
  		$this->form_validation->set_rules('file_upload', 'File', 'required');
  	}

  	if ($this->form_validation->run() === FALSE) {
  		echo json_encode(['status' => 'error', 'message' => validation_errors()]);
  		return;
  	}

    // Konfigurasi Upload
  	$upload_path = './uploads/observasi/';
  	if (!is_dir($upload_path)) mkdir($upload_path, 0777, true);


  	$config['upload_path']   = $upload_path;
    $config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png|ppt|pptx'; // Sesuaikan kebutuhan
    $config['max_size']      = 5120; // 5MB
    $config['encrypt_name']  = TRUE; // Enkripsi nama file agar unik

  	$this->upload->initialize($config);
    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('file_upload')) {
    	echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors('', '')]);
    	return;
    }

    $file_data = $this->upload->data();
    $user_id = $this->session->userdata('user_id');

    // Siapkan data DB
    $data = [
      'id'                  => generate_ulid(), // Helper ULID
      'observation_id' => $this->input->post('observation_slot_id'),
      'user_id'             => $user_id,
      'file_name'           => $file_data['file_name'],
      'original_name'       => $file_data['client_name'], // Nama asli file
      'description'         => $this->input->post('description')
    ];

    $this->Pbl_observasi_model->insert_upload($data);

    echo json_encode([
    	'status' => 'success',
    	'message' => 'File berhasil diunggah.',
    	'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

	// Delete Upload (Hanya jika milik sendiri)
  public function delete_upload($id = null)
  {
  	if (!$id) {
  		echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
  		return;
  	}

  	$user_id = $this->session->userdata('user_id');
  	$file = $this->Pbl_observasi_model->get_upload_by_id($id);

  // KEAMANAN: Pastikan file ada DAN milik user yang sedang login
  	if (!$file || $file->user_id !== $user_id) {
  		echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin menghapus file ini.']);
  		return;
  	}

  	$this->Pbl_observasi_model->delete_upload($id);

  	echo json_encode([
  		'status' => 'success', 
  		'message' => 'File berhasil dihapus.',
  		'csrf_hash' => $this->security->get_csrf_hash()
  	]);
  }
}

      /* End of file Pbl_observasi.php */
/* Location: ./application/controllers/Siswa/Pbl_observasi.php */