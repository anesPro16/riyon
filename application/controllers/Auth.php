<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Extend ke MY_Auth_Controller, bukan CI_Controller
class Auth extends MY_Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Konfigurasi Spesifik Siswa
        $this->view_path = 'auth';           // Folder view: views/auth/
        $this->base_redirect = 'auth';       // URL redirect jika gagal
        
        // Hanya Siswa yang boleh login lewat controller ini
        $this->allowed_role_login = ['Siswa']; 
    }

    public function index()
    {
        $this->_process_login();
    }

    public function register()
    {
        $this->_process_register(); // Bisa tambahkan parameter Role ID Siswa jika perlu
    }
    
    public function register_action()
    {
        $this->_process_register();
    }
    
    // Override blocked jika path view berbeda
    public function blocked()
    {
        $this->load->view('auth/blocked');
    }
}

/* End of file Auth.php */
/* Location: ./application/controllers/Auth.php */