<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_refleksi_akhir extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
    // is_logged_in();
		$this->load->model('Pbl_refleksi_akhir_model', 'reflection_model');
	}

  // Halaman Detail Refleksi (Form Pengisian)
	public function detail($reflection_id = null)
	{
		if (!$reflection_id) redirect('siswa/pbl');

		$reflection = $this->reflection_model->get_reflection_details($reflection_id);
		if (!$reflection) show_404();

    // Ambil Prompt Pertanyaan
		$prompts = $this->reflection_model->get_prompts($reflection_id);

    // Ambil Jawaban Sebelumnya (Jika ada - untuk edit mode)
		$user_id = $this->session->userdata('user_id');
		$submission = $this->reflection_model->get_submission($reflection_id, $user_id);

    // Decode jawaban JSON jika ada
		$existing_answers = [];
		if ($submission) {
			$existing_answers = json_decode($submission->submission_content, true);
		}

		$data['title'] = 'Refleksi: ' . $reflection->title;
		$data['reflection'] = $reflection;
		$data['prompts'] = $prompts;
    $data['submission'] = $submission; // Objek submission utuh
    $data['existing_answers'] = $existing_answers; // Array jawaban per prompt
    
    $data['class_id'] = $reflection->class_id;
    $data['user'] = $this->session->userdata();
    $data['url_name'] = 'siswa';

    $this->load->view('templates/header', $data);
    $this->load->view('siswa/pbl_refleksi_akhir_detail', $data); // View khusus siswa
    $this->load->view('templates/footer');
  }

	// AJAX: Submit Refleksi
  public function submit_reflection()
  {
  	$reflection_id = $this->input->post('reflection_id');
  	$user_id = $this->session->userdata('user_id');

    // Ambil semua input yang berawalan 'answer_'
    // Input name di view akan dibuat seperti: name="answer_[PROMPT_ID]"
  	$answers = [];
  	foreach ($this->input->post() as $key => $val) {
  		if (strpos($key, 'answer_') === 0) {
  			$prompt_id = str_replace('answer_', '', $key);
  			$answers[$prompt_id] = $val;
  		}
  	}

  	if (!$reflection_id || empty($answers)) {
  		echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
  		return;
  	}

  	$data = [
      'id' => generate_ulid(), // Helper ULID
      'reflection_id' => $reflection_id,
      'user_id' => $user_id,
      'submission_content' => json_encode($answers) // Simpan sebagai JSON
    ];

    $status = $this->reflection_model->save_submission($data);

    $msg = ($status == 'inserted') ? 'Refleksi berhasil dikirim.' : 'Refleksi berhasil diperbarui.';

    echo json_encode([
    	'status' => 'success',
    	'message' => $msg,
    	'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }
}

      /* End of file Pbl_refleksi_akhir.php */
/* Location: ./application/controllers/Siswa/Pbl_refleksi_akhir.php */