<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru_model extends CI_Model {

	protected $table = 'teachers';

	public function insert($d){ return $this->db->insert($this->table,$d); }

	public function update($id,$d){ return $this->db->where('id',$id)->update($this->table,$d); }

	public function get($id){ return $this->db->where('id',$id)->get($this->table)->row(); }

  /**
   * Mengambil satu kelas spesifik,
   * memastikan kelas itu milik guru yang login.
   */
  public function get_class_by_id($class_id, $user_id)
  {
  	return $this->db->get_where('classes', [
  		'id' => $class_id, 
  		'user_id' => $user_id
  	])->row();
  }

  /**
   * [READ] Mengambil detail kelas, HANYA jika dimiliki oleh guru yg login.
   * Ini adalah fungsi keamanan penting.
   * @param string $kelas_id ID kelas
   * @param string $user_id ID guru (dari session)
   * @return object|null
   */
  public function get_kelas_by_id_dan_guru($kelas_id, $user_id)
  {
  	$this->db->where('id', $kelas_id);
  	$this->db->where('user_id', $user_id);
  	return $this->db->get('classes')->row();
  }

  // Update query dasar teachers (hapus join schools)
  /*public function get_all_with_user(){
    $this->db->select('t.*, u.username, u.name, u.email');
    $this->db->from('teachers t');
    $this->db->join('users u','u.id = t.user_id','left');
    return $this->db->get()->result();
  }*/

  public function get_all_with_user(){
    // Pilih field spesifik untuk menghindari ambiguitas id
    $this->db->select('t.id, t.user_id, u.username, u.name, u.email');
    $this->db->from('teachers t');
    $this->db->join('users u','u.id = t.user_id');
    $this->db->order_by('u.name', 'ASC');
    return $this->db->get()->result();
  }

  /**
   * Mengambil semua kelas yang ditugaskan ke guru yang sedang login.
   * Relasi: User (Login) -> Teachers -> Classes (via teacher_id)
   */
  public function get_all_classes($user_id)
  {
    $this->db->select('classes.*');
    $this->db->from('classes');
    // Join ke tabel teachers untuk mencocokkan user_id yang login
    $this->db->join('teachers', 'teachers.id = classes.teacher_id');
    $this->db->where('teachers.user_id', $user_id);
    $this->db->order_by('classes.name', 'ASC');
    return $this->db->get()->result();
  }

  /**
   * Mengambil detail satu kelas spesifik.
   * Validasi: Memastikan kelas tersebut benar-benar milik guru yang login.
   */
  /*public function get_class_details($class_id, $user_id)
  {
    $this->db->select('classes.*');
    $this->db->from('classes');
    $this->db->join('teachers', 'teachers.id = classes.teacher_id');
    
    $this->db->where('classes.id', $class_id);
    $this->db->where('teachers.user_id', $user_id);
    
    $class = $this->db->get()->row();

    if (!$class) return null;

    $this->db->where('class_id', $class_id);
    $class->student_count = $this->db->count_all_results('students');

    return $class;
  }*/

  /**
     * [REVISI] Mengambil detail satu kelas spesifik.
     * Jika $user_id diisi, maka dicek kepemilikannya (Guru).
     * Jika $user_id NULL, maka bebas akses (Admin).
     */
    public function get_class_details($class_id, $user_id = null)
    {
        $this->db->select('classes.*, u.name as teacher_name'); // Tambah nama guru
        $this->db->from('classes');
        $this->db->join('teachers', 'teachers.id = classes.teacher_id');
        $this->db->join('users u', 'u.id = teachers.user_id'); // Join ke user guru

        $this->db->where('classes.id', $class_id);
        
        // Validasi kepemilikan HANYA JIKA user_id diberikan (Mode Guru)
        if ($user_id !== null) {
            $this->db->where('teachers.user_id', $user_id); 
        }
        
        $class = $this->db->get()->row();

        if (!$class) return null;

        // Hitung jumlah siswa di kelas
        $this->db->where('class_id', $class_id);
        $class->student_count = $this->db->count_all_results('students');

        return $class;
    }

  /**
   * Mengambil semua kelas digabung dengan data Guru (Teacher).
   * Digunakan oleh Admin untuk melihat daftar kelas.
   */
  public function get_all_classes_with_teachers()
  {
    $this->db->select('c.*, u.name as teacher_name, t.id as teacher_id');
    $this->db->from('classes c');
    // Join ke teachers, lalu ke users untuk ambil nama guru
    $this->db->join('teachers t', 't.id = c.teacher_id', 'left'); 
    $this->db->join('users u', 'u.id = t.user_id', 'left');
    $this->db->order_by('c.name', 'ASC');
    return $this->db->get()->result();
  }

  /**
   * Menambahkan kelas baru ke database.
   */
  public function insert_class($payload)
  {
  	return $this->db->insert('classes', $payload);
  }

  /**
   * Update Kelas (Admin)
   * Tidak butuh validasi user_id (pemilik) karena admin bypass
   */
  public function update_class_admin($id, $payload)
  {
    $this->db->where('id', $id);
    return $this->db->update('classes', $payload);
  }

  /**
   * Delete Kelas (Admin)
   */
  public function delete_class_admin($id)
  {
    $this->db->where('id', $id);
    return $this->db->delete('classes');
  }

  /**
   * [AJAX LOAD] Mengambil daftar siswa yang ada DI DALAM kelas tertentu.
   * Bergabung dengan tabel users untuk mendapatkan nama, username, dll.
   */
  public function get_students_in_class($class_id)
  {
    $this->db->select('s.id, u.name, u.username, u.email');
    $this->db->from('students s');
    $this->db->join('users u', 'u.id = s.user_id');
    $this->db->where('s.class_id', $class_id);
    return $this->db->get()->result();
  }

  /**
   * [MODAL DROPDOWN] Mengambil daftar siswa yang BELUM MEMILIKI KELAS.
   * Ini untuk mengisi <select> di modal "Tambah Siswa".
   */
  public function get_available_students()
  {
    $this->db->select('s.id, u.name, u.username'); // 's.id' adalah 'students.id'
    $this->db->from('students s');
    $this->db->join('users u', 'u.id = s.user_id');
    $this->db->where('s.class_id IS NULL');
    return $this->db->get()->result();
  }

  /**
   * [AJAX SAVE] Menambahkan siswa ke kelas (meng-UPDATE students.class_id).
   * @param string $student_id ID dari tabel 'students'
   * @param string $class_id ID dari tabel 'classes'
   */
  public function add_student_to_class($user_id, $class_id)
  {
    // 1. Cek sekali lagi apakah user ini sudah ada di tabel students
    $existing = $this->db->get_where('students', ['user_id' => $user_id])->row();

    // 2. Jika BELUM ADA, maka INSERT
    if ($existing === NULL) {
      $payload = [
        'id' => generate_ulid(),
        'user_id' => $user_id,
        'class_id' => $class_id
      ];
      return $this->db->insert('students', $payload);
    }

    // 3. Jika SUDAH ADA (mungkin di kelas lain), gagalkan.
    return false; // Gagal karena user sudah ada
  }

  /**
   * [AJAX DELETE] Mengeluarkan siswa dari kelas (set class_id = NULL).
   * @param string $student_id ID dari tabel 'students'
   * @param string $class_id ID kelas (untuk keamanan, pastikan kita hapus dari kelas yg benar)
   */
  public function remove_student_from_class($student_id, $class_id)
  {
  	$this->db->where('id', $student_id);
    $this->db->where('class_id', $class_id);
    return $this->db->delete('students');
  }

}

/* End of file Guru_model.php */
/* Location: ./application/models/Guru_model.php */