<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sekolah_model extends CI_Model {

	protected $table = 'schools';

	public function insert($d){ return $this->db->insert($this->table, $d); }
  public function update($id,$d){ return $this->db->where('id',$id)->update($this->table,$d); }
  public function delete($id){ return $this->db->where('id',$id)->delete($this->table); }
  public function get_all(){ return $this->db->order_by('created_at','desc')->get($this->table)->result(); }
  public function get($id){ return $this->db->where('id',$id)->get($this->table)->row(); }	

  public function get_school_by_class_id($class_id)
  {
  	$this->db->select('school_id');
  	$this->db->where('id', $class_id);
  	return $this->db->get('classes')->row();
  }

}

/* End of file Sekolah_model.php */
/* Location: ./application/models/Sekolah_model.php */