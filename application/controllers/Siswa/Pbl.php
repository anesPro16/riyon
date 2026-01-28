<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    is_logged_in();
  }

  public function index($class_id = null)
  {
    if (!$class_id) redirect('guru/dashboard');
    // $data['title'] = 'Tahap 1 – Orientasi Masalah';
    $data['title'] = 'Materi';
    $data['url_name'] = 'siswa';
    $data['class_id'] = $class_id;
    $data['user'] = $this->session->userdata();

    $role_id = $this->session->userdata('role_id');    
    $allowed_roles = ['Guru', 'Admin'];

    $data['is_admin_or_guru'] = $this->User_model->check_user_role($role_id, $allowed_roles);

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('guru/materi', $data);
    $this->load->view('templates/footer');
  }

  public function get_data($class_id)
  {
    $data = $this->Materi_model->get_all($class_id);

    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($data));
  }

  public function save()
  {
    $id = $this->input->post('id');
    $class_id = $this->input->post('class_id');
    $title = $this->input->post('title');
    $reflection = $this->input->post('reflection');
    $file_path = '';

    // Upload file (opsional)
    if (!empty($_FILES['file']['name'])) {
      $config['upload_path'] = './uploads/pbl/';
      $config['allowed_types'] = 'jpg|jpeg|png|mp4|mp3|wav|pdf';
      $config['max_size'] = 10240;
      $config['file_name'] = generate_ulid();
      if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);

      $this->upload->initialize($config);
      if ($this->upload->do_upload('file')) {
          $file_path = 'uploads/pbl/' . $this->upload->data('file_name');
      } else {
          echo json_encode(['status' => false, 'msg' => $this->upload->display_errors()]);
          return;
      }
    }

    if ($id == '') {
      // Create
      $data = [
          'id' => generate_ulid(),
          'class_id' => $class_id,
          'title' => $title,
          'reflection' => $reflection,
          'file_path' => $file_path,
          'created_at' => date('Y-m-d H:i:s')
      ];
      $insert = $this->Materi_model->insert($data);
      $status = $insert ? ['status' => true, 'msg' => 'Data berhasil ditambahkan'] : ['status' => false, 'msg' => 'Gagal menambah data'];
      $msg = 'Data berhasil ditambahkan';
    } else {
      $getData = $this->Materi_model->get_orientasi($id);
      if (!$getData) {
        echo json_encode(['status'=>'error','message'=>'materi tidak ada!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
      }
      // Update
      $data = [
          'title' => $title,
          'reflection' => $reflection
      ];
      if ($file_path) $data['file_path'] = $file_path;
      $update = $this->Materi_model->update($id, $data);
      $status = $update ? ['status' => true, 'msg' => 'Data berhasil diperbarui'] : ['status' => false, 'msg' => 'Gagal memperbarui data'];
      $msg = 'Data berhasil diperbarui';
    }

    echo json_encode([
    	'status' => 'success',
    	'message' => $msg,
    	'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

  public function delete($id)
  {
  	$getData = $this->Materi_model->get_orientasi($id);
  	if (!$getData) {
  		echo json_encode(['status'=>'error','message'=>'Gagal hapus materi!', 'csrf_hash' => $this->security->get_csrf_hash()]);
  		return;
  	}

    $result = $this->Materi_model->delete($id);
    if ($result) {
  		$message = 'Materi dihapus';
  		$status = 'success';
  	}
  	
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

  public function kuis($class_id = null)
	{
	  if (!$class_id) redirect('guru/dashboard');
	  // $data['title'] = 'Tahap 2 – Organisasi Belajar';
    $data['title'] = 'Kuis';
	  $data['class_id'] = $class_id;
	  $data['user'] = $this->session->userdata();
    $data['url_name'] = 'siswa';
    $role_id = $this->session->userdata('role_id');    
    $data['is_admin_or_guru'] = $this->User_model->check_is_teacher($role_id);

	  $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
	  $this->load->view('guru/kuis', $data);
	  $this->load->view('templates/footer');
	}

	/*  CRUD KUIS  */
	public function get_quizzes($class_id)
	{
	  $data = $this->Kuis_model->get_quizzes($class_id);
	  $this->output
         ->set_content_type('application/json')
         ->set_output(json_encode($data));
	}

	public function save_quiz()
	{
	  $id = $this->input->post('id');
	  $payload = [
	      'class_id' => $this->input->post('class_id'),
	      'title' => $this->input->post('title'),
	      'description' => $this->input->post('description')
	  ];
	  if ($id) {
      $getQuiz = $this->Kuis_model->get_quiz_by_id($id);
      if (!$getQuiz) {
        echo json_encode(['status'=>'error','message'=>'Kuis tidak ada!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
      }
	    $this->Kuis_model->update_quiz($id, $payload);
	    $msg = 'Kuis diperbarui';
	  } else {
	    $payload['id'] = generate_ulid();
	    $this->Kuis_model->insert_quiz($payload);
	    $msg = 'Kuis ditambahkan';
	  }
	  
	  echo json_encode([
        'status' => 'success',
        'message' => $msg,
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
	}

	public function delete_quiz()
  {
    $id = $this->input->post('id'); // Ambil dari POST, bukan URL
    
    $getQuiz = $this->Kuis_model->get_quiz_by_id($id);
    if (!$getQuiz) {
      echo json_encode(['status'=>'error','message'=>'Gagal hapus Kuis!', 'csrf_hash' => $this->security->get_csrf_hash()]);
      return;
    }

    $this->Kuis_model->delete_quiz($id);

    echo json_encode([
      'status' => 'success',
      'message' => 'kuis dihapus',
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }


	/*  CRUD TTS  */
	public function get_tts($class_id)
	{
	  $data = $this->Kuis_model->get_tts($class_id);
	  $this->output
         ->set_content_type('application/json')
         ->set_output(json_encode($data));
	}

	public function save_tts()
	{
    $this->form_validation->set_rules('grid_data', 'Data Grid', 'required|trim|numeric', [
      'required' => 'Data Grid wajib diisi!',
    ]);

    if ($this->form_validation->run() === FALSE) {
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
      return;
    }

    $grid_val = (int)$this->input->post('grid_data');
    // Cek 3: Minimal 8
    if ($grid_val < 8) {
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'error', 'message' => 'Ukuran Grid minimal adalah 8.']));
      return;
    }
    // Cek 4: Maksimal 25
    if ($grid_val > 25) {
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'error', 'message' => 'Ukuran Grid maksimal adalah 25.']));
      return;
    }

	  $payload = [
	      'class_id' => $this->input->post('class_id'),
	      'title' => $this->input->post('title'),
	      'grid_data' => $this->input->post('grid_data')
	  ];
	  $id = $this->input->post('id');
	  if ($id) {
      $getTts = $this->Pbl_tts_model->get_tts_by_id($id);
      if (!$getTts) {
        echo json_encode(['status'=>'error','message'=>'Teka teki tidak ada!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
      }
	      $this->Kuis_model->update_tts($id, $payload);
	      $msg = 'TTS diperbarui';
	  } else {
	      $payload['id'] = generate_ulid();
	      $this->Kuis_model->insert_tts($payload);
	      $msg = 'TTS ditambahkan';
	  }
	  echo json_encode([
      'status' => 'success',
      'message' => $msg,
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
	}

	public function delete_tts($id)
	{
    $getTts = $this->Pbl_tts_model->get_tts_by_id($id);
    if (!$getTts) {
      echo json_encode(['status'=>'error','message'=>'Gagal hapus teka teki!', 'csrf_hash' => $this->security->get_csrf_hash()]);
      return;
    }

	  $this->Kuis_model->delete_tts($id);
	  echo json_encode([
      'status' => 'success',
      'message' => 'TTS dihapus!',
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
	}

  /**
   *  Halaman utama untuk Tahap 3
   */
  public function observasi($class_id = null)
  {
    if (!$class_id) {
      redirect('guru/dashboard'); // Arahkan ke dashboard jika class_id tidak ada
    }

    // $data['title'] = 'Tahap 3 – Observasi';
    $data['title'] = 'Observasi';
    $data['class_id'] = $class_id;
    $data['user'] = $this->session->userdata();
    $data['url_name'] = 'siswa';
    $role_id = $this->session->userdata('role_id');    
    $data['is_admin_or_guru'] = $this->User_model->check_is_teacher($role_id);

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('guru/observasi', $data);
    $this->load->view('templates/footer');
  }

  /*   CRUD RUANG OBSERVASI  */
  public function get_observations($class_id)
  {
    $data = $this->Observasi_model->get_observations($class_id);
    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($data));
  }

  public function save_observation()
  {
    $this->form_validation->set_rules('title', 'Judul', 'required');

    if ($this->form_validation->run() === FALSE) {
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
      return;
    }

    $id = $this->input->post('id');
    $payload = [
      'class_id' => $this->input->post('class_id'),
      'title' => $this->input->post('title'),
      'description' => $this->input->post('description')
    ];

    if ($id) {
      $getData = $this->Observasi_model->get_observation($id);
      if (!$getData) {
        echo json_encode(['status'=>'error','message'=>'Observasi tidak ada!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
      }
      $this->Observasi_model->update_observation($id, $payload);
      $msg = 'Ruang Observasi diperbarui';
    } else {
      $payload['id'] = generate_ulid();
      $this->Observasi_model->insert_observation($payload);
      $msg = 'Ruang Observasi ditambahkan';
    }
    echo json_encode([
      'status' => 'success',
      'message' => $msg,
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

  public function delete_observation($id = null)
  {
    $getData = $this->Observasi_model->get_observation($id);
    if (!$getData) {
      echo json_encode(['status'=>'error','message'=>'Gagal hapus observasi!', 'csrf_hash' => $this->security->get_csrf_hash()]);
      return;
    }
      
    if ($id) {
      $this->Observasi_model->delete_observation($id);
      $msg = 'Ruang Observasi dihapus.';
      $status = 'success';
    }

    echo json_encode([
      'status' => $status,
      'message' => $msg,
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

  /**
   *  Halaman utama untuk Tahap 4
   */
  public function esai($class_id = null)
  {
    if (!$class_id) {
      redirect('guru/dashboard');
    }

    // $data['title'] = 'Tahap 4 – Pengembangan Solusi';
    $data['title'] = 'Esai';
    $data['class_id'] = $class_id;
    $data['user'] = $this->session->userdata();
    $data['url_name'] = 'siswa';
    $role_id = $this->session->userdata('role_id');    
    $data['is_admin_or_guru'] = $this->User_model->check_is_teacher($role_id);

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
    $this->load->view('guru/esai', $data);
    $this->load->view('templates/footer');
  }

  /*   CRUD ESAI SOLUSI  */
  public function get_solution_essays($class_id)
  {
    $data = $this->Esai_model->get_solution_essays($class_id);
    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($data));
  }

  public function save_solution_essay()
  {
    $this->form_validation->set_rules('title', 'Judul Esai', 'required');

    if ($this->form_validation->run() === FALSE) {
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
      return;
    }

    $id = $this->input->post('id');
    $payload = [
      'class_id' => $this->input->post('class_id'),
      'title' => $this->input->post('title'),
      'description' => $this->input->post('description')
    ];

    if ($id) {
      $getData = $this->Esai_model->get_solution_essay($id);
      if (!$getData) {
        echo json_encode(['status'=>'error','message'=>'Esai tidak ada!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
      }
      $this->Esai_model->update_solution_essay($id, $payload);
      $msg = 'Aktivitas Esai diperbarui';
    } else {
      $payload['id'] = generate_ulid();
      $this->Esai_model->insert_solution_essay($payload);
      $msg = 'Aktivitas Esai ditambahkan';
    }
    echo json_encode([
      'status' => 'success',
      'message' => $msg,
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

  public function delete_solution_essay($id = null)
  {
    $getData = $this->Esai_model->get_solution_essay($id);
    if (!$getData) {
      echo json_encode(['status'=>'error','message'=>'Gagal hapus Esai!', 'csrf_hash' => $this->security->get_csrf_hash()]);
      return;
    }

    if ($id) {
      $this->Esai_model->delete_solution_essay($id);
      $msg = 'Aktivitas Esai dihapus.';
      $status = 'success';
    }
    echo json_encode([
      'status' => $status,
      'message' => $msg,
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

  /*   CRUD KUIS EVALUASI  */
  public function get_evaluation_quizzes($class_id)
  {
    $data = $this->Esai_model->get_evaluation_quizzes($class_id);
    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($data));
  }

  public function save_evaluation_quiz()
  {
    $this->form_validation->set_rules('title', 'Judul Kuis', 'required');

    if ($this->form_validation->run() === FALSE) {
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
      return;
    }

    $id = $this->input->post('id');
    $payload = [
      'class_id' => $this->input->post('class_id'),
      'title' => $this->input->post('title'),
      'description' => $this->input->post('description')
    ];

    if ($id) {
      $getData = $this->Esai_model->get_evaluation_quiz($id);
      if (!$getData) {
        echo json_encode(['status'=>'error','message'=>'Kuis tidak ada!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
      }

      $this->Esai_model->update_evaluation_quiz($id, $payload);
      $msg = 'Kuis Evaluasi diperbarui';
    } else {
      $payload['id'] = generate_ulid();
      $this->Esai_model->insert_evaluation_quiz($payload);
      $msg = 'Kuis Evaluasi ditambahkan';
    }
    echo json_encode([
      'status' => 'success',
      'message' => $msg,
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

  public function delete_evaluation_quiz($id = null)
  {
    $getData = $this->Esai_model->get_evaluation_quiz($id);
    if (!$getData) {
      echo json_encode(['status'=>'error','message'=>'Gagal hapus kuis!', 'csrf_hash' => $this->security->get_csrf_hash()]);
      return;
    }

    if ($id) {
      $this->Esai_model->delete_evaluation_quiz($id);
      $msg = 'Kuis Evaluasi dihapus.';
      $status = 'success';
    }
    echo json_encode([
      'status' => $status,
      'message' => $msg,
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

  /**
   * Halaman utama untuk Tahap 5
   */
  public function tahap5($class_id = null)
  {
    if (!$class_id) {
        redirect('siswa/dashboard');
    }

    $data['title'] = 'Tahap 5 – Refleksi & Evaluasi Akhir';
    $data['class_id'] = $class_id;
    $data['user'] = $this->session->userdata(); // Data user yang login
    $data['url_name'] = 'siswa';
    
    // Load View
    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('siswa/pbl_tahap5', $data); // View khusus siswa
    $this->load->view('templates/footer');
  }

  // Get Data Rekap untuk Siswa
  public function get_my_recap($class_id)
  {
    $this->load->model('Refleksi_model');
    
    // Kita ambil data SEMUA siswa di kelas tersebut (untuk tabel leaderboard)
    // Logika privasi tombol "Lihat" akan ditangani di JavaScript
    $students = $this->Refleksi_model->getAllStudentScores($class_id);
    
    // Return JSON
    echo json_encode($students);
  }

  
}

/* End of file Pbl.php */
/* Location: ./application/controllers/Guru/Pbl.php */