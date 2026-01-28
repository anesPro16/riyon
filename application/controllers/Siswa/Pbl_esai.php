<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_esai extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		is_logged_in(); // Pastikan otentikasi siswa

		// Ambil user_id dari sesi (Asumsi data user disimpan di sesi)
		$this->user_id = $this->session->userdata('user_id'); 
		if (!$this->user_id) {
			// Jika user_id tidak ada (belum login), redirect ke halaman login
			redirect('auth/login'); 
		}
	}

	/**
  * Halaman Detail Esai untuk Siswa
  */
  public function detail($essay_id = null)
  {
    if (!$essay_id) redirect('siswa/pbl');

    $essay = $this->Esai_model->get_essay_details($essay_id);
    if (!$essay) show_404();

    // Ambil data submission siswa (jika ada)
    $submission = $this->Esai_model->get_student_submission($essay_id, $this->user_id);
    
    $data['title'] = 'Jawab Esai: ' . $essay->title;
    $data['essay'] = $essay;
    $data['submission'] = $submission;
    $data['class_id'] = $essay->class_id;
    $data['user'] = $this->session->userdata();

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
    $this->load->view('siswa/pbl_esai_detail', $data);
    $this->load->view('templates/footer');
  }

  /**
   * Mengambil daftar pertanyaan
   */
  public function get_questions_json($essay_id)
  {
    $data = $this->Esai_model->get_questions($essay_id);
    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($data));
  }

	/**
	 * AJAX: Menyimpan/Memperbarui Jawaban Siswa
	 */
	public function save_submission()
	{
		$this->form_validation->set_rules('essay_id', 'ID Esai', 'required');
    $this->form_validation->set_rules('submission_content', 'Jawaban', 'required|trim');

		if ($this->form_validation->run() === FALSE) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
			return;
		}

		$essay_id = $this->input->post('essay_id');
		$content  = $this->input->post('submission_content', TRUE);
		$submission_id = $this->input->post('submission_id'); // ID Submission, bisa kosong/null

		// Cek apakah esai ini ada
		$essay = $this->Esai_model->get_essay_details($essay_id);
		if (!$essay) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'error', 'message' => 'Esai tidak ditemukan.']));
			return;
		}

		// Cek apakah sudah dinilai? Jika sudah, tolak edit.
    if ($submission_id) {
      $existing = $this->Esai_model->get_student_submission($essay_id, $this->user_id);
      if ($existing && $existing->grade !== null) {
          echo json_encode(['status' => 'error', 'message' => 'Tugas sudah dinilai, tidak dapat diedit.']);
          return;
      }
    }

    $save = $this->Esai_model->save_student_submission($essay_id, $this->user_id, $content, $submission_id);

		if ($save) {
			$msg = $submission_id ? 'Jawaban berhasil diperbarui.' : 'Jawaban berhasil dikirim.';
			
			// Ambil ulang data submission terbaru (opsional, untuk tampilan real-time)

			// $new_submission = $this->Esai_model->get_student_submission($essay_id, $this->user_id);

			echo json_encode([
				'status' => 'success',
				'message' => $msg,
				// 'data' => $new_submission,
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Gagal menyimpan jawaban. Silakan coba lagi.',
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
		}
	}
}


/* End of file Pbl_esai.php */
/* Location: ./application/controllers/Siswa/Pbl_esai.php */