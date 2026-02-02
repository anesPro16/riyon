<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Parent Controller untuk menangani logika otentikasi
 * agar tidak ada duplikasi kode di controller Siswa dan Guru.
 */
class MY_Auth_Controller extends CI_Controller
{
    protected $view_path = '';      // Folder view (misal: 'auth/' atau 'auth/guru/')
    protected $base_redirect = '';  // Redirect url (misal: 'auth' atau 'guru/auth')
    protected $allowed_role_login = []; // Role yang boleh login di halaman ini

    // Konstanta Batas Harian
    const DAILY_REGISTER_LIMIT = 1;

    // Logika Redirect Dashboard Terpusat
    protected function _redirect_dashboard()
    {
        $role = $this->session->userdata('role');
        switch ($role) {
            case 'Admin': redirect('admin/dashboard'); break;
            case 'Guru':  redirect('guru/dashboard'); break;
            case 'Siswa': redirect('siswa/dashboard'); break;
            default: redirect('auth/blocked'); break;
        }
    }

    // Logika Utama Login
    protected function _process_login()
    {
        // Cek Session
        if ($this->session->userdata('logged_in')) {
            $this->_redirect_dashboard();
        }

        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Login Page';
            $this->load->view($this->view_path . '/login', $data);
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $user = $this->Auth_model->get_user_by_username($username);

            // 1. Cek User Ada
            if ($user) {
                // 2. Cek Role yang diizinkan login di halaman ini
                // Misal: Siswa tidak boleh login di halaman Guru
                if (!in_array($user['role_name'], $this->allowed_role_login)) {
                    $this->session->set_flashdata('error', 'Akses ditolak! Role Anda tidak valid untuk halaman ini.');
                    redirect($this->base_redirect);
                }

                // 3. Cek Status Aktif
                if ($user['is_active'] == 1) {
                    // 4. Cek Password
                    if (password_verify($password, $user['password'])) {
                        $session_data = [
                            'user_id'   => $user['id'],
                            'username'  => $user['username'],
                            'name'      => $user['name'],
                            'image'     => $user['image'],
                            'role_id'   => $user['role_id'],
                            'role'      => $user['role_name'],
                            'logged_in' => TRUE
                        ];
                        $this->session->set_userdata($session_data);
                        $this->_redirect_dashboard();
                    } else {
                        $this->session->set_flashdata('error', 'Password salah!');
                        redirect($this->base_redirect);
                    }
                } else {
                    $this->session->set_flashdata('error', 'Akun belum diaktifkan!');
                    redirect($this->base_redirect);
                }
            } else {
                $this->session->set_flashdata('error', 'Akun belum terdaftar!');
                redirect($this->base_redirect);
            }
        }
    }

    // Logika Utama Register
    // Logika Utama Register
    protected function _process_register($default_role_id = null)
    {
        // 1. AMBIL DATA KUOTA
        $today_count = $this->Auth_model->count_registrations_today();
        $limit = self::DAILY_REGISTER_LIMIT;
        $is_quota_full = ($today_count >= $limit);
        
        // 2. LOGIKA SAAT TOMBOL SUBMIT DITEKAN (METHOD POST)
        if ($this->input->method() === 'post') {
            
            // Security Check: Jika kuota penuh tapi ada yang memaksa submit (misal via Postman/Inspect Element)
            if ($is_quota_full) {
                $this->session->set_flashdata('error', 'Mohon maaf, kuota pendaftaran harian sudah penuh. Silakan coba lagi besok!');
                redirect($this->base_redirect . '/register'); 
                return; 
            }

            // Lanjut validasi normal
            $this->form_validation->set_rules('name', 'Nama', 'trim|required');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[users.username]', [
                'is_unique' => 'Username sudah digunakan!'
            ]);
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]', [
                'is_unique' => 'Email sudah terdaftar!'
            ]);
            $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[5]|matches[password_confirm]');
            $this->form_validation->set_rules('password_confirm', 'Konfirmasi Password', 'required|trim|matches[password]');

            if ($this->form_validation->run() == true) {
                // Proses Insert Data
                $data = [
                    'id'       => generate_ulid(),
                    'name'     => $this->input->post('name', TRUE),
                    'email'    => $this->input->post('email', TRUE),
                    'username' => $this->input->post('username', TRUE),
                    'password' => password_hash($this->input->post('password', TRUE), PASSWORD_DEFAULT),
                    'image'    => 'default.jpg',
                    'is_active'=> 0, 
                    'created_at' => date('Y-m-d H:i:s') 
                ];
                
                if($default_role_id) {
                    $data['role_id'] = $default_role_id; 
                }

                if ($this->Auth_model->register_user($data)) {
                    $this->session->set_flashdata('success', 'Pendaftaran berhasil, tunggu aktivasi akun sebelum login!');
                    redirect($this->base_redirect);
                    return;
                } else {
                    $this->session->set_flashdata('error', 'Terjadi kesalahan sistem.');
                    redirect($this->base_redirect . '/register');
                    return;
                }
            }
        }

        // 3. LOGIKA MENAMPILKAN VIEW (METHOD GET ATAU VALIDASI GAGAL)
        // Bagian ini akan dieksekusi jika:
        // a. User baru buka halaman (GET)
        // b. User submit tapi validasi error
        
        $data['title'] = 'Halaman Pendaftaran';
        
        // Kirim info kuota ke View untuk mematikan tombol
        $data['is_quota_full'] = $is_quota_full;
        $data['remaining_quota'] = ($limit - $today_count) < 0 ? 0 : ($limit - $today_count);
        $data['limit'] = $limit;

        // Load view tanpa redirect loop
        $this->load->view($this->view_path . '/register', $data);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect($this->base_redirect);
    }
}