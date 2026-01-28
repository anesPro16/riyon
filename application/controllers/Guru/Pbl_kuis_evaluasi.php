<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_kuis_evaluasi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		// is_logged_in();
		$this->load->model('Pbl_kuis_evaluasi_model', 'quiz_model'); // Model baru
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->helper('security');
		$this->load->helper('url');
		// $this->load->helper('ulid'); // Pastikan helper ulid di-load
	}

	/**
	 * Halaman Detail Kuis (Menampilkan list pertanyaan)
	 */
	public function detail($quiz_id = null)
	{
		if (!$quiz_id) redirect('guru/pbl'); // Arahkan kembali

		$quiz = $this->quiz_model->get_quiz_details($quiz_id);
		if (!$quiz) show_404();

		$data['title'] = 'Kelola Kuis Evaluasi: ' . $quiz->title;
		$data['quiz'] = $quiz;
		$data['class_id'] = $quiz->class_id; // Ambil class_id dari kuis
		$data['user'] = $this->session->userdata();

		$this->load->view('templates/header', $data);
		// $this->load->view('templates/sidebar');
		$this->load->view('guru/pbl_kuis_evaluasi_detail', $data); // View Detail BARU
		$this->load->view('templates/footer');
	}

	/* ===== AJAX UNTUK CRUD PERTANYAAN ===== */

	/**
	 * AJAX: Mengambil semua pertanyaan untuk CrudHandler
	 */
	public function get_questions($quiz_id)
	{
		$data = $this->quiz_model->get_questions($quiz_id);
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	/**
	 * AJAX: Menyimpan (Create/Update) pertanyaan
	 */
	public function save_question()
	{
		// Validasi
		$this->form_validation->set_rules('question_text', 'Teks Pertanyaan', 'required');
		$this->form_validation->set_rules('option_a', 'Opsi A', 'required');
		$this->form_validation->set_rules('option_b', 'Opsi B', 'required');
		$this->form_validation->set_rules('option_c', 'Opsi C', 'required');
		$this->form_validation->set_rules('option_d', 'Opsi D', 'required');
		$this->form_validation->set_rules('correct_answer', 'Jawaban Benar', 'required');

		if ($this->form_validation->run() === FALSE) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
			return;
		}

		$id = $this->input->post('id'); // ID Pertanyaan (jika edit)
		$payload = [
			'quiz_id' => $this->input->post('quiz_id'),
			'question_text' => $this->input->post('question_text'),
			'option_a' => $this->input->post('option_a'),
			'option_b' => $this->input->post('option_b'),
			'option_c' => $this->input->post('option_c'),
			'option_d' => $this->input->post('option_d'),
			'correct_answer' => $this->input->post('correct_answer'),
		];

		if ($id) {
			// Mode Update
			$this->quiz_model->update_question($id, $payload);
			$msg = 'Pertanyaan diperbarui.';
		} else {
			// Mode Create
			$payload['id'] = generate_ulid();
			$this->quiz_model->insert_question($payload);
			$msg = 'Pertanyaan ditambahkan.';
		}

		echo json_encode([
			'status' => 'success',
			'message' => $msg,
			'csrf_hash' => $this->security->get_csrf_hash()
		]);
	}

	/**
	 * AJAX: Menghapus pertanyaan (Sesuai pola pbl_tahap4.js)
	 */
	public function delete_question($question_id = null)
	{
		if ($question_id) {
			$this->quiz_model->delete_question($question_id);
			$msg = 'Pertanyaan dihapus.';
			$status = 'success';
		} else {
			$msg = 'ID Pertanyaan tidak valid.';
			$status = 'error';
		}

		echo json_encode([
			'status' => $status,
			'message' => $msg,
			'csrf_hash' => $this->security->get_csrf_hash()
		]);
	}
}

/* End of file Pbl_kuis_evaluasi.php */
/* Location: ./application/models/Pbl_kuis_evaluasi.php */