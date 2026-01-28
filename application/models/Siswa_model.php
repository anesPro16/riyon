<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa_model extends CI_Model {
	private $table = 'user';

	public function get_all() {
		$this->db->select('id, name, username, email, is_active');
		$this->db->where('role_id', 2);
		$this->db->order_by('created_at', 'DESC');
		return $this->db->get($this->table)->result();
	}

	public function get_by_id($id) {
		$this->db->select('id, name, username, email, is_active');
		return $this->db->get_where($this->table, ['id' => $id, 'role_id' => 2])->row();
	}

	public function insert($data) {
		return $this->db->insert($this->table, $data);
	}

	public function update($id, $data) {
		return $this->db->where('id', $id)->update($this->table, $data);
	}

	public function delete($id) {
		return $this->db->delete($this->table, ['id' => $id, 'role_id' => 2]);
	}

	public function is_unique($field, $value, $exclude_id = null) {
		$this->db->where($field, $value)->where('role_id', 2);
		if ($exclude_id) $this->db->where('id !=', $exclude_id);
		return $this->db->get($this->table)->num_rows() === 0;
	}

	/**
     * Memperbarui status banyak siswa sekaligus
     * @param array $ids Array berisi ID siswa
     * @param int $status 1 untuk aktif, 0 untuk nonaktif
     */
    public function bulk_update_status($ids, $status) {
        if (empty($ids)) return false;
        
        $this->db->where_in('id', $ids);
        $this->db->where('role_id', 2);
        return $this->db->update($this->table, ['is_active' => $status]);
    }

    /**
     * Menghapus banyak siswa sekaligus
     * @param array $ids Array berisi ID siswa
     */
    public function bulk_delete($ids) {
        if (empty($ids)) return false;

        $this->db->where_in('id', $ids);
        $this->db->where('role_id', 2);
        return $this->db->delete($this->table);
    }
}
