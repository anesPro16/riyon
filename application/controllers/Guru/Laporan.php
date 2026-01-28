<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

	public function __construct()
  {
    parent::__construct();
    is_logged_in();
  }

	/* =========================
   * GURU – DAFTAR Kelas
   * ========================= */
  public function index()
  {
    $user = $this->session->userdata();
    $role_id = $user['role_id'];
    $user_id = $user['user_id'];

    // 1. Validasi apakah user adalah Guru
    if (!$this->User_model->check_is_teacher($role_id)) {
        show_error('Akses ditolak. Halaman ini khusus Guru.', 403);
    }

    // 2. Ambil Data Kelas milik Guru tersebut
    // Kita asumsikan Guru_model menghandle logika pengambilan kelas berdasarkan user_id guru
    $data = [
        'title'      => 'Pilih Kelas',
        'user'       => $user,
        'kelas_list' => $this->Guru_model->get_all_classes($user_id), 
        'url_name'   => 'guru'
    ];

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('guru/laporan', $data);
    $this->load->view('templates/footer');
  }

  public function detail_kelas($class_id = null)
  {
    if (!$class_id) {
      redirect('guru/dashboard');
    }

    $data['title'] = 'Laporan Hasil Belajar';
    $data['class_id'] = $class_id;
    $data['user'] = $this->session->userdata();
    $data['url_name'] = 'guru';
    $role_id = $this->session->userdata('role_id');    
    $data['is_admin_or_guru'] = $this->User_model->check_is_teacher($role_id);

    $data['exam_subjects'] = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'PPKN'];

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('guru/detail_laporan', $data);
    $this->load->view('templates/footer');
  }

  public function get_student_recap($class_id)
  {
    $students = $this->Laporan_model->getAllStudentScores($class_id);
    
    // Return JSON langsung untuk ditangkap fetch JS
    echo json_encode($students);
  }

  public function save_reflection()
  {
    $this->form_validation->set_rules('user_id', 'ID Siswa', 'required');
    $this->form_validation->set_rules('class_id', 'ID Kelas', 'required');

    if ($this->form_validation->run() === FALSE) {
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
          'status' => 'error', 
          'message' => validation_errors(),
          'csrf_hash' => $this->security->get_csrf_hash() // Update CSRF
        ]));
      return;
    }


  // 3. Siapkan Data
  $data = [
    'class_id' => $this->input->post('class_id'),
    'user_id' => $this->input->post('user_id'),
    'teacher_reflection' => $this->input->post('teacher_reflection'),
    'student_feedback' => $this->input->post('student_feedback'),
  ];

  // 4. Simpan ke Database
  // Model akan menangani logika Insert (jika baru) atau Update (jika sudah ada)
  $saved = $this->Laporan_model->save_reflection($data);

  // 5. Return JSON sukses + CSRF Hash baru
  if ($saved) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Refleksi dan Feedback berhasil disimpan.',
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  } else {
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menyimpan data ke database.',
        'csrf_hash' => $this->security->get_csrf_hash()
      ]);
    }
  }

}

/* End of file Laporan.php */
/* Location: ./application/controllers/Guru/Laporan.php */