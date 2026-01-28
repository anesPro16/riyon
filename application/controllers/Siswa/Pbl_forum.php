<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_forum extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		is_logged_in();
		// $this->load->model('Pbl_forum_model', 'forum_model'); // Model baru
		// $this->load->helper('ulid');
	}

	/**
	 * Halaman Detail Forum (Menampilkan Topik & List Postingan)
	 */
	public function detail($topic_id = null)
	{
		if (!$topic_id) redirect('siswa/pbl');

		$topic = $this->forum_model->get_topic_details($topic_id);
		if (!$topic) show_404();

		$data['title'] = 'Forum: ' . $topic->title;
		$data['topic'] = $topic;
		$data['class_id'] = $topic->class_id; // Ambil class_id dari topik
		$data['user'] = $this->session->userdata();
		$data['url_name'] = 'siswa';
    $role_id = $this->session->userdata('role_id');    
    $data['is_admin_or_guru'] = $this->User_model->check_is_teacher($role_id);

		$this->load->view('templates/header', $data);
		// $this->load->view('templates/sidebar');
		$this->load->view('guru/pbl_forum_detail', $data); // View Detail BARU
		$this->load->view('templates/footer');
	}

	/* ===== AJAX UNTUK CRUD POSTINGAN ===== */

	/**
	 * AJAX: Mengambil semua postingan untuk CrudHandler
	 */
	public function get_posts($topic_id)
	{
		$data = $this->forum_model->get_posts($topic_id);
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	/**
	 * AJAX: Menyimpan (Create/Update) postingan
	 */
	public function save_post()
	{
		$this->form_validation->set_rules('post_content', 'Isi Postingan', 'required');

		if ($this->form_validation->run() === FALSE) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
			return;
		}

		$id = $this->input->post('id'); // ID Postingan (jika edit)
		$topic_id = $this->input->post('topic_id');
		$user_id = $this->session->userdata('user_id'); // ID Guru yang sedang login

		if (empty($user_id) || empty($topic_id)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'error', 'message' => 'Sesi tidak valid.']));
			return;
		}

		$payload = [
			'post_content' => $this->input->post('post_content')
		];

		if ($id) {
			// Mode Update
			// (Guru hanya bisa edit post miliknya sendiri, sesuai logika model)
			$this->forum_model->update_post($id, $user_id, $payload);
			$msg = 'Postingan diperbarui.';
		} else {
			// Mode Create
			$payload['id'] = generate_ulid();
			$payload['topic_id'] = $topic_id;
			$payload['user_id'] = $user_id;
			$this->forum_model->insert_post($payload);
			$msg = 'Postingan ditambahkan.';
		}

		echo json_encode([
			'status' => 'success',
			'message' => $msg,
			'csrf_hash' => $this->security->get_csrf_hash()
		]);
	}

	/**
	 * AJAX: Menghapus postingan (Sesuai pola pbl_tahap2.js)
	 */
	public function delete_post($post_id = null)
	{	
		if ($post_id) {
			$this->forum_model->delete_post($post_id);
			$msg = 'Postingan dihapus.';
			$status = 'success';
		} else {
			$msg = 'ID Postingan tidak valid.';
			$status = 'error';
		}

		echo json_encode([
			'status' => $status,
			'message' => $msg,
			'csrf_hash' => $this->security->get_csrf_hash()
		]);
	}
}

/* End of file Pbl_forum.php */
/* Location: ./application/controllers/Siswa/Pbl_forum.php */