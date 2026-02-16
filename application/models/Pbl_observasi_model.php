<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_observasi_model extends CI_Model
{
  private $table_slots = 'observation';
  private $table_uploads = 'observation_uploads';
  private $table_results = 'observation_results';
  private $table_users = 'users';

  // Ambil detail slot (judul & deskripsi tugas)
  public function get_slot_by_id($id)
  {
    return $this->db->where('observation_id', $id)->get($this->table_slots)->row();
  }

  public function get_uploads_by_slot($slot_id)
  {
    // Select data upload + nama siswa + data nilai (jika ada)
    $this->db->select('u.*, users.name as student_name, 
      r.id as grade_id, r.score, r.feedback'); 
    
    $this->db->from($this->table_uploads . ' as u');
    $this->db->join($this->table_users . ' as users', 'u.user_id = users.id');
    
    // LEFT JOIN ke tabel hasil nilai berdasarkan user_id dan slot_id
    // Gunakan kondisi ganda pada ON agar hasil join spesifik untuk slot ini
    $this->db->join($this->table_results . ' as r', 
      'r.user_id = u.user_id AND r.observation_id = u.observation_id', 
      'left');

    $this->db->where('u.observation_id', $slot_id);
    $this->db->order_by('u.created_at', 'DESC');
    return $this->db->get()->result();
  }

  // Ambil upload HANYA milik siswa yang login di slot tertentu
  public function get_uploads_by_slot_and_user($slot_id, $user_id)
  {
    $this->db->select('*');
    $this->db->from($this->table_uploads);
    $this->db->where('observation_id', $slot_id);
    $this->db->where('user_id', $user_id);
    $this->db->order_by('created_at', 'DESC');
    return $this->db->get()->result();
  }

  // Simpan data file ke database
  public function insert_upload($data)
  {
    return $this->db->insert($this->table_uploads, $data);
  }

  // Ambil satu file (untuk cek kepemilikan sebelum hapus)
  public function get_upload_by_id($id)
  {
    return $this->db->where('id', $id)->get($this->table_uploads)->row();
  }

  // Hapus upload
  public function delete_upload($id)
  {
    // Ambil data file dulu untuk unlink
    $file = $this->db->where('id', $id)->get($this->table_uploads)->row();

    if ($file) {
        // Hapus file fisik jika ada
      $file_path = FCPATH . 'uploads/observasi/' . $file->file_name;
      if (file_exists($file_path)) {
        unlink($file_path);
      }

      // Hapus data di DB
      return $this->db->where('id', $id)->delete($this->table_uploads);
    }
    return false;
  }

  // --- Fungsi UNTUK SISWA MELIHAT NILAI ---
  public function get_student_result($slot_id, $user_id)
  {
    return $this->db->where('observation_id', $slot_id)
      ->where('user_id', $user_id)
      ->get($this->table_results)
      ->row();
  }

  /**
   * Mengambil daftar nilai pada slot ini.
   */
  public function get_grades_by_slot($slot_id)
  {
    $this->db->select('r.*, u.name as student_name, u.username');
    $this->db->from($this->table_results . ' r');
    $this->db->join($this->table_users . ' u', 'u.id = r.user_id');
    $this->db->where('r.observation_id', $slot_id);
    $this->db->order_by('u.name', 'ASC');
    return $this->db->get()->result();
  }

  public function get_grade_by_id($id)
  {
    return $this->db->where('id', $id)->get($this->table_results)->row();
  }

  /**
   * Cek apakah siswa sudah dinilai di slot ini (untuk update/insert)
   */
  public function check_grade_exists($observation_id, $user_id)
  {
    return $this->db->where('observation_id', $observation_id)
      ->where('user_id', $user_id)
      ->get($this->table_results)
      ->row();
  }

  public function insert_grade($data)
  {
    return $this->db->insert($this->table_results, $data);
  }

  public function update_grade($id, $data)
  {
    $this->db->where('id', $id);
    return $this->db->update($this->table_results, $data);
  }

  public function get_observation_id($id)
  {
      /*return $this->db->select('id', 'observation_id')
        ->where('id', $id)
        ->get($this->table_uploads)
        ->row();*/
        return $this->db->where('id', $id)->get($this->table_uploads)->row();
  }

  public function delete_grade($id)
  {
    $this->db->where('id', $id);
    return $this->db->delete($this->table_results);
  }

  public function delete_grade_by_observation_id($id)
  {
    $this->db->where('observation_id', $id);
    return $this->db->delete($this->table_results);
  }

  public function delete_grade_by_observation_id_by_user_id($id, $user_id)
  {
    $this->db->where('observation_id', $id);
    $this->db->where('user_id', $user_id);
    return $this->db->delete($this->table_results);
  }

}

/* End of file Pbl_observasi_model.php */
/* Location: ./application/models/Pbl_observasi_model.php */