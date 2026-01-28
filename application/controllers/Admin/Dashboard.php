<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		is_logged_in();
    $this->load->model('Dashboard_model', 'dashboard');
	}

	/*public function index()
	{
		$data['user'] = $this->session->userdata();
		$data['title'] = 'Dashboard Admin';

		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('dashboard/admin', $data);
		$this->load->view('templates/footer');
	}*/

  public function index()
  {
    $data['title'] = 'Dashboard Admin';
    $data['user'] = $this->session->userdata();

    /* ===== CARD STATISTIC ===== */
    $data['total_users']   = $this->dashboard->count_users();
    $data['total_teachers'] = $this->dashboard->count_teachers();
    $data['total_students'] = $this->dashboard->count_students();
    $data['total_classes']  = $this->dashboard->count_classes();
    $data['total_quizzes']  = $this->dashboard->count_pbl_quizzes();
    $data['total_exams']    = $this->dashboard->count_exams();

    /* ===== CHART DATA ===== */
    $data['user_per_role'] = $this->dashboard->chart_users_by_role();
    $data['class_per_year'] = $this->dashboard->chart_classes_per_year();
    $data['avg_scores']     = $this->dashboard->chart_average_scores();
    $data['teacher_student_ratio'] = $this->dashboard->chart_teacher_student();


    /* ===== TABLE SUMMARY ===== */
    $data['latest_classes'] = $this->dashboard->latest_classes();
    $data['latest_teachers'] = $this->dashboard->latest_teachers();
    $data['active_exams']    = $this->dashboard->active_exams();

    $data['avg_exam_uts_uas'] = $this->dashboard->chart_exam_uts_uas();

    $uts = $data['avg_exam_uts_uas']['data'][0];
    $uas = $data['avg_exam_uts_uas']['data'][1];

    if ($uas < $uts) {
        $data['exam_insight'] = 'Nilai UAS menurun dibanding UTS, perlu evaluasi akhir semester.';
    } else {
        $data['exam_insight'] = 'Nilai UAS meningkat, pembelajaran PBL berjalan efektif.';
    }

        
    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
    $this->load->view('dashboard/admin', $data);
    $this->load->view('templates/footer');
    
  }

    /**
     * Halaman index Kelas Admin.
     * Menggunakan view 'admin/sekolah_detail' sesuai permintaan,
     * meskipun isinya sekarang adalah manajemen kelas global.
     */
    public function classes()
    {
        $data['user'] = $this->session->userdata();
        $data['title'] = 'Kelola Kelas';
        
        // Ambil daftar guru untuk dropdown di modal tambah kelas
        $data['teachers'] = $this->Guru_model->get_all_with_user();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        // View dipindahkan dari guru/sekolah_detail ke admin/sekolah_detail
        $this->load->view('admin/sekolah_detail', $data); 
        $this->load->view('templates/footer');
    }

    public function getClassList()
    {
        // Ambil semua kelas beserta nama gurunya
        $data = $this->Guru_model->get_all_classes_with_teachers();
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    public function class_save()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Nama Kelas', 'required|trim');
        $this->form_validation->set_rules('teacher_id', 'Guru Pengampu', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'status' => 'error',
                'message' => validation_errors(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $user_id = $this->session->userdata('user_id'); // Admin ID (pembuat)
        $class_id = $this->input->post('id', TRUE);
        $name = $this->input->post('name', TRUE);
        $teacher_id = $this->input->post('teacher_id', TRUE);

        if ($class_id) {
            // --- LOGIKA UPDATE ---
            $payload = [
                'name' => $name,
                'teacher_id' => $teacher_id
                // Code tidak diubah saat update
            ];
            $this->Guru_model->update_class_admin($class_id, $payload);
            $msg = 'Kelas diperbarui';

        } else {
            // --- LOGIKA CREATE ---
            // Kode kelas otomatis (huruf besar, acak 6 karakter)
            $auto_code = strtoupper(substr(uniqid(), -6));
            
            $payload = [
                'id' => generate_ulid(),
                'user_id' => $user_id, // Disimpan sebagai history siapa yang buat
                'teacher_id' => $teacher_id,
                'name' => $name,
                'code' => $auto_code 
            ];
            $this->Guru_model->insert_class($payload);
            $msg = 'Kelas ditambahkan';
        }

        echo json_encode([
            'status' => 'success',
            'message' => $msg,
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }

    public function class_delete()
    {
        $class_id = $this->input->post('id', TRUE);

        if (!$class_id) {
            echo json_encode(['status'=>'error','message'=>'ID Kelas kosong.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        $deleted = $this->Guru_model->delete_class_admin($class_id);

        if ($deleted) {
            echo json_encode(['status' => 'success', 'message' => 'Kelas dihapus', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus kelas.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

/**
     * [PAGE] Detail Kelas untuk Admin
     * Admin bisa mengelola siswa (Add/Remove)
     */
    public function class_detail($class_id = null)
    {
        if (!$class_id) redirect('admin/dashboard/classes');
        
        // 1. Ambil detail kelas TANPA user_id (bypass check owner)
        $data['kelas'] = $this->Guru_model->get_class_details($class_id, null); // param ke-2 null = admin
        
        if (!$data['kelas']) {
            $this->session->set_flashdata('error', 'Kelas tidak ditemukan.');
            redirect('admin/dashboard/classes');
        }

        // 2. Data pendukung
        $data['siswa_list'] = $this->User_model->get_students_by_role_name('siswa');    
        $data['title'] = 'Detail Kelas: ' . $data['kelas']->name;
        $data['user'] = $this->session->userdata();

        // 3. Flag Permission (Admin = True)
        $data['can_manage_students'] = true; 
        $data['role_controller'] = 'admin'; // Untuk JS tahu harus panggil endpoint mana

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('guru/class_detail', $data); // Menggunakan view yang sama dengan guru
        $this->load->view('templates/footer');
    }

  /**
   * [AJAX LOAD] Mengambil daftar siswa UNTUK KELAS INI (untuk CrudHandler).
   */
  public function getStudentListForClass($class_id)
  {
    // Anda mungkin ingin validasi bahwa $class_id ini milik guru yg login
    $data = $this->Guru_model->get_students_in_class($class_id);
    $this->output
    ->set_content_type('application/json')
    ->set_output(json_encode($data));
}

  /**
   * [REVISI] Menambahkan siswa ke kelas (Logika INSERT).
   */
  public function add_student_to_class()
  {
    // 'student_id' dari form sebenarnya adalah 'user_id'
    $this->form_validation->set_rules('student_id', 'Siswa', 'required');
    $this->form_validation->set_rules('class_id', 'Kelas', 'required');

    if ($this->form_validation->run() === FALSE) {
        echo json_encode(['status'=>'error','message'=>validation_errors(), 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
    }

    $user_id_from_form = $this->input->post('student_id', TRUE);
    $class_id = $this->input->post('class_id', TRUE);
    
    // Panggil model INSERT yang baru
    $success = $this->Guru_model->add_student_to_class($user_id_from_form, $class_id);

    if ($success) {
        $msg = 'Siswa berhasil ditambahkan ke kelas.';
        $status = 'success';
    } else {
        $msg = 'Gagal menambahkan siswa (mungkin siswa sudah ada di kelas lain).';
        $status = 'error';
    }

    echo json_encode([
        'status' => $status,
        'message' => $msg,
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}

  /**
   * [REVISI] Mengeluarkan siswa dari kelas (Logika DELETE).
   */
  public function remove_student_from_class()
  {
      // 'id' yang dikirim CrudHandler adalah 'students.id'
      $student_id = $this->input->post('id', TRUE); 
      $class_id = $this->input->post('class_id', TRUE); // (Opsional, untuk keamanan)
      
      if (!$student_id) {
          echo json_encode(['status'=>'error','message'=>'ID Siswa kosong.', 'csrf_hash' => $this->security->get_csrf_hash()]);
          return;
      }
      
      // Panggil model DELETE yang baru
      $this->Guru_model->remove_student_from_class($student_id, $class_id);
      
      echo json_encode([
          'status' => 'success',
          'message' => 'Siswa dikeluarkan dari kelas.',
          'csrf_hash' => $this->security->get_csrf_hash()
      ]);
  }

	// ========== Guru =============
  public function teachers()
  {
		// $data['teachers'] = $this->Guru_model->get_all_with_user_and_school();
      $data['teachers'] = $this->Guru_model->get_all_with_user();
      $data['user'] = $this->session->userdata();
      $data['title'] = 'Kelola Guru';

      $this->load->view('templates/header', $data);
      $this->load->view('templates/sidebar');
      $this->load->view('admin/teachers/index', $data);
      $this->load->view('templates/footer');
  }

	// Tambahkan ini di admin/dashboard.php
  public function getTeacherList()
  {
      $data = $this->Guru_model->get_all_with_user();
	  // Kirim sebagai JSON
      $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($data));
  }

  public function teacher_save()
  {
      $this->load->library('form_validation');
      $this->form_validation->set_rules('username','Username','required');
      $this->form_validation->set_rules('name','Nama','required|trim');

      if ($this->form_validation->run() === FALSE) {
       echo json_encode(['status'=>'error','message'=>validation_errors(), 'csrf_hash' => $this->security->get_csrf_hash()]);
       return;
   }
	    $id = $this->input->post('id', TRUE); // teacher id for update
	    $username = $this->input->post('username', TRUE);
	    $email = $this->input->post('email', TRUE);
	    $password = $this->input->post('password', TRUE);

	    if ($id) {
	        // update teacher info (update user too)
	    	$teacher = $this->Guru_model->get($id);
	    	if (!$teacher) { echo json_encode(['status'=>'error','message'=>'Guru tidak ditemukan']); return; }
	    	$this->User_model->update($teacher->user_id, ['name'=>$this->input->post('name', TRUE), 'email'=>$email]);

	    	// $this->Guru_model->update($id, ['school_id'=>$school_id]);
	    	echo json_encode(['status'=>'success','message'=>'Guru diperbarui', 'csrf_hash' => $this->security->get_csrf_hash()]);
	    	return;
	    } else {

	      // create user + teacher
	    	$uid = generate_ulid();
	    	$user_payload = [
	    		'id' => $uid,
	    		'username' => $username,
	    		'password' => password_hash($password ?: 'password', PASSWORD_DEFAULT),
	    		'role_id' => $this->User_model->get_role_id_by_name('guru'),
	    		'name' => $this->input->post('name', TRUE),
	    		'email' => $email
	    	];
	    	$this->User_model->insert($user_payload);
	    	$teacher_payload = [
	    		'id' => generate_ulid(),
	    		'user_id' => $uid,
	    	];
	    	$this->Guru_model->insert($teacher_payload);
	    	echo json_encode(['status'=>'success','message'=>'Guru ditambahkan', 'csrf_hash' => $this->security->get_csrf_hash()]);
	    	return;
	    }
   }

   public function teacher_delete()
   {
    $id = $this->input->post('id', TRUE);
    $teacher = $this->Guru_model->get($id);
    if (!$teacher) {
     echo json_encode([
      'status' => 'error',
      'message' => 'Guru tidak ditemukan',
            'csrf_hash' => $this->security->get_csrf_hash() // <-- TAMBAHKAN INI
        ]);
     return;
 }

 $this->User_model->delete($teacher->user_id);
 echo json_encode([
     'status' => 'success',
     'message' => 'Guru dihapus',
        'csrf_hash' => $this->security->get_csrf_hash() // <-- TAMBAHKAN INI
    ]);
}

	// ========== Murid =============
	  /**
     * Menampilkan halaman 'Kelola Murid'.
     * Data tabel akan dimuat via AJAX.
     */
    public function students()
    {
        // $data['students'] tidak diperlukan lagi, data dimuat oleh getStudentList()
        $data['user'] = $this->session->userdata();
        $data['title'] = 'Kelola Murid';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/students/index', $data); // View baru/sederhana
        $this->load->view('templates/footer');
    }

    /**
     * REFAKTOR: Method student_save()
     * - Sekarang hanya beroperasi di tabel 'users'.
     * - 'id' sekarang merujuk langsung ke 'users.id'.
     * - Menambahkan role_id 'siswa' saat CREATE.
     * - Menambahkan pengecekan role_id saat UPDATE.
     */
    public function student_save()
    {
        $this->load->library('form_validation');
        $id = $this->input->post('id', TRUE); // Ini adalah users.id
        
        $this->form_validation->set_rules('name','Nama','required|trim');
        
        // Aturan validasi username/email
        if (!$id) {
            // --- Mode CREATE ---
            $this->form_validation->set_rules('username','Username','required|trim|is_unique[users.username]', [
                'is_unique' => 'Username ini sudah digunakan.'
            ]);
            $this->form_validation->set_rules('email','Email','trim|valid_email|is_unique[users.email]', [
                'is_unique' => 'Email ini sudah digunakan.'
            ]);
        } else {
            // --- Mode UPDATE ---
            $user = $this->User_model->get($id);
            if (!$user) {
                echo json_encode(['status'=>'error','message'=>'User tidak ditemukan', 'csrf_hash' => $this->security->get_csrf_hash()]);
                return;
            }
            
            // Validasi email HANYA jika diubah
            if ($this->input->post('email') != $user->email) {
             $this->form_validation->set_rules('email','Email','trim|valid_email|is_unique[users.email]', [
                'is_unique' => 'Email ini sudah digunakan.'
            ]);
         }
     }

     if ($this->form_validation->run() === FALSE) {
        echo json_encode([
            'status'=>'error',
            'message'=>validation_errors(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
        return;
    }

    $name = $this->input->post('name', TRUE);
    $email = $this->input->post('email', TRUE);
        // class_id DIHAPUS

        // Ambil Role ID 'siswa'
    $role_id_siswa = $this->User_model->get_role_id_by_name('siswa'); 
    if (!$role_id_siswa) {
        echo json_encode(['status'=>'error','message'=>'Role "siswa" tidak ditemukan di database.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
    }

    if ($id) {
            // --- LOGIKA UPDATE ---
        $user = $this->User_model->get($id);
        
            // Pengecekan keamanan: pastikan user ada dan role-nya 'siswa'
        if (!$user || $user->role_id != $role_id_siswa) {
         echo json_encode(['status'=>'error','message'=>'Akses ditolak atau siswa tidak ditemukan', 'csrf_hash' => $this->security->get_csrf_hash()]);
         return;
     }
     
     $user_payload = ['name' => $name, 'email' => $email];
     $this->User_model->update($id, $user_payload);
     $msg = 'Siswa diperbarui';

 } else {
            // --- LOGIKA CREATE ---
    $username = $this->input->post('username', TRUE);
    $password = $this->input->post('password', TRUE);

    $user_payload = [
        'id' => generate_ulid(),
        'username' => $username,
        'password' => password_hash($password ?: 'password', PASSWORD_DEFAULT),
                'role_id' => $role_id_siswa, // <-- PENTING
                'name' => $name,
                'email' => $email
            ];
            $this->User_model->insert($user_payload);
            $msg = 'Siswa ditambahkan';
        }
        
        echo json_encode([
            'status'=>'success',
            'message'=> $msg,
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }

    /**
     * REFAKTOR: Method student_delete()
     * - 'id' adalah users.id.
     * - Menambahkan pengecekan role_id sebelum delete.
     */
    public function student_delete()
    {
        $id = $this->input->post('id', TRUE); // users.id
        $user = $this->User_model->get($id);
        $role_id_siswa = $this->User_model->get_role_id_by_name('siswa');
        
        // Pengecekan keamanan: pastikan user ada dan role-nya 'siswa'
        if (!$user || $user->role_id != $role_id_siswa) {
            echo json_encode([
                'status'=>'error',
                'message'=>'Siswa tidak ditemukan atau akses ditolak',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }
        
        $this->User_model->delete($id); // Hapus user
        
        echo json_encode([
            'status'=>'success',
            'message'=>'Siswa dihapus',
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }
    
    /**
     * REFAKTOR: Endpoint untuk 'fetch' data
     * - Mengambil data dari User_model berdasarkan role 'siswa'.
     */
    public function getStudentList()
    {
        // Gunakan method baru di User_model
        $data = $this->User_model->get_by_role_name('siswa');
        
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Admin/Dashboard.php */