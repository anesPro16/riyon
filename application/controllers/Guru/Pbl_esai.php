<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_esai extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		is_logged_in();
	}

	/**
	 * Halaman Detail Esai (Menampilkan List Jawaban Siswa)
	 */
	public function detail($essay_id = null)
	{
		if (!$essay_id) redirect('guru/pbl');

		$essay = $this->Esai_model->get_essay_details($essay_id);
		if (!$essay) show_404();

		$data['title'] = 'Halaman Detail Esai';
		$data['essay'] = $essay;
		$data['class_id'] = $essay->class_id; // Ambil class_id dari esai
		$data['user'] = $this->session->userdata();

		$this->load->view('templates/header', $data);
		$this->load->view('guru/pbl_esai_detail', $data); // View Detail BARU
		$this->load->view('templates/footer');
	}

	/* ==============================================
     ENDPOINT 1: MANAJEMEN SOAL (QUESTIONS)
     ============================================== */

  public function get_questions_json($essay_id)
  {
      $data = $this->Esai_model->get_questions($essay_id);
      echo json_encode($data);
  }

  public function save_question()
  {
    // Validasi: Pastikan question_text dikirim (bisa array atau string)
    if (empty($this->input->post('question_text'))) {
        echo json_encode(['status' => 'error', 'message' => 'Soal tidak boleh kosong.']);
        return;
    }

    $essay_id = $this->input->post('essay_id');
    $id = $this->input->post('id'); // ID untuk Edit (jika ada)
    
    // Input dari View 'question_text[]' akan diterima sebagai Array
    $questions = $this->input->post('question_text'); 
    
    // Pastikan formatnya array agar konsisten di Model
    if (!is_array($questions)) {
        $questions = [$questions];
    }

    $result = $this->Esai_model->save_question_batch($essay_id, $questions, $id);

    if ($result) {
      echo json_encode([
        'status' => 'success', 
        'message' => 'Soal berhasil disimpan.', 
        'csrf_hash' => $this->security->get_csrf_hash()
      ]);
    } else {
      echo json_encode([
        'status' => 'error', 
        'message' => 'Gagal menyimpan atau tidak ada data yang valid.'
      ]);
    }
  }

  public function delete_question($id)
  {
    if ($this->Esai_model->delete_question($id)) {
       echo json_encode(['status' => 'success', 'message' => 'Soal berhasil dihapus.', 'csrf_hash' => $this->security->get_csrf_hash()]);
    } else {
       echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus soal.']);
    }
  }

  /* ==============================================
     ENDPOINT 2: MANAJEMEN PENILAIAN (GRADING)
     ============================================== */

  // Mengambil data siswa + jawaban mereka untuk tabel grading
  public function get_grading_json($essay_id)
  {
    // Ambil detail esai dulu untuk tahu class_id nya
    $essay = $this->Esai_model->get_essay_details($essay_id);
    if (!$essay) {
        echo json_encode([]); return;
    }

    // Ambil data gabungan (Siswa + Jawaban)
    $data = $this->Esai_model->get_class_students_with_submission($essay->class_id, $essay_id);
    echo json_encode($data);
  }

  public function save_grade()
  {
    $submission_id = $this->input->post('submission_id');
    
    // Validasi sederhana
    if(empty($submission_id)) {
      echo json_encode(['status' => 'error', 'message' => 'Data submission tidak ditemukan. Siswa mungkin belum mengumpulkan.']);
      return;
    }

    $data = [
      'grade'    => $this->input->post('grade'),
      'feedback' => $this->input->post('feedback')
    ];

    if ($this->Esai_model->save_feedback($submission_id, $data)) {
      echo json_encode(['status' => 'success', 'message' => 'Nilai & Feedback berhasil disimpan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
    }
  }
	
}

/* End of file Pbl_esai.php */
/* Location: ./application/controllers/Guru/Pbl_esai.php */