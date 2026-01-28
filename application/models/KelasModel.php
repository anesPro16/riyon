<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KelasModel extends CI_Model
{
    private $table = 'kelas';

    public function get_all_by_guru($guru_id)
    {
        return $this->db->get_where($this->table, ['guru_id' => $guru_id])->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function get_siswa_by_kelas($kelas_id)
    {
        $this->db->select('siswa_kelas.id as rel_id, users.id as siswa_id, users.name, users.email, users.username');
        $this->db->from('siswa_kelas');
        $this->db->join('users', 'users.id = siswa_kelas.siswa_id');
        $this->db->where('siswa_kelas.kelas_id', $kelas_id);
        return $this->db->get()->result();
    }

    public function is_siswa_exists($kelas_id, $siswa_id)
    {
        $query = $this->db->get_where('siswa_kelas', [
            'kelas_id' => $kelas_id,
            'siswa_id' => $siswa_id
        ]);
        return $query->num_rows() > 0;
    }

    public function add_siswa($kelas_id, $siswa_id)
    {
        if (!$this->is_siswa_exists($kelas_id, $siswa_id)) {
            return $this->db->insert('siswa_kelas', [
                'kelas_id' => $kelas_id,
                'siswa_id' => $siswa_id
            ]);
        }
        return false;
    }

    public function remove_siswa($rel_id)
    {
        return $this->db->delete('siswa_kelas', ['id' => $rel_id]);
    }

}
