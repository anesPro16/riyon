<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function add()
    {
        $data = [
            'username' => 'guru',
            'password' => password_hash('12345678', PASSWORD_DEFAULT),
            // 'role_id'  => '01K8WA6A9HTVM98RYM1P5ZWNYH', // admin
            'role_id'  => '01K8WA6WVXEKX7JK822G9PVZG9', // guru
            'name'     => 'guru_ipas',
            'email'    => 'guru_ipas@example.com',
        ];

        // $this->User_model->insert_user($data);

        echo "User berhasil ditambahkan dengan ID ULID: ";
        var_dump($this->User_model->get_all());
    }
}
