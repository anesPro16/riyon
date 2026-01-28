<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_refleksi_akhir extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		is_logged_in();
		$this->load->model('Pbl_refleksi_akhir_model', 'reflection_model');
	}

	/**
	 * Halaman Detail Refleksi (Menampilkan list prompt)
	 */
	public function detail($reflection_id = null)
	{
		if (!$reflection_id) redirect('guru/pbl'); // Arahkan kembali

		$reflection = $this->reflection_model->get_reflection_details($reflection_id);
		if (!$reflection) show_404();

		$data['title'] = 'Kelola Refleksi: ' . $reflection->title;
		$data['reflection'] = $reflection;
		$data['class_id'] = $reflection->class_id; // Ambil class_id dari refleksi
		$data['user'] = $this->session->userdata();
		$data['prompts'] = $this->reflection_model->get_prompts($reflection_id);

		$this->load->view('templates/header', $data);
		// $this->load->view('templates/sidebar');
		$this->load->view('guru/pbl_refleksi_akhir_detail', $data);
		$this->load->view('templates/footer');
	}

	/* ===== AJAX UNTUK CRUD PROMPT/PERTANYAAN ===== */

	/**
	 * AJAX: Mengambil semua prompt untuk CrudHandler
	 */
	public function get_prompts($reflection_id)
	{
		$data = $this->reflection_model->get_prompts($reflection_id);
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	public function get_submissions($reflection_id)
	{
		$data = $this->reflection_model->get_submissions($reflection_id);
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	/**
	 * AJAX: Menyimpan (Create/Update) prompt
	 */
	public function save_prompt()
	{
		// Validasi
		$this->form_validation->set_rules('prompt_text', 'Teks Pertanyaan Refleksi', 'required');

		if ($this->form_validation->run() === FALSE) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
			return;
		}

		$id = $this->input->post('id'); // ID Prompt (jika edit)
		$payload = [
			'reflection_id' => $this->input->post('reflection_id'),
			'prompt_text' => $this->input->post('prompt_text'),
		];

		if ($id) {
			// Mode Update
			$this->reflection_model->update_prompt($id, $payload);
			$msg = 'Pertanyaan refleksi diperbarui.';
		} else {
			// Mode Create
			$payload['id'] = generate_ulid();
			$this->reflection_model->insert_prompt($payload);
			$msg = 'Pertanyaan refleksi ditambahkan.';
		}

		echo json_encode([
			'status' => 'success',
			'message' => $msg,
			'csrf_hash' => $this->security->get_csrf_hash()
		]);
	}

	/**
	 * AJAX: Menghapus prompt (Sesuai pola $id dari URL)
	 */
	public function delete_prompt($prompt_id = null)
	{
		if ($prompt_id) {
			$this->reflection_model->delete_prompt($prompt_id);
			$msg = 'Pertanyaan refleksi dihapus.';
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

/* End of file Pbl_refleksi_akhir.php */
/* Location: ./application/controllers/Guru/Pbl_refleksi_akhir.php */