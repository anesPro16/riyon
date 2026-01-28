<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exam extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Exam_model');
    $this->load->model('User_model');
    $this->load->helper(['security']);
    date_default_timezone_set('Asia/Jakarta');
  }

  /* =========================
   * GURU – DAFTAR Kelas
   * ========================= */
  public function index()
  {
    $user = $this->session->userdata();
    $role_id = $user['role_id'];
    $user_id = $user['user_id'];

    if ($user['role'] === 'Siswa') {
    	redirect('exam/student_list','refresh');
    }

    // 1. Validasi apakah user adalah Guru
    if (!$this->User_model->check_is_teacher($role_id)) {
        show_error('Akses ditolak. Halaman ini khusus Guru.', 403);
    }

    // 2. Ambil Data Kelas milik Guru tersebut
    // Kita asumsikan Guru_model menghandle logika pengambilan kelas berdasarkan user_id guru
    $data = [
        'title'      => 'Pilih Kelas Ujian',
        'user'       => $user,
        'kelas_list' => $this->Guru_model->get_all_classes($user_id), 
        'url_name'   => 'guru'
    ];

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('exam/select_class', $data); // View baru untuk daftar kelas
    $this->load->view('templates/footer');
  }

  /* =========================
   * HALAMAN 2: MANAJEMEN UJIAN (CRUD)
   * Masuk ke fitur ujian berdasarkan class_id
   * ========================= */
  public function management($class_id = null)
  {
    // Jika class_id kosong, kembalikan ke halaman pilih kelas
    if (!$class_id) redirect('guru/exam');

    $user = $this->session->userdata();
    $role_id = $user['role_id'];

    // 1. Validasi Guru
    if (!$this->User_model->check_is_teacher($role_id)) {
        show_error('Akses ditolak', 403);
    }

    // 2. Ambil ID Teacher dari User ID
    $teacher_id = $this->Exam_model->getTeacherId($user['user_id']);

    // 3. Validasi apakah Kelas ini benar milik Guru tersebut
    if (!$this->Exam_model->is_teacher_class($class_id, $teacher_id)) {
        show_error('Bukan kelas Anda atau Kelas tidak ditemukan.', 403);
    }

    // 4. Data Mapel untuk Dropdown
    $subjects = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'PPKN'];

    $data = [
        'title'    => 'Manajemen Ujian',
        'class_id' => $class_id, // ID Kelas yang sedang dikelola
        'user'     => $user,
        'subjects' => $subjects, 
        'url_name' => 'guru'
    ];

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('exam/index', $data); // View lama (Tabel CRUD Ujian)
    $this->load->view('templates/footer');
  }

  private function _auto_update_status($class_id)
  {
    // Set is_active = 0 jika end_time < waktu sekarang
    $now = date('Y-m-d H:i:s');
    $this->db->where('class_id', $class_id)
	    ->where('end_time <', $now)
	    ->where('is_active', 1)
	    ->update('exams', ['is_active' => 0]);
  }

  /* =========================
   * AJAX – GET UJIAN
   * ========================= */
  public function get_exams($class_id)
  {
  	$this->_auto_update_status($class_id);

    $data = $this->Exam_model->get_by_class($class_id);

    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($data));
  }

  /* =========================
   * AJAX – SIMPAN UJIAN
   * ========================= */
  public function save()
{
    // 1. Ambil Input
    $class_id   = $this->input->post('class_id', true);
    $start_time = $this->input->post('start_time', true);
    $end_time   = $this->input->post('end_time', true);
    $exam_name  = $this->input->post('exam_name', true); // Diperlukan untuk validasi
    $type       = $this->input->post('type', true);      // Diperlukan untuk validasi
    $id         = $this->input->post('exam_id');
    
    // Helper Tanggal
    $now_str = date('Y-m-d H:i:s');
    $new_date = new DateTime($start_time); // Object DateTime untuk perhitungan selisih

    // 2. Validasi Dasar Waktu (Create Only)
    if (!$id && $start_time < $now_str) {
        echo json_encode(['status'=>'error', 'message'=>'Waktu mulai tidak boleh kurang dari waktu sekarang!', 'csrf_hash'=>$this->security->get_csrf_hash()]);
        return;
    }

    if ($end_time <= $start_time) {
        echo json_encode(['status'=>'error', 'message'=>'Waktu selesai harus lebih besar dari waktu mulai!', 'csrf_hash'=>$this->security->get_csrf_hash()]);
        return;
    }

    // 3. Validasi Kepemilikan Kelas
    $user = $this->session->userdata();
    $teacher_id = $this->Exam_model->getTeacherId($user['user_id']);
    if (!$this->Exam_model->is_teacher_class($class_id, $teacher_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Akses ditolak', 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
    }

    // ============================================================
    // 4. VALIDASI LOGIKA BISNIS (UTS/UAS INTERVAL)
    // ============================================================
    
    // Hanya jalankan validasi ini jika input Mata Pelajaran & Tipe ada
    // Dan ini sebaiknya hanya untuk PEMBUATAN BARU (Insert) agar tidak mengganggu Edit
    if (!$id && $exam_name && $type) {
        
        // A. KASUS JIKA INGIN MEMBUAT "UAS"
        if ($type === 'UAS') {
            // Cari data UTS terakhir untuk mapel ini di kelas ini
            $last_uts = $this->Exam_model->get_latest_exam($class_id, $exam_name, 'UTS');

            // Syarat 1: Harus ada data UTS dulu
            if (!$last_uts) {
                echo json_encode([
                    'status' => 'error', 
                    'message' => "Gagal! Belum ada data UTS untuk mata pelajaran $exam_name. Buat UTS terlebih dahulu.", 
                    'csrf_hash'=>$this->security->get_csrf_hash()
                ]);
                return;
            }

            // Syarat 2: Jarak UTS ke UAS harus > 40 Hari
            $uts_date = new DateTime($last_uts->start_time);
            $interval = $uts_date->diff($new_date);
            $days_diff = $interval->days; // Selisih hari absolut
            
            // Pastikan tanggal baru setelah tanggal lama DAN selisih > 40
            if ($new_date <= $uts_date || $days_diff <= 40) {
                echo json_encode([
                    'status' => 'error', 
                    'message' => "Gagal! Jarak UAS harus lebih dari 40 hari setelah UTS (Terakhir: ".date('d-m-Y', strtotime($last_uts->start_time)).").", 
                    'csrf_hash'=>$this->security->get_csrf_hash()
                ]);
                return;
            }
        }

        // B. KASUS JIKA INGIN MEMBUAT "UTS" (Cek Siklus Semester Baru)
        if ($type === 'UTS') {
            // Cari data UTS sebelumnya (Semester lalu/Ujian sebelumnya)
            $last_uts = $this->Exam_model->get_latest_exam($class_id, $exam_name, 'UTS');

            if ($last_uts) {
                // Syarat 3: Jarak antar UTS harus > 70 Hari
                $prev_uts_date = new DateTime($last_uts->start_time);
                $interval = $prev_uts_date->diff($new_date);
                $days_diff = $interval->days;

                if ($new_date <= $prev_uts_date || $days_diff <= 70) {
                    echo json_encode([
                        'status' => 'error', 
                        'message' => "Gagal! Jarak antar UTS untuk mapel yang sama harus lebih dari 70 hari (Terakhir: ".date('d-m-Y', strtotime($last_uts->start_time)).").", 
                        'csrf_hash'=>$this->security->get_csrf_hash()
                    ]);
                    return;
                }
            }
        }
    }
    // ============================================================

    $payload = [
        'class_id'   => $class_id,
        'exam_name'  => $exam_name,
        'type'       => $type,
        'start_time' => $start_time,
        'end_time'   => $end_time,
        'is_active'  => 1
    ];

    if ($id) {
        $exam = $this->Exam_model->get_by_id($id);
        if (!$exam) {
            echo json_encode(['status'=>'error','message'=>'Ujian tidak ditemukan','csrf_hash'=>$this->security->get_csrf_hash()]);
            return;
        }
        $this->Exam_model->update($id, $payload);
        $msg = 'Ujian diperbarui';
    } else {
        $payload['exam_id'] = generate_ulid();
        $this->Exam_model->insert($payload);
        $msg = 'Ujian ditambahkan';
    }

    echo json_encode([
        'status' => 'success',
        'message' => $msg,
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}


  /* =========================
   * AJAX – HAPUS
   * ========================= */
  public function delete()
  {
    $id = $this->input->post('id');

    $exam = $this->Exam_model->get_by_id($id);
    if (!$exam) {
      echo json_encode(['status'=>'error','message'=>'Data tidak ada','csrf_hash'=>$this->security->get_csrf_hash()]);
      return;
    }

    $this->Exam_model->delete($id);

    echo json_encode([
      'status' => 'success',
      'message' => 'Ujian dihapus',
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

  public function questions($exam_id = null)
{
    if (!$exam_id) show_404();

    $user = $this->session->userdata();
    $teacher_id = $this->Exam_model->getTeacherId($user['user_id']);

    $exam = $this->Exam_model->get_exam_with_class($exam_id);
    if (!$exam) show_404();

    // validasi ujian milik guru
    if (!$this->Exam_model->is_teacher_class($exam->class_id, $teacher_id)) {
        show_error('Akses ditolak', 403);
    }

    $data = [
        'title' => 'Soal Ujian',
        'exam'  => $exam,
        'user'     => $user,
    ];

    $this->load->view('templates/header', $data);
    $this->load->view('exam/questions', $data);
    $this->load->view('templates/footer');
}

public function get_questions($exam_id)
{
    $data = $this->Exam_model->get_questions($exam_id);
    echo json_encode($data);
}

public function save_question()
{
    $id = $this->input->post('id');
    $payload = [
        'exam_id' => $this->input->post('exam_id'),
        'question' => $this->input->post('question', true),
        'option_a' => $this->input->post('option_a', true),
        'option_b' => $this->input->post('option_b', true),
        'option_c' => $this->input->post('option_c', true),
        'option_d' => $this->input->post('option_d', true),
        'correct_answer' => $this->input->post('correct_answer', true)
    ];

    if ($id) {
        $this->Exam_model->update_question($id, $payload);
        $msg = 'Soal diperbarui';
    } else {
        $payload['id'] = generate_ulid();
        $this->Exam_model->insert_question($payload);
        $msg = 'Soal ditambahkan';
    }

    echo json_encode([
        'status' => 'success',
        'message' => $msg,
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}

public function delete_question()
{
    $id = $this->input->post('id');
    $this->Exam_model->delete_question($id);

    echo json_encode([
        'status' => 'success',
        'message' => 'Soal dihapus',
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}

	public function save_questions_batch()
{
    // Validasi Session & Guru (Sama seperti sebelumnya)
    $user = $this->session->userdata();
    $teacher_id = $this->Exam_model->getTeacherId($user['user_id']);
    
    $exam_id = $this->input->post('exam_id');
    $exam = $this->Exam_model->get_exam_with_class($exam_id);
    
    if (!$exam || !$this->Exam_model->is_teacher_class($exam->class_id, $teacher_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
        return;
    }

    // Ambil data array dari form
    $questions = $this->input->post('question'); // Ini array
    $opt_a = $this->input->post('option_a');
    $opt_b = $this->input->post('option_b');
    $opt_c = $this->input->post('option_c');
    $opt_d = $this->input->post('option_d');
    $correct = $this->input->post('correct_answer');

    $batch_data = [];
    $timestamp = date('Y-m-d H:i:s');

    if (!empty($questions)) {
        foreach ($questions as $key => $val) {
            // Pastikan soal tidak kosong
            if (trim($val) == '') continue;

            $batch_data[] = [
                'id' => generate_ulid(), // Pastikan helper generate_ulid dipanggil unik tiap iterasi
                'exam_id' => $exam_id,
                'question' => $val,
                'option_a' => $opt_a[$key],
                'option_b' => $opt_b[$key],
                'option_c' => $opt_c[$key],
                'option_d' => $opt_d[$key],
                'correct_answer' => $correct[$key],
                'created_at' => $timestamp
            ];
        }
    }

    if (count($batch_data) > 0) {
        $this->Exam_model->insert_batch_questions($batch_data);
        $msg = count($batch_data) . ' soal berhasil ditambahkan.';
        $status = 'success';
    } else {
        $msg = 'Tidak ada data soal yang disimpan.';
        $status = 'error';
    }

    echo json_encode([
        'status' => $status,
        'message' => $msg,
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}

	/* =========================
   * GURU – MONITORING & HASIL
   * ========================= */

	public function result($exam_id = null)
	{
	    if (!$exam_id) show_404();
	    
	    $user = $this->session->userdata();
	    // Validasi guru... (sesuaikan dengan kode Anda)

	    $exam = $this->Exam_model->get_exam_with_class($exam_id);
	    $students = $this->Exam_model->get_exam_results($exam->class_id, $exam_id);

	    // --- LOGIKA STATISTIK ---
	    $total_score = 0;
	    $count_finished = 0;
	    $max_score = 0;
	    $min_score = 100;

	    foreach ($students as $s) {
	        if ($s->attempt_status == 'finished') {
	            $val = floatval($s->score);
	            $total_score += $val;
	            $count_finished++;
	            if ($val > $max_score) $max_score = $val;
	            if ($val < $min_score) $min_score = $val;
	        }
	    }

	    $avg_score = $count_finished > 0 ? ($total_score / $count_finished) : 0;
	    if ($count_finished == 0) $min_score = 0; // Reset jika belum ada yg selesai

	    $data = [
	        'title'    => 'Hasil Ujian: ' . $exam->exam_name,
	        'exam'     => $exam,
	        'students' => $students,
	        // Data Statistik dikirim ke View
	        'stats' => [
	            'avg' => number_format($avg_score, 1),
	            'max' => number_format($max_score, 1),
	            'min' => number_format($min_score, 1),
	            'total_students' => count($students),
	            'finished_count' => $count_finished
	        ]
	    ];

	    $this->load->view('templates/header', $data);
	    $this->load->view('exam/result_list', $data);
	    $this->load->view('templates/footer');
	}

	// FUNGSI BARU: Export ke Excel (Native PHP Header)
	public function export_result($exam_id = null)
	{
	    if (!$exam_id) show_404();
	    
	    // Ambil Data
	    $exam = $this->Exam_model->get_exam_with_class($exam_id);
	    $students = $this->Exam_model->get_exam_results($exam->class_id, $exam_id);
	    
	    // Nama File
	    $filename = 'Nilai_' . url_title($exam->exam_name) . '_' . date('Ymd') . '.xls';

	    // Header untuk memaksa download sebagai Excel
	    header("Content-Description: File Transfer");
	    header("Content-Type: application/vnd.ms-excel");
	    header("Content-Disposition: attachment; filename=\"$filename\""); 
	    
	    // Cetak Tabel HTML Sederhana (Excel bisa membaca table HTML)
	    echo '
	    <table border="1">
	        <thead>
	            <tr>
	                <th colspan="5" style="font-size:14px; font-weight:bold; text-align:center;">
	                    DAFTAR NILAI - ' . strtoupper($exam->exam_name) . '
	                </th>
	            </tr>
	            <tr>
	                <th style="background-color:#f0f0f0;">No</th>
	                <th style="background-color:#f0f0f0;">Nama Siswa</th>
	                <th style="background-color:#f0f0f0;">Status</th>
	                <th style="background-color:#f0f0f0;">Waktu Selesai</th>
	                <th style="background-color:#ffff00;">Nilai</th>
	            </tr>
	        </thead>
	        <tbody>';

	    $no = 1;
	    foreach ($students as $s) {
	        $status = ($s->attempt_status == 'finished') ? 'Selesai' : 
	                 (($s->attempt_status == 'ongoing') ? 'Mengerjakan' : 'Belum');
	        
	        $score = ($s->score !== null) ? str_replace('.', ',', $s->score) : '0'; // Koma untuk Excel Indo
	        
	        echo "<tr>
	            <td>$no</td>
	            <td>{$s->full_name}</td>
	            <td>$status</td>
	            <td>{$s->finished_time}</td>
	            <td>$score</td>
	        </tr>";
	        $no++;
	    }

	    echo '</tbody></table>';
	    exit;
	}

	public function review_student($attempt_id)
	{
	    // Cek Guru (Logic sama seperti di atas, disederhanakan)
	    $user = $this->session->userdata();
	    // Tambahkan validasi kepemilikan kelas di sini...

	    $detail = $this->Exam_model->get_attempt_detail($attempt_id);
	    if(!$detail) show_404();

	    $data = [
	        'title'   => 'Review: ' . $detail->full_name,
	        'detail'  => $detail,
	        'answers' => $this->Exam_model->get_student_answers($attempt_id)
	    ];

	    $this->load->view('templates/header', $data);
	    $this->load->view('exam/review_student', $data);
	    $this->load->view('templates/footer');
	}

	public function reset_student_exam()
	{
	    $attempt_id = $this->input->post('attempt_id');
	    // Validasi guru pemilik kelas wajib ada di sini
	    
	    if ($this->Exam_model->reset_attempt($attempt_id)) {
	        echo json_encode(['status' => 'success', 'message' => 'Data ujian siswa berhasil direset. Siswa dapat mengerjakan ulang.', 'csrf_hash' => $this->security->get_csrf_hash()]);
	    } else {
	        echo json_encode(['status' => 'error', 'message' => 'Gagal mereset.', 'csrf_hash' => $this->security->get_csrf_hash()]);
	    }
	}

	/* =========================
  * SISWA – AREA
  * ========================= */

	public function student_list($class_id = null)
	{
		$user_id = $this->session->userdata('user_id');

    // Ambil daftar sekolah dari model
		$kelas = $this->Murid_model->get_kelas_by_murid($user_id);

		$class_id = $kelas->id;
	    if (!$class_id) show_404();
	    
	    $user = $this->session->userdata();

	    if ($user['role'] != 'Siswa') { 
	        show_error('Akses khusus siswa', 403);
	    }

	    $data = [
	        'title'    => 'Daftar Ujian Aktif',
	        'class_id' => $class_id,
	        'user'     => $user,
	        'url_name' => 'siswa' // Untuk helper link di view
	    ];

	    $this->load->view('templates/header', $data);
	    $this->load->view('templates/sidebar', $data);
	    $this->load->view('exam/student_list', $data);
	    $this->load->view('templates/footer');
	}

	// JSON Provider untuk Tabel Siswa
	public function get_student_exams($class_id)
	{
	  $this->_auto_update_status($class_id);

	  $user = $this->session->userdata();
	  $user_id = $user['user_id']; 
	  
	  $exams = $this->Exam_model->get_exams_with_status($class_id, $user_id);
	  
	  // --- LOGIKA BACKEND UNTUK VALIDASI WAKTU ---
	  $now_timestamp = time();

	  foreach ($exams as $exam) {
	      $start_timestamp = strtotime($exam->start_time);
	      $end_timestamp = strtotime($exam->end_time);

	      // Default: Tidak bisa dikerjakan
	      $exam->can_attempt = false; 
	      $exam->status_message = '';

	      // Jika sudah selesai mengerjakan
	      if ($exam->attempt_status === 'finished') {
	           $exam->status_label = 'finished';
	           $exam->can_attempt = false;
	      } 
	      // Jika sedang mengerjakan (resume)
	      elseif ($exam->attempt_status === 'ongoing') {
	           $exam->status_label = 'ongoing';
	           $exam->can_attempt = true;
	      } 
	      // Jika belum mengerjakan
	      else {
	          // Toleransi 3 detik (agar sinkronisasi waktu client-server tidak kaku)
	          $is_started = $now_timestamp >= ($start_timestamp - 3); 
	          $is_expired = $now_timestamp > $end_timestamp;

	          if (!$is_started) {
	              // Kasus: Ujian akan datang (muncul di list tapi tombol disable)
	              $exam->status_label = 'upcoming';
	              $exam->can_attempt = false;
	              $exam->status_message = 'Belum Dimulai';
	          } elseif ($is_expired) {
	              $exam->status_label = 'expired';
	              $exam->can_attempt = false;
	              $exam->status_message = 'Waktu Habis';
	          } else {
	              // Kasus: Ujian sedang berlangsung & Valid
	              $exam->status_label = 'available';
	              $exam->can_attempt = true;
	          }
	      }
	  }

	  $this->output
	    ->set_content_type('application/json')
	    ->set_output(json_encode($exams));
	}

	// Halaman Detail / Konfirmasi Sebelum Mulai
	public function confirmation($exam_id = null)
	{
	    $user = $this->session->userdata();

	    if (!$exam_id) show_404();
	    // Validasi dasar
	    $exam = $this->Exam_model->get_exam_with_class($exam_id);
	    if (!$exam) show_404();

	    // Cek apakah ujian masih aktif
	    if ($exam->is_active == 0 || strtotime($exam->end_time) < time()) {
	        $this->session->set_flashdata('error', 'Ujian ini sudah tidak aktif atau waktu habis.');
	        redirect('siswa/pbl/exam/student_list/' . $exam->class_id);
	    }

	    if (time() < (strtotime($exam->start_time) - 3)) {
	        $this->session->set_flashdata('error', 'Ujian belum dimulai! Harap tunggu jadwal yang ditentukan.');
	        redirect('siswa/pbl/exam/student_list/' . $exam->class_id);
	        return;
	    }
	    
	    // Cek apakah waktu mulai sudah masuk
	    if (strtotime($exam->start_time) > time()) {
	        $this->session->set_flashdata('error', 'Ujian belum dimulai.');
	        redirect('siswa/pbl/exam/student_list/' . $exam->class_id);
	    }

	    $data = [
	        'title' => 'Detail Ujian',
	        'exam'  => $exam,
	        'user'  => $user,
	    ];

	    $this->load->view('templates/header', $data);
	    $this->load->view('exam/confirmation', $data);
	    $this->load->view('templates/footer');
	}

	/* =========================
 * LOGIKA CBT (COMPUTER BASED TEST)
 * ========================= */

// 1. Memulai atau Melanjutkan Ujian
public function start_attempt()
{
    $user = $this->session->userdata();

    $exam_id = $this->input->post('exam_id');
    
    // Validasi sederhana
    if (!$exam_id || $user['role'] != 'Siswa') show_error('Akses Ditolak', 403);

    // Cek apakah siswa sudah pernah mulai mengerjakan?
    $attempt = $this->db->get_where('exam_attempts', [
        'exam_id' => $exam_id,
        'user_id' => $user['user_id'] // Sesuaikan key session ID user Anda
    ])->row();

    if ($attempt) {
        // Jika status sudah finish, tolak akses
        if ($attempt->status === 'finished') {
            $this->session->set_flashdata('error', 'Anda sudah menyelesaikan ujian ini.');
            redirect('siswa/pbl/exam/student_list/' . $this->input->post('class_id')); // Sesuaikan redirect
            return;
        }
        // Jika masih ongoing, lanjut ke halaman paper
        $attempt_id = $attempt->id;
    } else {
        // Jika belum, buat data attempt baru
        $attempt_id = generate_ulid();
        $this->db->insert('exam_attempts', [
            'id' => $attempt_id,
            'exam_id' => $exam_id,
            'user_id' => $user['user_id'],
            'start_time' => date('Y-m-d H:i:s'),
            'status' => 'ongoing'
        ]);
    }

    // Redirect ke halaman pengerjaan
    redirect('exam/paper/' . $attempt_id);
}

// 2. Halaman Pengerjaan Soal (Paper)
public function paper($attempt_id = null)
{
    if (!$attempt_id) show_404();
    $user = $this->session->userdata();

    // Ambil data attempt & validasi kepemilikan
    $attempt = $this->db->select('exam_attempts.*, exams.exam_name, exams.end_time, exams.type')
        ->from('exam_attempts')
        ->join('exams', 'exams.exam_id = exam_attempts.exam_id')
        ->where('exam_attempts.id', $attempt_id)
        ->where('exam_attempts.user_id', $user['user_id'])
        ->get()->row();

    if (!$attempt || $attempt->status === 'finished') {
        redirect('guru/dashboard'); // Redirect default jika error
    }

    // Ambil Soal
    $questions = $this->db->where('exam_id', $attempt->exam_id)
        ->order_by('created_at', 'ASC') // Atau RAND() jika ingin acak
        ->get('exam_questions')->result();

    // Ambil Jawaban yang sudah tersimpan (untuk resume)
    $saved_answers_query = $this->db->where('attempt_id', $attempt_id)->get('exam_answers')->result();
    $saved_answers = [];
    foreach ($saved_answers_query as $ans) {
        $saved_answers[$ans->question_id] = $ans->answer;
    }

    $data = [
        'title'   => $attempt->exam_name,
        'attempt' => $attempt,
        'questions' => $questions,
        'saved_answers' => $saved_answers,
        'user' => $user
    ];

    // Kita gunakan layout khusus ujian (tanpa sidebar admin biasanya)
    $this->load->view('exam/paper', $data);
}

// 3. AJAX: Simpan Jawaban Per Soal
public function save_answer()
{
    $attempt_id = $this->input->post('attempt_id');
    $question_id = $this->input->post('question_id');
    $answer = $this->input->post('answer');

    // Cek apakah jawaban sudah ada
    $exists = $this->db->get_where('exam_answers', [
        'attempt_id' => $attempt_id,
        'question_id' => $question_id
    ])->row();

    if ($exists) {
        $this->db->where('id', $exists->id)->update('exam_answers', ['answer' => $answer]);
    } else {
        $this->db->insert('exam_answers', [
            'id' => generate_ulid(),
            'attempt_id' => $attempt_id,
            'question_id' => $question_id,
            'answer' => $answer
        ]);
    }
    echo json_encode(['status' => 'success']);
}

	// 4. Selesai Ujian (Hitung Nilai)
	public function finish_exam()
	{
	  $attempt_id = $this->input->post('attempt_id');
	  
	  // Ambil semua jawaban siswa
	  $answers = $this->db->get_where('exam_answers', ['attempt_id' => $attempt_id])->result();
	  
	  // Ambil kunci jawaban
	  $attempt = $this->db->get_where('exam_attempts', ['id' => $attempt_id])->row();
	  $questions = $this->db->where('exam_id', $attempt->exam_id)->get('exam_questions')->result();

	  $correct_count = 0;
	  $total_questions = count($questions);
	  
	  // Mapping Kunci Jawaban
	  $key_map = [];
	  foreach($questions as $q) {
	      $key_map[$q->id] = $q->correct_answer;
	  }

	  // Hitung Benar
	  foreach($answers as $ans) {
	    if(isset($key_map[$ans->question_id]) && $key_map[$ans->question_id] == $ans->answer) {
	        $correct_count++;
	        // Update status benar di tabel jawaban (opsional, untuk analisis butir soal)
	        $this->db->where('id', $ans->id)->update('exam_answers', ['is_correct' => 1]);
	    }
	  }

	  // Hitung Score (Skala 100)
	  $score = ($total_questions > 0) ? ($correct_count / $total_questions) * 100 : 0;

	  // Update Attempt
	  $this->db->where('id', $attempt_id)->update('exam_attempts', [
	      'finished_time' => date('Y-m-d H:i:s'),
	      'score' => $score,
	      'status' => 'finished'
	  ]);

	  echo json_encode(['status' => 'success', 'redirect' => base_url('exam/student_list')]);
	}



}



/* End of file Exam.php */
/* Location: ./application/controllers/Exam.php */