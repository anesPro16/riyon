<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends MY_Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Konfigurasi Spesifik Staf
        $this->view_path = 'auth/staf';       // Folder view: views/auth/staf/
        $this->base_redirect = 'staf';   // URL redirect
        
        // Hanya Admin dan Guru yang boleh login lewat halaman ini
        $this->allowed_role_login = ['Admin', 'Guru'];
    }

    public function index()
    {
        $this->_process_login();
    }

    public function register()
    {
        $this->_process_register();
    }
    
    public function register_action()
    {
        $this->_process_register();
    }
}

/* End of file Index.php */
/* Location: ./application/controllers/Staf/Index.php */