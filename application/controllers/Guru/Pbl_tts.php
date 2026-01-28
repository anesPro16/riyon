<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_tts extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
    is_logged_in();
    $this->load->model('Pbl_tts_model');
  }

  // Halaman detail TTS (preview + pertanyaan)
  public function detail($tts_id = null)
	{
	  if (!$tts_id) redirect('guru/pbl/tahap2');
	  $tts = $this->Pbl_tts_model->get_tts_by_id($tts_id);
	  if (!$tts) show_404();

	  // Decode grid_data JSON
	  // Jika grid_data kosong → default 15
		$gridSize = (int)($tts->grid_data ?: 15);
		if ($gridSize < 5) $gridSize = 5; // minimal keamanan
		$tts->grid_size = $gridSize;

	  $data['title'] = 'Detail TTS: ' . $tts->title;
	  $data['tts'] = $tts;
	  $data['class_id'] = $tts->class_id;
	  $data['user'] = $this->session->userdata();

	  $this->load->view('templates/header', $data);
	  $this->load->view('guru/tts_detail', $data);
	  $this->load->view('templates/footer');
	}


  // --- AJAX GET daftar pertanyaan ---
  public function get_questions($tts_id)
  {
  	$data = $this->Pbl_tts_model->get_questions($tts_id);
  	$this->output
     ->set_content_type('application/json')
     ->set_output(json_encode($data));
  }

	// --- AJAX SAVE pertanyaan ---
  public function save_question()
	{
		// Validasi
		$this->form_validation->set_rules('tts_id', 'TTS', 'required');
		// $this->form_validation->set_rules('number', 'Nomor', 'required|integer');
		// Kita letakkan di 'number' karena callback akan memeriksa semua field lain
		$this->form_validation->set_rules('number', 'Nomor', 'required|integer|callback__check_question_uniqueness');
		$this->form_validation->set_rules('direction', 'Arah', 'required');
		$this->form_validation->set_rules('question', 'Pertanyaan', 'required');
		// $this->form_validation->set_rules('answer', 'Jawaban', 'required');
		$this->form_validation->set_rules('answer', 'Jawaban', 'required|callback__check_answer_collision');
		$this->form_validation->set_rules('start_x', 'Koordinat X', 'required|integer');
		$this->form_validation->set_rules('start_y', 'Koordinat Y', 'required|integer');

		if ($this->form_validation->run() === FALSE) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
			return;
		}

		$id = $this->input->post('id');
		$data = [
			'tts_id' => $this->input->post('tts_id', TRUE),
			'number' => $this->input->post('number', TRUE),
			'direction' => $this->input->post('direction', TRUE),
			'question' => $this->input->post('question', TRUE),
			'answer' => strtoupper(trim($this->input->post('answer', TRUE))),
			'start_x' => $this->input->post('start_x', TRUE),
			'start_y' => $this->input->post('start_y', TRUE)
		];

		if ($id) {
			$getQuestion = $this->Pbl_tts_model->get_tts_questions_by_id($id);
      if (!$getQuestion) {
        echo json_encode(['status'=>'error','message'=>'Soal TTS tidak ada!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
      }

			$this->Pbl_tts_model->update_question($id, $data);
			$msg = 'Pertanyaan diperbarui.';
		} else {
			$data['id'] = generate_ulid(); 
			$this->Pbl_tts_model->insert_question($data);
			$msg = 'Pertanyaan ditambahkan.';
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => 'success',
				'message' => $msg,
				'csrf_hash' => $this->security->get_csrf_hash()
			]));
	}

	/**
	 * Memeriksa keunikan (Nomor + Arah) DAN (Koordinat + Arah)
	 * @param string $number Nilai dari field 'number'
	 * @return bool TRUE jika valid, FALSE jika ada duplikat
	 */
	public function _check_question_uniqueness($number)
	{
		// Ambil semua data yang diperlukan dari POST
		$id = $this->input->post('id'); // ID soal (jika sedang edit)
		$tts_id = $this->input->post('tts_id');
		$direction = $this->input->post('direction');
		$start_x = $this->input->post('start_x');
		$start_y = $this->input->post('start_y');

		// 1. Validasi Aturan 1: (Nomor + Arah) harus unik
		$is_number_duplicate = $this->Pbl_tts_model->check_duplicate_number($tts_id, $number, $direction, $id);
		
		if ($is_number_duplicate) {
			$this->form_validation->set_message('_check_question_uniqueness', 'Kombinasi <strong>Nomor ' . $number . ' ' . $direction . '</strong> sudah digunakan.');
			return FALSE;
		}

		// 2. Validasi Aturan 2: (Koordinat X + Y + Arah) harus unik
		// Ini sesuai dengan contoh Anda (boleh X,Y sama tapi arah beda)
		$is_coord_duplicate = $this->Pbl_tts_model->check_duplicate_coordinate($tts_id, $start_x, $start_y, $direction, $id);
		
		if ($is_coord_duplicate) {
			$this->form_validation->set_message('_check_question_uniqueness', 'Koordinat <strong>(X: ' . $start_x . ', Y: ' . $start_y . ')</strong> sudah digunakan untuk arah <strong>' . $direction . '</strong>.');
			return FALSE;
		}

		// Jika lolos semua
		return TRUE;
	}

	/**
	 * Memeriksa tabrakan huruf jawaban dengan jawaban lain di grid
	 * @param string $answer Nilai dari field 'answer'
	 * @return bool TRUE jika valid, FALSE jika ada tabrakan
	 */
	public function _check_answer_collision($answer)
	{
		// Ambil semua data yang diperlukan
		$id = $this->input->post('id');
		$tts_id = $this->input->post('tts_id');
		$start_x = $this->input->post('start_x');
		$start_y = $this->input->post('start_y');
		$direction = $this->input->post('direction');
		$answer_clean = strtoupper(trim($answer)); // Gunakan jawaban yang sudah dibersihkan

		// Panggil fungsi Model baru untuk melakukan validasi
		$validation_result = $this->Pbl_tts_model->validate_answer_placement(
			$tts_id,
			$id,
			$answer_clean,
			$start_x,
			$start_y,
			$direction
		);

		if ($validation_result['valid'] === false) {
			// Set pesan error spesifik yang diterima dari Model
			$this->form_validation->set_message('_check_answer_collision', $validation_result['message']);
			return FALSE;
		}

		return TRUE;
	}

	// --- AJAX DELETE pertanyaan ---
  public function delete_question()
	{
		// Ambil ID dari POST body, sesuai config CrudHandler
		$id = $this->input->post('id', TRUE);

		$getQuestion = $this->Pbl_tts_model->get_tts_questions_by_id($id);
    if (!$getQuestion) {
      echo json_encode(['status'=>'error','message'=>'Gagal hapus soal TTS!', 'csrf_hash' => $this->security->get_csrf_hash()]);
      return;
    }

		$this->Pbl_tts_model->delete_question($id);

		// Kirim kembali hash CSRF baru agar tetap sinkron
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => 'success',
				'message' => 'Pertanyaan dihapus.',
				'csrf_hash' => $this->security->get_csrf_hash()
			]));
	}

}


/* End of file Pbl_tts.php */
/* Location: ./application/controllers/Guru/Pbl_tts.php */