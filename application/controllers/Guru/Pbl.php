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
    // $data['title'] = 'Tahap 1 - Orientasi Masalah';
    $data['title'] = 'Materi';
    $data['url_name'] = 'guru';
    $data['class_id'] = $class_id;
    $data['user'] = $this->session->userdata();

    $role_id = $this->session->userdata('role_id');    
    $data['is_admin_or_guru'] = $this->User_model->check_is_teacher($role_id);

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
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
    $data['url_name'] = 'guru';
    $role_id = $this->session->userdata('role_id');    
    $data['is_admin_or_guru'] = $this->User_model->check_is_teacher($role_id);

    $data['subjects'] = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'PPKN'];

	  $this->load->view('templates/header', $data);
	  $this->load->view('templates/sidebar');
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
	  $quiz_id = $this->input->post('quiz_id');
	  $payload = [
	      'class_id' => $this->input->post('class_id'),
	      'title' => $this->input->post('title'),
	      'subjects' => $this->input->post('subjects')
	  ];
	  if ($quiz_id) {
      $getQuiz = $this->Kuis_model->get_quiz_by_id($quiz_id);
      if (!$getQuiz) {
        echo json_encode(['status'=>'error','message'=>'Kuis tidak ada!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
      }
	    $this->Kuis_model->update_quiz($quiz_id, $payload);
	    $msg = 'Kuis diperbarui';
	  } else {
	    $payload['quiz_id'] = generate_ulid();
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
    $quiz_id = $this->input->post('id'); // Ambil dari POST, bukan URL
    
    $getQuiz = $this->Kuis_model->get_quiz_by_id($quiz_id);
    if (!$getQuiz) {
      echo json_encode(['status'=>'error','message'=>'Gagal hapus Kuis!', 'csrf_hash' => $this->security->get_csrf_hash()]);
      return;
    }

    $this->Kuis_model->delete_quiz($quiz_id);

    echo json_encode([
      'status' => 'success',
      'message' => 'kuis dihapus',
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

    $data['url_name'] = 'guru';
    $role_id = $this->session->userdata('role_id');    
    $data['is_admin_or_guru'] = $this->User_model->check_is_teacher($role_id);

    $data['subjects'] = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'PPKN'];

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
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
    $this->form_validation->set_rules('instruction', 'Intruksi', 'required');


    if ($this->form_validation->run() === FALSE) {
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
      return;
    }

    $id = $this->input->post('id');
    $payload = [
      'class_id'    => $this->input->post('class_id'),
      'title'       => $this->input->post('title'),
      'subjects'    => $this->input->post('subjects'),
      'instruction' => $this->input->post('instruction'),
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
      $payload['observation_id'] = generate_ulid();
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
    $data['url_name'] = 'guru';
    $role_id = $this->session->userdata('role_id');    
    $data['is_admin_or_guru'] = $this->User_model->check_is_teacher($role_id);

    $data['subjects'] = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'PPKN'];

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
      'subjects' => $this->input->post('subjects')
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
      $payload['essay_id'] = generate_ulid();
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
  

  /**
   * Halaman utama untuk Tahap 5
   */
  public function tahap5($class_id = null)
  {
    if (!$class_id) {
      redirect('guru/dashboard');
    }

    $data['title'] = 'Tahap 5 – Refleksi Akhir';
    $data['class_id'] = $class_id;
    $data['user'] = $this->session->userdata();
    $data['url_name'] = 'guru';
    $role_id = $this->session->userdata('role_id');    
    $data['is_admin_or_guru'] = $this->User_model->check_is_teacher($role_id);

    $data['exam_subjects'] = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'PPKN'];

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
    $this->load->view('guru/pbl_tahap5', $data);
    $this->load->view('templates/footer');
  }
  
}

/* End of file Pbl.php */
/* Location: ./application/controllers/Guru/Pbl.php */