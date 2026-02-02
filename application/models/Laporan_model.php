<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

  /**
   * 1. Ambil daftar Nama Ujian (Mata Pelajaran) yang unik di kelas ini
   * Digunakan untuk membuat Header Tabel (IPA, MTK, IPS, dst)
   */
  public function getExamSubjects($class_id)
  {
    $this->db->select('exam_name');
    $this->db->distinct();
    $this->db->from('exams');
    $this->db->where('class_id', $class_id);
    $this->db->where('is_active', 0);
    $this->db->order_by('exam_name', 'ASC');
    return $this->db->get()->result();
  }

  /**
   * Mengambil rekap nilai siswa + data refleksi jika ada
   * UPDATE: Menggunakan tabel 'students' sebagai penghubung
   */
  public function getAllStudentScores($class_id)
    {
        $this->db->select("
            u.id as user_id, 
            u.name as student_name, 
            u.image,
            
            -- Data Refleksi (Tahap 5)
            MAX(r.id) as reflection_id,
            MAX(r.teacher_reflection) as teacher_reflection,
            MAX(r.student_feedback) as student_feedback,

            -- 1. DATA UJIAN (UTS/UAS)
            -- Format: Matematika::UTS::85||Matematika::UAS::90
            (
                SELECT GROUP_CONCAT(CONCAT(e.exam_name, '::', e.type, '::', ea.score) SEPARATOR '||')
                FROM exam_attempts ea
                JOIN exams e ON e.exam_id = ea.exam_id
                WHERE ea.user_id = u.id 
                AND e.class_id = '$class_id'
                AND ea.status = 'finished' 
            ) as exam_data,

            -- 2. DATA KUIS (PBL)
            -- Join ke tabel quizzes untuk ambil kolom 'description'
            -- Format: Matematika::80||IPA::90
            (
                SELECT GROUP_CONCAT(CONCAT(pq.subjects, '::', pqr.score) SEPARATOR '||')
                FROM quiz_results pqr
                JOIN quizzes pq ON pq.quiz_id = pqr.quiz_id
                WHERE pqr.user_id = u.id 
                AND pq.class_id = '$class_id'
            ) as quiz_data,

            -- 3. DATA OBSERVASI (PBL)
            -- Join ke tabel observation untuk ambil kolom 'description'
            (
                SELECT GROUP_CONCAT(CONCAT(pos.subjects, '::', por.score) SEPARATOR '||')
                FROM observation_results por
                JOIN observation pos ON pos.observation_id = por.observation_id
                WHERE por.user_id = u.id 
                AND pos.class_id = '$class_id'
            ) as obs_data,

            -- 4. DATA ESAI (PBL)
            -- Join ke tabel essays untuk ambil kolom 'description'
            (
                SELECT GROUP_CONCAT(CONCAT(pse.subjects, '::', pes.grade) SEPARATOR '||')
                FROM essay_submissions pes
                JOIN essays pse ON pse.essay_id = pes.essay_id
                WHERE pes.user_id = u.id 
                AND pse.class_id = '$class_id'
                AND pes.grade IS NOT NULL
            ) as essay_data
        ");

        $this->db->from('students s'); 
        $this->db->join('users u', 'u.id = s.user_id'); 
        $this->db->join('evaluation r', 'r.user_id = u.id AND r.class_id = s.class_id', 'left');
        $this->db->where('s.class_id', $class_id);
        $this->db->group_by('u.id'); 

        return $this->db->get()->result();
    }

    public function get_student_score_data($user_id, $class_id)
    {
      $this->db->select("
        u.id as user_id, 
        u.name as student_name,
        
        -- Data Refleksi Guru untuk Siswa ini
        MAX(r.teacher_reflection) as teacher_reflection,
        MAX(r.student_feedback) as student_feedback,

        -- 1. DATA UJIAN (UTS/UAS)
        -- Format: Matematika::UTS::85||Matematika::UAS::90
        (
            SELECT GROUP_CONCAT(CONCAT(e.exam_name, '::', e.type, '::', ea.score) SEPARATOR '||')
            FROM exam_attempts ea
            JOIN exams e ON e.exam_id = ea.exam_id
            WHERE ea.user_id = u.id 
            AND e.class_id = '$class_id'
            AND ea.status = 'finished' 
        ) as exam_data,

        -- 2. DATA KUIS (PBL) - Ambil Subjects
        -- Format: Matematika::80||IPA::90
        (
            SELECT GROUP_CONCAT(CONCAT(pq.subjects, '::', pqr.score) SEPARATOR '||')
            FROM quiz_results pqr
            JOIN quizzes pq ON pq.quiz_id = pqr.quiz_id
            WHERE pqr.user_id = u.id 
            AND pq.class_id = '$class_id'
        ) as quiz_data,

        -- 3. DATA OBSERVASI (PBL)
        (
            SELECT GROUP_CONCAT(CONCAT(pos.subjects, '::', por.score) SEPARATOR '||')
            FROM observation_results por
            JOIN observation pos ON pos.observation_id = por.observation_id
            WHERE por.user_id = u.id 
            AND pos.class_id = '$class_id'
        ) as obs_data,

        -- 4. DATA ESAI (PBL)
        (
            SELECT GROUP_CONCAT(CONCAT(pse.subjects, '::', pes.grade) SEPARATOR '||')
            FROM essay_submissions pes
            JOIN essays pse ON pse.essay_id = pes.essay_id
            WHERE pes.user_id = u.id 
            AND pse.class_id = '$class_id'
            AND pes.grade IS NOT NULL
        ) as essay_data
    ");

    $this->db->from('users u'); 
    $this->db->join('students s', 's.user_id = u.id');
    $this->db->join('evaluation r', 'r.user_id = u.id AND r.class_id = s.class_id', 'left');
    
    $this->db->where('u.id', $user_id);
    $this->db->where('s.class_id', $class_id);
    $this->db->group_by('u.id'); 

    return $this->db->get()->row(); // Return 1 Row Object
  }

  public function save_reflection($data)
  {
    // Cek apakah data sudah ada
  	$exists = $this->db->get_where('evaluation', [
  		'class_id' => $data['class_id'],
  		'user_id'  => $data['user_id']
  	])->row();

  	if ($exists) {
        // Update jika sudah ada
  		$this->db->where('id', $exists->id);
  		return $this->db->update('evaluation', [
  			'teacher_reflection' => $data['teacher_reflection'],
  			'student_feedback'   => $data['student_feedback']
  		]);
  	} else {
      // Insert baru jika belum ada
  		if (empty($data['id'])) {
  			$data['id'] = generate_ulid(); 
  		}

  		return $this->db->insert('evaluation', $data);
  	}
  }
}

/* End of file Laporan_model.php */
/* Location: ./application/models/Laporan_model.php */