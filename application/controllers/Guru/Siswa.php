<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
    }

    public function index()
    {
        $data['siswa'] = $this->Siswa_model->get_all();
        $data['title'] = 'Kelola Siswa';
        $data['user'] = $this->session->userdata();

        if ($this->input->is_ajax_request()) {
            echo json_encode($data['siswa']);
            return;
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('guru/siswa/index');
        $this->load->view('templates/footer');
    }

    public function get($id) {
        echo json_encode($this->Siswa_model->get_by_id($id));
    }

    public function save() {
        $id = $this->input->post('id');
        $username = trim($this->input->post('username'));
        $email = trim($this->input->post('email'));


        // Cek duplikat username/email
        if (!$this->Siswa_model->is_unique('username', $username, $id)) {
            echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan!']);
            return;
        }
        if (!$this->Siswa_model->is_unique('email', $email, $id)) {
            echo json_encode(['status' => 'error', 'message' => 'Email sudah digunakan!']);
            return;
        }
        $is_active = $this->input->post('is_active') ? 1 : 0;
        $data = [
            'username'  => $username,
            'email'     => $email,
            'name'      => trim($this->input->post('name')),
            'is_active' => $is_active,
        ];

        if (!$id) { // CREATE
            $data['password'] = password_hash('password', PASSWORD_DEFAULT);
            $this->Siswa_model->insert($data);
            $message = 'Siswa berhasil ditambahkan!';
        } else { // UPDATE
            $this->Siswa_model->update($id, $data);
            $message = 'Data siswa berhasil diperbarui!';
        }

        echo json_encode(['status' => 'success', 'message' => $message]);
    }

    public function delete($id) {
        $this->Siswa_model->delete($id);
        echo json_encode(['status' => 'success', 'message' => 'Siswa berhasil dihapus.']);
    }

    /**
     * Mengaktifkan beberapa siswa sekaligus
     */
    public function bulk_activate()
    {
        $ids = $this->input->post('ids');
        if (empty($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada siswa yang dipilih.']);
            return;
        }

        $this->Siswa_model->bulk_update_status($ids, 1);
        echo json_encode(['status' => 'success', 'message' => 'Siswa terpilih berhasil diaktifkan.']);
    }

    /**
     * Menonaktifkan beberapa siswa sekaligus
     */
    public function bulk_deactivate()
    {
        $ids = $this->input->post('ids');
        if (empty($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada siswa yang dipilih.']);
            return;
        }

        $this->Siswa_model->bulk_update_status($ids, 0);
        echo json_encode(['status' => 'success', 'message' => 'Siswa terpilih berhasil dinonaktifkan.']);
    }

    /**
     * Menghapus beberapa siswa sekaligus
     */
    public function bulk_delete()
    {
        $ids = $this->input->post('ids');
        if (empty($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada siswa yang dipilih.']);
            return;
        }

        $this->Siswa_model->bulk_delete($ids);
        echo json_encode(['status' => 'success', 'message' => 'Siswa terpilih berhasil dihapus.']);
    }
}
