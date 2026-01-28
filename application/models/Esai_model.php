<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Esai_model extends CI_Model
{
	private $table_essays = 'essays';
	private $table_submissions = 'essay_submissions';
	private $table_questions = 'essay_questions';
	private $table_students = 'students';
	private $table_users = 'users';

	/* SOLUTION ESSAY FUNCTIONS */
	public function get_solution_essays($class_id)
	{
		return $this->db->where('class_id', $class_id)
			->order_by('created_at', 'DESC')
			->get($this->table_essays)
			->result();
	}

	public function get_solution_essay($id)
	{
		return $this->db->where('essay_id', $id)->get($this->table_essays)->row();
	}

	public function insert_solution_essay($data)
	{
		return $this->db->insert($this->table_essays, $data);
	}

	public function update_solution_essay($id, $data)
	{
		return $this->db->where('essay_id', $id)->update($this->table_essays, $data);
	}

	public function delete_solution_essay($id)
	{
		return $this->db->where('essay_id', $id)->delete($this->table_essays);
	}

	/**
	 * Mengambil detail Esai utama
	 */
	public function get_essay_details($essay_id)
	{
		return $this->db->where('essay_id', $essay_id)
			->get($this->table_essays)
			->row();
	}

	/**
   * Mengambil daftar seluruh siswa dalam kelas beserta status pengumpulannya
   * Logic: Ambil data Student -> Join User (untuk nama) -> Left Join Submission
   */
  public function get_class_students_with_submission($class_id, $essay_id)
  {
	  // Select kolom yang dibutuhkan
	  $this->db->select('
      std.user_id as student_user_id,
      u.name as student_name,
      u.image as student_image,
      sub.id as submission_id,
      sub.submission_content,
      sub.grade,
      sub.feedback,
      sub.created_at as submitted_at
	  ');

	  // Dari tabel students (filter by class)
	  $this->db->from($this->table_students . ' as std');
	  
	  // Join ke Users untuk dapat Nama
	  $this->db->join($this->table_users . ' as u', 'std.user_id = u.id');

	  // LEFT Join ke Submissions untuk cek apakah sudah mengumpulkan
	  // Kondisi join ganda: user_id sama DAN essay_id sesuai
	  $this->db->join($this->table_submissions . ' as sub', 'sub.user_id = std.user_id AND sub.essay_id = "' . $essay_id . '"', 'left');

	  $this->db->where('std.class_id', $class_id);
	  $this->db->where('u.role_id !=', '1'); // Pastikan bukan admin/guru (opsional, tergantung role id siswa)
	  
	  return $this->db->get()->result();
  }

	/**
	 * Mengambil semua jawaban siswa, di-join dengan nama
	 */
	public function get_submissions($essay_id)
	{
		$this->db->select('s.*, u.name as student_name');
		$this->db->from($this->table_submissions . ' as s');
		$this->db->join($this->table_users . ' as u', 's.user_id = u.id', 'left');
		$this->db->where('s.essay_id', $essay_id);
		$this->db->order_by('s.created_at', 'ASC');
		return $this->db->get()->result();
	}

	/**
	 * Menyimpan (Update) nilai dan feedback dari guru
	 */
	public function save_feedback($submission_id, $data)
	{
		$this->db->where('id', $submission_id);
		return $this->db->update($this->table_submissions, $data);
	}

	/* FUNGSI UNTUK PERTANYAAN ESAI */

	/**
	 * Mengambil semua pertanyaan esai untuk suatu esai
	 */
	public function get_questions($essay_id)
	{
		return $this->db->where('essay_id', $essay_id)
			->order_by('question_number', 'ASC')
			->get($this->table_questions)
			->result();
	}

	/**
   * Mendapatkan nomor urut terakhir untuk esai tertentu
   */
  public function get_last_question_number($essay_id)
  {
    $this->db->select_max('question_number');
    $this->db->where('essay_id', $essay_id);
    $query = $this->db->get($this->table_questions);
    $result = $query->row();
    return $result->question_number ? (int)$result->question_number : 0;
  }

  /**
   * Menyimpan Soal (Bisa insert banyak sekaligus atau update satu)
   */
  public function save_question_batch($essay_id, $questions_data, $update_id = null)
  {
    // KASUS 1: UPDATE (Edit 1 Soal)
    if ($update_id) {
      // Ambil data pertama saja karena edit hanya 1 row
      $text = $questions_data[0]; 
      $this->db->where('id', $update_id);
      return $this->db->update($this->table_questions, ['question_text' => $text]);
    }

    // KASUS 2: INSERT BATCH (Tambah Banyak Soal)
    $batch_data = [];
    $last_num = $this->get_last_question_number($essay_id);

    foreach ($questions_data as $text) {
      if (trim($text) === '') continue; // Skip input kosong
      
      $last_num++;
      $batch_data[] = [
        'id'              => generate_ulid(),
        'essay_id'        => $essay_id,
        'question_number' => $last_num,
        'question_text'   => $text,
        'created_at'      => date('Y-m-d H:i:s')
      ];
    }

    if (!empty($batch_data)) {
      return $this->db->insert_batch($this->table_questions, $batch_data);
    }
    
    return false;
  }

	/**
	 * Menyimpan (Create/Update) pertanyaan esai
	 */
	public function save_question($data, $id = null)
	{
		// Cek apakah ada ID (untuk Update)
		if ($id) {
			$this->db->where('id', $id);
			return $this->db->update($this->table_questions, $data);
		} else {
			// Jika tidak ada ID (untuk Create), buat ID baru
			$data['id'] = generate_ulid();
			return $this->db->insert($this->table_questions, $data);
		}
	}

	/**
	 * Menghapus pertanyaan esai
	 */
	public function delete_question($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete($this->table_questions);
	}

		/* FUNGSI BARU UNTUK SISWA (SUBMISSION) */

	/**
	 * Mengambil jawaban siswa untuk esai tertentu
	 */
	public function get_student_submission($essay_id, $user_id)
	{
		return $this->db->where('essay_id', $essay_id)
			->where('user_id', $user_id)
			->get($this->table_submissions)
			->row();
	}

	/**
	 * Menyimpan atau memperbarui jawaban siswa
	 */
	public function save_student_submission($essay_id, $user_id, $content, $submission_id = null)
	{
		$data = [
			'essay_id' => $essay_id,
			'user_id' => $user_id,
			'submission_content' => $content,
		];

		if ($submission_id) {
			// Update
			$this->db->where('id', $submission_id);
			return $this->db->update($this->table_submissions, $data);
		} else {
			// Insert baru
			$data['id'] = generate_ulid();
			return $this->db->insert($this->table_submissions, $data);
		}
	}

}