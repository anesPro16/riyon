<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Forum extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Forum_model');
        $this->load->model('Komentar_model');
        $this->load->library('session');
        $this->load->helper(['url', 'form']);
    }

    // Menampilkan daftar forum dari guru
    public function index()
    {
        $data['forum'] = $this->Forum_model->get_all_forum();
        $data['title'] = 'Forum';
        $data['user'] = $this->session->userdata();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('siswa/forum_siswa/index', $data);
        $this->load->view('templates/footer');
    }

    // Menampilkan detail forum dan komentar
    public function detail($id)
    {
        $data['forum'] = $this->Forum_model->get_by_id($id);
        $data['komentar'] = $this->Komentar_model->get_by_forum($id);
        $data['title'] = 'Forum';
        $data['user'] = $this->session->userdata();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('siswa/forum_siswa/detail', $data);
        $this->load->view('templates/footer');
    }

    // Tambah komentar siswa
    public function tambah_komentar()
    {
        $data = [
            'forum_id' => $this->input->post('forum_id'),
            'user_id' => $this->session->userdata('user_id'),
            'isi_komentar' => $this->input->post('isi_komentar'),
            'tanggal' => date('Y-m-d H:i:s')
        ];

        $this->Komentar_model->insert($data);
        redirect('siswa/forum/detail/' . $this->input->post('forum_id'));
    }

    // Hapus komentar (hanya jika milik sendiri)
    public function hapus_komentar($id)
    {
        $komentar = $this->Komentar_model->get_by_id($id);
        $user_id = $this->session->userdata('user_id');

        if ($komentar && $komentar->user_id == $user_id) {
            $this->Komentar_model->delete($id);
            $this->session->set_flashdata('success', 'Komentar berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Anda tidak dapat menghapus komentar ini.');
        }

        redirect('siswa/forum/detail/' . $komentar->forum_id);
    }
}
