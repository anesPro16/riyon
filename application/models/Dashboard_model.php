<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
/* =========================================================
 * CARD STATISTIC
 * ========================================================= */

  public function count_users()
  {
    return $this->db->count_all('users');
  }

  public function count_teachers()
  {
    return $this->db->count_all('teachers');
  }

  public function count_students()
  {
    return $this->db->count_all('students');
  }

  public function count_classes()
  {
    return $this->db->count_all('classes');
  }

  public function count_pbl_quizzes()
  {
    return $this->db->count_all('pbl_quizzes');
  }

  public function count_exams()
  {
    return $this->db->count_all('exams');
  }

  /* =========================================================
   * CHART : USER PER ROLE
   * ========================================================= */

  public function chart_users_by_role()
  {
    $query = $this->db
      ->select('roles.role, COUNT(users.id) as total')
      ->from('roles')
      ->join('users', 'users.role_id = roles.id', 'left')
      ->group_by('roles.id')
      ->order_by('roles.role', 'ASC')
      ->get()
      ->result();

    $labels = [];
    $data   = [];

    foreach ($query as $row) {
        $labels[] = $row->role;
        $data[]   = (int)$row->total;
    }

    return [
        'labels' => $labels,
        'data'   => $data
    ];
  }

  /* =========================================================
   * CHART : JUMLAH KELAS PER TAHUN
   * ========================================================= */

  public function chart_classes_per_year()
  {
      $query = $this->db
          ->select('YEAR(created_at) as year, COUNT(id) as total')
          ->from('classes')
          ->group_by('YEAR(created_at)')
          ->order_by('year', 'ASC')
          ->get()
          ->result();

      $labels = [];
      $data   = [];

      foreach ($query as $row) {
          $labels[] = $row->year;
          $data[]   = (int)$row->total;
      }

      return [
          'labels' => $labels,
          'data'   => $data
      ];
  }

  /* =========================================================
   * CHART : RATA-RATA NILAI PBL
   * ========================================================= */

  public function chart_average_scores()
  {
      /* Quiz */
      $quiz = $this->db
          ->select_avg('score')
          ->get('pbl_quiz_results')
          ->row()->score ?? 0;

      /* Observasi */
      $observasi = $this->db
          ->select_avg('score')
          ->get('pbl_observation_results')
          ->row()->score ?? 0;

      /* Essay */
      $essay = $this->db
          ->select_avg('grade')
          ->get('pbl_essay_submissions')
          ->row()->grade ?? 0;

      /* Ujian (UTS + UAS) */
      $exam = $this->db
          ->select_avg('score')
          ->where('status', 'finished')
          ->get('exam_attempts')
          ->row()->score ?? 0;

      return [
          'labels' => ['Quiz PBL', 'Observasi', 'Essay Solusi', 'Ujian'],
          'data'   => [
              round($quiz, 2),
              round($observasi, 2),
              round($essay, 2),
              round($exam, 2)
          ]
      ];
  }

  public function chart_teacher_student()
	{
	  $total_teachers = $this->db->count_all('teachers');
	  $total_students = $this->db->count_all('students');

	  return [
	    'labels' => ['Guru', 'Siswa'],
	    'data'   => [$total_teachers, $total_students]
	  ];
	}


  /* =========================================================
   * TABLE : DATA TERBARU
   * ========================================================= */

  public function latest_classes()
  {
      return $this->db
          ->select('name, code, created_at')
          ->from('classes')
          ->order_by('created_at', 'DESC')
          ->limit(5)
          ->get()
          ->result();
  }

  public function latest_teachers()
  {
      return $this->db
          ->select('users.name, users.email, teachers.created_at')
          ->from('teachers')
          ->join('users', 'users.id = teachers.user_id')
          ->order_by('teachers.created_at', 'DESC')
          ->limit(5)
          ->get()
          ->result();
  }

  public function active_exams()
  {
      return $this->db
          ->select('
              exams.exam_name,
              exams.type,
              exams.start_time,
              classes.name as class_name
          ')
          ->from('exams')
          ->join('classes', 'classes.id = exams.class_id')
          ->where('exams.is_active', 1)
          ->order_by('exams.start_time', 'ASC')
          ->limit(5)
          ->get()
          ->result();
  }

  public function chart_exam_uts_uas()
	{
    $result = $this->db
      ->select('
          exams.type,
          AVG(exam_attempts.score) as avg_score
      ')
      ->from('exam_attempts')
      ->join('exams', 'exams.exam_id = exam_attempts.exam_id')
      ->where('exam_attempts.status', 'finished')
      ->group_by('exams.type')
      ->get()
      ->result();

    $data = [
      'UTS' => 0,
      'UAS' => 0
    ];

    foreach ($result as $row) {
      $data[$row->type] = round($row->avg_score, 2);
    }

    return [
      'labels' => ['UTS', 'UAS'],
      'data'   => [
        $data['UTS'],
        $data['UAS']
      ]
    ];
	}
}


/* End of file Dashboard_model.php */
/* Location: ./application/models/Dashboard_model.php */