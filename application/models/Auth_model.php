<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{
  // Ambil user beserta nama rolenya (JOIN table)
  public function get_user_by_username($username)
  {
      $this->db->select('users.*, roles.role as role_name');
      $this->db->from('users');
      $this->db->join('roles', 'roles.id = users.role_id');
      $this->db->where('users.username', $username);
      return $this->db->get()->row_array();
  }

  public function register_user($data)
  {
      return $this->db->insert('users', $data);
  }

  // Tambahkan function ini di dalam class Auth_model
  public function count_registrations_today()
  {
      // Mengambil tanggal hari ini (Y-m-d)
      $today = date('Y-m-d');
      
      // Query: SELECT COUNT(*) FROM users WHERE DATE(created_at) = '202X-XX-XX'
      $this->db->where('DATE(created_at)', $today);
      $this->db->from('users');
      return $this->db->count_all_results();
  }

  public function is_unique_username($username)
  {
      return $this->db->get_where('users', ['username' => $username])->num_rows() === 0;
  }
}

/* End of file Auth_model.php */
/* Location: ./application/models/Auth_model.php */