<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Observasi_model extends CI_Model {

	// Definisikan tabel untuk Tahap 3
	private $table_observations = 'observation';
	private $table_discussions = 'pbl_discussion_topics';

	/* OBSERVATION FUNCTIONS */
	public function get_observations($class_id)
	{
		return $this->db->where('class_id', $class_id)
			->order_by('created_at', 'DESC')
			->get($this->table_observations)
			->result();
	}

	public function get_observation($id)
  {
  	return $this->db->where('observation_id', $id)->get($this->table_observations)->row();
  }

	public function insert_observation($data)
	{
		return $this->db->insert($this->table_observations, $data);
	}

	public function update_observation($id, $data)
	{
		return $this->db->where('observation_id', $id)->update($this->table_observations, $data);
	}

	public function delete_observation($id)
	{
		return $this->db->where('observation_id', $id)->delete($this->table_observations);
	}


	/* DISCUSSION FUNCTIONS */
	public function get_discussions($class_id)
	{
		return $this->db->where('class_id', $class_id)
			->order_by('created_at', 'DESC')
			->get($this->table_discussions)
			->result();
	}

	public function get_discussion($id)
	{
		return $this->db->where('id', $id)->get($this->table_discussions)->row();
	}

	public function insert_discussion($data)
	{
		return $this->db->insert($this->table_discussions, $data);
	}

	public function update_discussion($id, $data)
	{
		return $this->db->where('id', $id)->update($this->table_discussions, $data);
	}

	public function delete_discussion($id)
	{
		return $this->db->where('id', $id)->delete($this->table_discussions);
	}

}

/* End of file Observasi_model.php */
/* Location: ./application/models/Observasi_model.php */