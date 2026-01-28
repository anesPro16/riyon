<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		is_logged_in();
	}

	public function index()
	{
		$data['title'] = 'Dashboard Guru';
		$data['user'] = $this->session->userdata();
		$user_id = $this->session->userdata('user_id');

    // REVISI: Langsung ambil daftar kelas, tidak ada sekolah lagi
    $data['kelas_list'] = $this->Guru_model->get_all_classes($user_id);

    // View khusus guru
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar');
		$this->load->view('guru/index', $data);
		$this->load->view('templates/footer');
	}

  public function dashboard_stats()
    {
        $user_id = $this->session->userdata('user_id');

        // 1. Ambil ID Kelas yang diajar oleh guru ini
        // Asumsi: Guru_model->get_all_classes mengembalikan array object kelas
        $classes = $this->Guru_model->get_all_classes($user_id);
        
        if (empty($classes)) {
            echo json_encode(['status' => 'empty']);
            return;
        }

        $class_ids = array_column($classes, 'id');

        // ---------------------------------------------------------
        // A. Statistik Cards
        // ---------------------------------------------------------

        // 1. Total Siswa (Mengambil dari tabel students/users via class_id)
        // Asumsi ada tabel 'students' yang menghubungkan user_id (siswa) dengan class_id
        $this->db->where_in('class_id', $class_ids);
        $total_siswa = $this->db->count_all_results('students'); 

        // 2. Perlu Pemeriksaan (Essay Grade IS NULL)
        // Join: Submission -> Essay -> Class
        $this->db->select('count(subs.id) as total');
        $this->db->from('essay_submissions subs');
        $this->db->join('essays essay', 'essay.essay_id = subs.essay_id'); // Asumsi nama tabel essays
        $this->db->where_in('essay.class_id', $class_ids);
        $this->db->where('subs.grade', NULL);
        $pending_grading = $this->db->get()->row()->total;

        // 3. Rata-rata Nilai Kuis (Seluruh Kelas)
        // Join: Quiz Result -> Quiz -> Class
        $this->db->select_avg('qr.score');
        $this->db->from('quiz_results qr');
        $this->db->join('quizzes q', 'q.quiz_id = qr.quiz_id'); // Asumsi nama tabel quizzes
        $this->db->where_in('q.class_id', $class_ids);
        $avg_quiz = $this->db->get()->row()->score;

        // ---------------------------------------------------------
        // B. Data Chart: Sebaran Nilai Kuis (Bar Chart)
        // ---------------------------------------------------------
        $this->db->select('
            SUM(CASE WHEN qr.score <= 50 THEN 1 ELSE 0 END) as range_e,
            SUM(CASE WHEN qr.score > 50 AND qr.score <= 70 THEN 1 ELSE 0 END) as range_c,
            SUM(CASE WHEN qr.score > 70 AND qr.score <= 85 THEN 1 ELSE 0 END) as range_b,
            SUM(CASE WHEN qr.score > 85 THEN 1 ELSE 0 END) as range_a
        ');
        $this->db->from('quiz_results qr');
        $this->db->join('quizzes q', 'q.quiz_id = qr.quiz_id');
        $this->db->where_in('q.class_id', $class_ids);
        $quiz_dist = $this->db->get()->row();

        // ---------------------------------------------------------
        // C. Data Chart: Status Essay (Doughnut Chart)
        // ---------------------------------------------------------
        $this->db->select('
            SUM(CASE WHEN subs.grade IS NOT NULL THEN 1 ELSE 0 END) as graded,
            SUM(CASE WHEN subs.grade IS NULL THEN 1 ELSE 0 END) as pending
        ');
        $this->db->from('essay_submissions subs');
        $this->db->join('essays essay', 'essay.essay_id = subs.essay_id');
        $this->db->where_in('essay.class_id', $class_ids);
        $essay_stats = $this->db->get()->row();

        // ---------------------------------------------------------
        // D. Tabel Prioritas (5 Submission Terbaru yg belum dinilai)
        // ---------------------------------------------------------
        $this->db->select('subs.id, subs.created_at, u.name as student_name, essay.essay_id, essay.title as task_title, essay.class_id');
        $this->db->from('essay_submissions subs');
        $this->db->join('essays essay', 'essay.essay_id = subs.essay_id');
        $this->db->join('users u', 'u.id = subs.user_id');
        $this->db->where_in('essay.class_id', $class_ids);
        $this->db->where('subs.grade', NULL);
        $this->db->order_by('subs.created_at', 'DESC');
        $this->db->limit(5);
        $priority_list = $this->db->get()->result();

        // Response JSON
        echo json_encode([
            'status' => 'success',
            'cards' => [
                'students' => $total_siswa,
                'pending'  => $pending_grading,
                'avg_quiz' => number_format($avg_quiz ?? 0, 1)
            ],
            'charts' => [
                'quiz_dist' => [
                    $quiz_dist->range_e ?? 0, 
                    $quiz_dist->range_c ?? 0, 
                    $quiz_dist->range_b ?? 0, 
                    $quiz_dist->range_a ?? 0
                ],
                'essay_stats' => [
                    $essay_stats->graded ?? 0,
                    $essay_stats->pending ?? 0
                ]
            ],
            'priority_list' => $priority_list
        ]);
    }


  public function kelas()
  {
    $data['title'] = 'Kelas';
    $data['user']  = $this->session->userdata();
    
    $user_id = $this->session->userdata('user_id');
    $role_id = $this->session->userdata('role_id');

    $role = $this->session->userdata('role');
    if ($role != 'Guru') redirect('guru');

    $data['classes'] = $this->Murid_model->get_classes_by_teacher($user_id);
    $data['role_label'] = 'Pengajar';
    $data['url_name'] = 'guru';

    // Hitung statistik sederhana untuk Info Card Header
    $data['total_kelas'] = count($data['classes']);
    
    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
    $this->load->view('dashboard/kelas', $data); // View reusable
    $this->load->view('templates/footer');
  }

	/**
   * Halaman detail untuk satu sekolah, menampilkan kelas-kelas guru di sekolah tsb.
   * @param string $school_id ID dari sekolah yang akan dilihat
   */
	public function detail($school_id = null)
	{
		if (!$school_id) {
          // Redirect jika tidak ada ID sekolah
			redirect('guru/sekolah');
		}

		$user_id = $this->session->userdata('user_id');

      // Ambil detail sekolah
		$data['sekolah'] = $this->Guru_model->get_school_by_id($school_id);

		if (!$data['sekolah']) {
          // Jika sekolah tidak ditemukan, kembalikan ke index
			redirect('guru/sekolah');
		}

    // Set judul halaman berdasarkan nama sekolah
    $data['title'] = 'Kelas di ' . $data['sekolah']->name; // Menggunakan 'name' dari skema
    $data['user'] = $this->session->userdata();

    // Ambil daftar kelas guru di sekolah ini
    // $data['kelas_list'] = $this->Guru_model->get_kelas_by_guru_dan_sekolah($user_id, $school_id);

    // Muat view
    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
    $this->load->view('guru/sekolah_detail', $data); // View detail baru kita
    $this->load->view('templates/footer');
  }

  /**
   * [AJAX LOAD] Mengambil daftar kelas untuk CrudHandler.
   */
  public function getClassList($school_id)
  {
  	$user_id = $this->session->userdata('user_id');
  	$data = $this->Guru_model->get_kelas_by_guru_dan_sekolah($user_id, $school_id);
  	
  	$this->output
  	->set_content_type('application/json')
  	->set_output(json_encode($data));
  }

  /**
   * [AJAX SAVE] Menyimpan (Create/Update) data kelas.
   */
  public function class_save()
  {
  	$this->form_validation->set_rules('name', 'Nama Kelas', 'required|trim');
  	
  	if ($this->form_validation->run() === FALSE) {
  		echo json_encode([
  			'status' => 'error',
  			'message' => validation_errors(),
  			'csrf_hash' => $this->security->get_csrf_hash()
  		]);
  		return;
  	}

  	$user_id = $this->session->userdata('user_id');
  	$class_id = $this->input->post('id', TRUE);
    $name = $this->input->post('name', TRUE);
    $code = $this->input->post('code', TRUE);

    if ($class_id) {
        // --- LOGIKA UPDATE ---
    	$payload = [
    		'name' => $name,
    		'code' => $code
            // school_id dan user_id tidak boleh diubah
    	];
    	$this->Guru_model->update_class($class_id, $user_id, $payload);
    	$msg = 'Kelas diperbarui';

    } else {
      // --- LOGIKA CREATE ---
    	$payload = [
        'id' => generate_ulid(),
        'user_id' => $user_id,
        'name' => $name,
        'code' => $code ?: strtoupper(substr(uniqid(), -6)) // Kode acak jika kosong
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

  /**
   * [AJAX DELETE] Menghapus data kelas.
   */
  public function class_delete()
  {
  	$user_id = $this->session->userdata('user_id');
  	$class_id = $this->input->post('id', TRUE);

  	if (!$class_id) {
  		echo json_encode(['status'=>'error','message'=>'ID Kelas kosong.', 'csrf_hash' => $this->security->get_csrf_hash()]);
  		return;
  	}

  	$deleted = $this->Guru_model->delete_class($class_id, $user_id);

  	if ($deleted) {
  		$msg = 'Kelas dihapus';
  		$status = 'success';
  	} else {
  		$msg = 'Gagal menghapus kelas (mungkin tidak ditemukan atau bukan milik Anda).';
  		$status = 'error';
  	}
  	
  	echo json_encode([
  		'status' => $status,
  		'message' => $msg,
  		'csrf_hash' => $this->security->get_csrf_hash()
  	]);
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
        $data['title'] = 'Halaman Detail Kelas';
        $data['user'] = $this->session->userdata();

        // 3. Flag Permission (Admin = True)
        $data['can_manage_students'] = false; 
        $data['role_controller'] = 'guru'; // Untuk JS tahu harus panggil endpoint mana

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

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Guru/Dashboard.php */