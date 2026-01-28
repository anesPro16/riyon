<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_model extends CI_Model
{
  private $table = 'pbl_orientasi';

  public function get_all($class_id)
  {
    $this->db->where('class_id', $class_id);
    $this->db->order_by('created_at', 'DESC');
    return $this->db->get($this->table)->result();
  }

  public function insert($data)
  {
    return $this->db->insert($this->table, $data);
  }

  public function update($id, $data)
  {
    return $this->db->where('id', $id)->update($this->table, $data);
  }

  public function get_orientasi($id)
  {
  	return $this->db->where('id', $id)->get($this->table)->row();
  }

  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->table);
  }

}

/* End of file Pbl_model.php */
/* Location: ./application/models/Pbl_model.php */