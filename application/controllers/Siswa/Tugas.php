<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tugas extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// $this->load->model('Tugas_model');
		is_logged_in();
	}

	public function index()
	{
		$data['title'] = 'Tugas';
		$data['user'] = $this->session->userdata();
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar');
		// $this->load->view('siswa/tugas/index', $data);
		$this->load->view('templates/footer');	
	}

}

/* End of file Tugas.php */
/* Location: ./application/controllers/Siswa/Tugas.php */