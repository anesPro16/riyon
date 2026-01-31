<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    // Fungsi untuk halaman login
    public function index()
    {
        // Jika sudah login, arahkan sesuai role
        if ($this->session->userdata('logged_in')) {
            $role = $this->session->userdata('role');
            if ($role === 'Admin') {
                redirect('admin/dashboard');
            }
            elseif ($role == 'Guru') {
                redirect('guru/dashboard');
            }
            else {
                redirect('siswa/dashboard');
            }
        }

        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Login Page';
            $this->load->view('auth/guru/login');
        } else {
            // validasinya success
            $this->_login();
        }
    }

    private function _login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $user = $this->db->get_where('users', ['username' => $username])->row_array();

        $role_name = $this->db->get_where('roles', ['id' => $user['role_id']])->row();

        if ($role_name->role == 'Siswa') {
            $this->session->set_flashdata('error', 'Anda Tidak boleh akses halaman ini!');
            redirect('auth');
        }

        // jika usernya ada
        if ($user) {
            // jika usernya aktif
            if ($user['is_active'] == 1) {
                // cek password
                if (password_verify($password, $user['password'])) {
                    $data = [
                        'username' => $user['username'],
                        'name' => $user['name'],
                        'image' => $user['image'],
                        'user_id' => $user['id'],
                        'role_id' => $user['role_id'],
                        'role' => $role_name->role,
                        'logged_in' => TRUE
                    ];
                    $this->session->set_userdata($data);
                    if ($role_name->role === 'Admin') {
                        redirect('admin/dashboard/classes');
                    } elseif ($role_name->role === 'Guru') {
                        redirect('guru/dashboard');
                    } elseif ($role_name->role === 'Siswa') {
                        redirect('siswa/dashboard');
                    } else {
                        redirect('auth/blocked');
                    }
                } else {
                    $this->session->set_flashdata('error', 'password salah!');
                    redirect('guru/auth');
                }
            } else {
                $this->session->set_flashdata('error', 'Akun belum diaktifkan!');
                redirect('guru/auth');
            }
        } else {
            $this->session->set_flashdata('error', 'Akun belum terdaftar!');
            redirect('guru/auth');
        }
    }

    // Fungsi untuk halaman register
    public function register()
    {
        $this->load->view('auth/guru/register');
    }

    // Fungsi logika proses register
    public function register_action()
    {

        $this->form_validation->set_rules('name', 'Nama', 'trim|required', [
            'required' => 'Nama wajib diisi!'
        ]);
        $this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[users.username]', [
            'required' => 'Username wajib diisi!',
            'is_unique' => 'Username sudah terdaftar!'
        ]);
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]', [
            'required' => 'Email wajib diisi!',
            'is_unique' => 'Email sudah terdaftar!'
        ]);
        $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[5]|matches[password_confirm]', [
            'required' => 'Password wajib diisi!',
            'matches' => 'Password tidak sama!',
            'min_length' => 'Password minimal 5 karakter!'
        ]);
        $this->form_validation->set_rules('password_confirm', 'Password', 'required|trim|matches[password]');
        

        if ($this->form_validation->run() == false) {

            $data['title'] = 'Halaman Pendaftaran';
            $this->load->view('auth/guru/register');
        } else {
            // validasinya success
            $data = [
                'id'       => generate_ulid(),
                'name'     => $this->input->post('name', TRUE),
                'email'    => $this->input->post('email', TRUE),
                'username' => $this->input->post('username', TRUE),
                'password' => password_hash($this->input->post('password', TRUE), PASSWORD_DEFAULT),
            ];

            // Cek username sudah digunakan
            $check = $this->User_model->get_by_username($data['username']);
            if ($check) {
                $this->session->set_flashdata('error', 'Username sudah digunakan!');
                redirect('guru/auth/register');
            }
            $this->User_model->insert($data);
            redirect('guru/auth');
            $this->session->set_flashdata('success', 'Pendaftaran berhasil, silakan login.');
        }

    }

    // Fungsi untuk logout akun
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('guru/auth');
    }

    // Fungsi untuk menanhan akses user
    public function blocked()
    {
        if ($this->session->userdata()) {
            $data['user'] = $this->session->userdata();
        } else {
            $data['user'] = [];
        }
        $this->load->view('templates/header', $data);
        $this->load->view('auth/blocked');
        $this->load->view('templates/footer');
    }

    // Fungsi untuk mengecek apakah telah login
    public function cek_login()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    //  Fungsi untuk mengecek role user
    public function cek_role($role)
    {
        if ($this->session->userdata('role') != $role) {
            show_error('Akses ditolak: Anda tidak memiliki izin untuk halaman ini.', 403);
        }
    }
}
