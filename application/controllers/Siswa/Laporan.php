<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

	public function __construct()
  {
    parent::__construct();
    is_logged_in();
  }

	/* =========================
   * Siswa – DAFTAR Kelas
   * ========================= */
  public function index()
  {
    $user_id = $this->session->userdata('user_id');

    // Ambil data kelas dari model
    $kelas = $this->Murid_model->get_kelas_by_murid($user_id);

    $class_id = $kelas->id;
    if (!$class_id) show_404();
    
    $user = $this->session->userdata();

    if ($user['role'] != 'Siswa') { 
        show_error('Akses khusus siswa', 403);
    }

    $data['title'] = 'Laporan Hasil Belajar';
    $data['class_id'] = $class_id;
    $data['user'] = $this->session->userdata();
    $data['url_name'] = 'siswa';
    $role_id = $this->session->userdata('role_id');    
    $data['is_admin_or_guru'] = $this->User_model->check_is_teacher($role_id);

    $data['exam_subjects'] = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'PPKN'];

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('siswa/pbl_tahap5', $data);
    $this->load->view('templates/footer');
  }

  public function get_my_recap($class_id)
  {
    $user_id = $this->session->userdata('user_id');    
    // Ambil data nilai
    $data = $this->Refleksi_model->get_student_score_data($user_id, $class_id);
    
    // Return JSON
    echo json_encode($data);
  }

}

/* End of file Laporan.php */
/* Location: ./application/controllers/Siswa/Laporan.php */