<?php
class Exam_model extends CI_Model
{
  private $table = 'exams';

  public function get_by_class($class_id)
  {
    return $this->db
      ->where('class_id', $class_id)
      ->order_by('created_at', 'DESC')
      ->get($this->table)
      ->result();
  }

  public function get_by_id($id)
  {
    return $this->db
      ->where('exam_id', $id)
      ->get($this->table)
      ->row();
  }

  public function insert($data)
  {
    return $this->db->insert($this->table, $data);
  }

  public function update($id, $data)
  {
    return $this->db
      ->where('exam_id', $id)
      ->update($this->table, $data);
  }

  public function delete($id)
  {
    return $this->db
      ->where('exam_id', $id)
      ->delete($this->table);
  }

 	public function getTeacherId($user_id)
{
    $query = $this->db
        ->select('id')
        ->from('teachers')
        ->where('user_id', $user_id)
        ->get();

    // Cek apakah data ditemukan
    if ($query->num_rows() > 0) {
        return $query->row()->id; // Mengambil value 'id' dari baris pertama
    }

    return null; // Atau return false jika data tidak ditemukan
}

  /* =========================
   * VALIDASI KELAS GURU
   * ========================= */
  public function is_teacher_class($class_id, $teacher_id)
	{
	    return $this->db
	        ->select('classes.id')
	        ->from('classes')
	        // 1. JOIN tabel classes dan teachers berdasarkan ID Guru
	        ->join('teachers', 'teachers.id = classes.teacher_id') 
	        
	        // 2. Cek apakah ID kelas sesuai
	        ->where('classes.id', $class_id)
	        
	        // 3. Cek apakah ID di tabel teachers cocok dengan ID yang dioper
	        // Kita TIDAK menggunakan teachers.user_id, tapi teachers.id
	        ->where('teachers.id', $teacher_id) 
	        
	        ->get()
	        ->num_rows() > 0;
	}

	public function get_exam_with_class($exam_id)
	{
	    return $this->db
	        ->select('exams.*, classes.id as class_id')
	        ->from('exams')
	        ->join('classes', 'classes.id = exams.class_id')
	        ->where('exams.exam_id', $exam_id)
	        ->get()->row();
	}


	public function get_latest_exam($class_id, $exam_name, $type)
	{
	  return $this->db
	    ->where('class_id', $class_id)
	    ->where('exam_name', $exam_name)
	    ->where('type', $type)
	    ->order_by('start_time', 'DESC') // Ambil yang paling baru
	    ->limit(1)
	    ->get('exams')
	    ->row();
	}

	/* ===== QUESTIONS ===== */

	public function get_questions($exam_id)
	{
	    return $this->db
	        ->where('exam_id', $exam_id)
	        ->order_by('created_at', 'ASC')
	        ->get('exam_questions')
	        ->result();
	}

	public function insert_question($data)
	{
	    return $this->db->insert('exam_questions', $data);
	}

	public function update_question($id, $data)
	{
	    return $this->db->where('id', $id)->update('exam_questions', $data);
	}

	public function delete_question($id)
	{
	    return $this->db->where('id', $id)->delete('exam_questions');
	}

	public function insert_batch_questions($data)
	{
	    return $this->db->insert_batch('exam_questions', $data);
	}

	// Tambahkan di dalam class Exam_model

	public function get_active_exams_by_class($class_id)
	{
    $now = date('Y-m-d H:i:s');
    
    return $this->db
      ->where('class_id', $class_id)
      ->where('is_active', 1) 
      ->where('start_time <=', $now) // Hanya tampil jika waktu mulai sudah lewat/sekarang
      ->where('end_time >=', $now)   // Dan waktu selesai belum lewat
      ->order_by('end_time', 'ASC')  // Yang mau habis duluan ditaruh atas
      ->get('exams')
      ->result();
	}

	// Tambahkan function ini di Exam_model.php
	public function get_exams_with_status($class_id, $user_id)
	{
    $now = date('Y-m-d H:i:s');
    // Hitung tanggal 1 minggu ke depan
    $one_week_ahead = date('Y-m-d H:i:s', strtotime('+1 week')); 
    
    $this->db->select('exams.*, exam_attempts.status as attempt_status, exam_attempts.score, exam_attempts.finished_time');
    $this->db->from('exams');
    
    // Sesuaikan user_id / user_id dengan struktur database Anda
    $this->db->join('exam_attempts', 'exams.exam_id = exam_attempts.exam_id AND exam_attempts.user_id = "'.$user_id.'"', 'left');
    
    $this->db->where('exams.class_id', $class_id);
    $this->db->where('exams.is_active', 1);
    
    $this->db->group_start();
        // LOGIKA BARU: Tampilkan jika waktu mulai <= 1 minggu ke depan
        $this->db->where('exams.start_time <=', $one_week_ahead);
        $this->db->or_where('exam_attempts.status IS NOT NULL');
    $this->db->group_end();

    $this->db->order_by('exams.start_time', 'ASC'); // Urutkan berdasarkan waktu mulai agar yang terdekat muncul duluan
    
    return $this->db->get()->result();
	}

	public function get_exam_results($class_id, $exam_id)
{
    // Pilih kolom yang dibutuhkan
    // u.id diambil sebagai user_id
    // u.name diambil sebagai full_name (alias agar sesuai view sebelumnya)
    $this->db->select('
        u.id as user_id, 
        u.name as full_name, 
        ea.id as attempt_id,
        ea.score,
        ea.start_time,
        ea.finished_time,
        ea.status as attempt_status
    ');
    
    // 1. Mulai dari tabel students (karena ini yang punya class_id)
    $this->db->from('students s');
    
    // 2. Join ke tabel users untuk ambil Nama
    $this->db->join('users u', 'u.id = s.user_id');
    
    // 3. Left Join ke attempt untuk cek status ujian
    // Kondisi: user_id di attempt = id di users DAN exam_id sesuai parameter
    $this->db->join('exam_attempts ea', 'ea.user_id = u.id AND ea.exam_id = "'.$exam_id.'"', 'left');
    
    // 4. Filter berdasarkan Kelas
    $this->db->where('s.class_id', $class_id);
    
    // 5. Urutkan berdasarkan nama
    $this->db->order_by('u.name', 'ASC');
    
    return $this->db->get()->result();
}

// Perbaikan juga diperlukan di sini karena kolom nama di tabel users adalah 'name', bukan 'full_name'
public function get_attempt_detail($attempt_id)
{
    return $this->db->select('ea.*, u.name as full_name, e.exam_name')
        ->from('exam_attempts ea')
        ->join('users u', 'u.id = ea.user_id') // Ubah u.user_id jadi u.id
        ->join('exams e', 'e.exam_id = ea.exam_id')
        ->where('ea.id', $attempt_id)
        ->get()->row();
}

	public function get_student_answers($attempt_id)
	{
	    // Ambil jawaban siswa beserta kunci jawaban asli untuk perbandingan
	    return $this->db->select('
	            q.id as question_id, q.question, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_answer,
	            a.answer as student_answer, a.is_correct
	        ')
	        ->from('exam_questions q')
	        ->join('exam_answers a', 'a.question_id = q.id AND a.attempt_id = "'.$attempt_id.'"', 'left')
	        ->join('exam_attempts ea', 'ea.id = "'.$attempt_id.'"')
	        ->where('q.exam_id', 'ea.exam_id', false) // Trick CI3 join
	        ->where('ea.id', $attempt_id)
	        ->order_by('q.created_at', 'ASC') // Atau urutan soal
	        ->get()->result();
	}

	public function reset_attempt($attempt_id)
	{
	    // Hapus jawaban & attempt (Trigger MySQL biasanya menangani cascade, tapi kita manual saja biar aman)
	    $this->db->trans_start();
	    $this->db->delete('exam_answers', ['attempt_id' => $attempt_id]);
	    $this->db->delete('exam_attempts', ['id' => $attempt_id]);
	    $this->db->trans_complete();
	    return $this->db->trans_status();
	}


}


/* End of file Exam_model.php */
/* Location: ./application/models/Exam_model.php */