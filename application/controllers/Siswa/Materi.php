<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Materi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
    }

    public function index()
    {
        $q = $this->input->get('q'); // Ambil kata kunci dari input form

        if (!empty($q)) {
            $this->db->like('judul', $q);
            $this->db->or_like('deskripsi', $q);
        }

        $data['materi'] = $this->db->get('materi')->result();
        $data['title'] = 'Materi';
        $data['user'] = $this->session->userdata();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('materi/index', $data);
        $this->load->view('templates/footer');
    }

    public function detail($id)
    {
        $data['materi'] = $this->Materi_model->get($id);
        $data['title'] = 'Detail Materi';
        $data['user'] = $this->session->userdata();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('materi/detail', $data);
        $this->load->view('templates/footer');
    }

}
