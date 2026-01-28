<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nilai extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		is_logged_in();
	}

	public function index()
	{
		$data['title'] = 'Data Nilai';
		$data['user'] = $this->session->userdata();
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar');
		// $this->load->view('siswa/nilai/index', $data);
		$this->load->view('templates/footer');
	}

}

/* End of file Nilai.php */
/* Location: ./application/controllers/Siswa/Nilai.php */