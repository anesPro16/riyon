<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model 
{
    private $table = 'users';
    private $role_table = 'roles'; // Asumsi nama tabel role Anda

    public function get($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function get_by_username($username)
    {
        return $this->db->get_where($this->table, ['username' => $username])->row();
    }

    public function get_by_email($email)
    {
        return $this->db->get_where($this->table, ['email' => $email])->row();
    }

    /**
     * Mendapatkan semua user berdasarkan nama role (cth: 'siswa', 'guru')
     */
    public function get_by_role_name($role_name)
    {
        $role_id = $this->get_role_id_by_name($role_name);
        if (!$role_id) {
            return []; // Kembalikan array kosong jika role tidak ditemukan
        }
        
        $this->db->where('role_id', $role_id);
        $this->db->order_by('id', 'DESC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Mengambil pengguna berdasarkan nama peran,
     * khusus untuk 'siswa' yang belum memiliki kelas.
     */
    public function get_students_by_role_name($role_name)
    {
        $role_id = $this->get_role_id_by_name($role_name);
        if (!$role_id) {
            return []; // Kembalikan array kosong jika role tidak ditemukan
        }
        
        // Hanya terapkan logika khusus ini jika perannya adalah 'siswa'
        if ($role_name == 'siswa') {
            
            // SELECT users.*
            $this->db->select('users.*');
            
            // FROM users
            $this->db->from($this->table . ' AS users'); // $this->table adalah 'users'
            
            // LEFT JOIN students ON users.id = students.user_id
            $this->db->join('students', 'users.id = students.user_id', 'left');
            
            // WHERE users.role_id = '...'
            $this->db->where('users.role_id', $role_id);
            
            // AND (students.user_id IS NULL OR students.class_id IS NULL)
            // Ini adalah logika utamanya:
            // 1. Ambil siswa yang tidak ada di tabel 'students' (students.user_id IS NULL)
            // 2. ATAU ambil siswa yang ada di tabel 'students' tapi 'class_id'-nya NULL
            $this->db->group_start();
            $this->db->where('students.user_id IS NULL');
            $this->db->or_where('students.class_id IS NULL');
            $this->db->group_end();

            $this->db->order_by('users.id', 'DESC');
            return $this->db->get()->result();

        } else {
            // Logika default jika mencari peran selain 'siswa'
            $this->db->where('role_id', $role_id);
            $this->db->order_by('id', 'DESC');
            return $this->db->get($this->table)->result();
        }
    }

    /**
     * Helper untuk mendapatkan ID dari sebuah role berdasarkan namanya
     */
    public function get_role_id_by_name($role_name)
    {
        $role = $this->db->get_where($this->role_table, ['role' => $role_name])->row();
        return $role ? $role->id : null;
    }

    public function insert_user($data)
    {
        // Jika kolom id belum diisi, generate ULID otomatis
        if (!isset($data['id']) || empty($data['id'])) {
            $data['id'] = generate_ulid();
        }

        return $this->db->insert($this->table, $data);
    }

    public function insert($payload)
    {
        return $this->db->insert($this->table, $payload);
    }

    public function update($id, $payload)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $payload);
    }

    public function delete($id)
    {
        // Pastikan Anda memiliki 'on delete cascade' di foreign key
        // atau tangani penghapusan data terkait secara manual di sini.
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function check_login($username, $password)
    {
        $user = $this->get_by_username($username);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    public function check_is_teacher($role_id)
    {
        // Memanfaatkan fungsi generic yang sudah ada
        return $this->check_user_role($role_id, ['Guru']);
    }

    /**
     * Memeriksa apakah role_id yang diberikan ada di dalam
     * daftar nama peran yang diizinkan.
     *
     * @param string $role_id ID peran untuk diperiksa
     * @param array  $allowed_role_names Array nama peran (e.g., ['Admin', 'Guru'])
     * @return bool
     */
    public function check_user_role($role_id, $allowed_role_names = [])
    {
        // Jika role_id kosong atau tidak ada peran yang diizinkan,
        // langsung kembalikan false.
        if (empty($role_id) || empty($allowed_role_names)) {
            return false;
        }

        // 1. Query ke tabel roles
        $role = $this->db->select('role')
                         ->get_where('roles', ['id' => $role_id])
                         ->row();

        // 2. Cek jika peran ditemukan DAN namanya ada di dalam array
        //    $allowed_role_names
        if ($role && in_array($role->role, $allowed_role_names)) {
            return true;
        }

        // Jika tidak, kembalikan false
        return false;
    }

    public function get_profile($user_id)
    {
        $this->db->select('u.*, r.role');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.id = u.role_id');
        $this->db->where('u.id', $user_id);
        return $this->db->get()->row();
    }

    public function update_profile($user_id, $data)
    {
        $this->db->where('id', $user_id);
        $this->db->update('users', $data);
    }

    public function is_username_exist($username, $exclude_id)
    {
        $this->db->where('username', $username);
        $this->db->where('id !=', $exclude_id);
        return $this->db->get('users')->num_rows() > 0;
    }
}

/* End of file User_model.php */
/* Location: ./application/models/User_model.php */