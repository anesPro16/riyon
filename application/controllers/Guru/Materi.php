<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Materi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // cek login
        is_logged_in();
    }

    /**
     * Menampilkan daftar semua materi
     */
    public function index()
    {
        $data['title'] = 'Materi';
        $data['user'] = $this->session->userdata();
        $data['materi'] = $this->Materi_model->all(); 
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('materi/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Menampilkan detail satu materi
     */
    public function detail($id)
    {
        $data['materi'] = $this->Materi_model->get($id);
        if (!$data['materi']) {
            show_404();
        }

        $data['title'] = 'Materi';
        $data['user'] = $this->session->userdata();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('materi/detail', $data);
        $this->load->view('templates/footer');
    }

    /**
     * CREATE: Menampilkan form tambah materi (GET)
     * dan memproses penyimpanan materi baru (POST)
     */
    public function create()
    {
        $data['title'] = 'Materi';
        $data['user'] = $this->session->userdata();

        // Aturan validasi
        $this->form_validation->set_rules('judul', 'Judul', 'required|trim');
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            // Jika validasi gagal, tampilkan form (materi/form.php)
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('materi/form', $data);
            $this->load->view('templates/footer');
        } else {
            // Jika validasi berhasil, proses data
            
            $file_path = null; // Default null jika tidak ada file

            // Konfigurasi Upload
            $config['upload_path']   = './uploads/materi/';
            // Gabungan semua tipe file dari kode Anda
            $config['allowed_types'] = 'pdf|doc|docx|zip|jpg|jpeg|png|mp4|webm|mov|avi'; 
            $config['max_size']      = 100000; // 100MB
            $config['encrypt_name']  = TRUE; // Enkripsi nama file untuk keamanan

            // Buat folder jika belum ada
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, TRUE);
            }

            $this->upload->initialize($config);

            // Cek jika ada file di-upload
            if (!empty($_FILES['file']['name'])) {
                if ($this->upload->do_upload('file')) {
                    $uploadData = $this->upload->data();
                    $file_path = 'uploads/materi/' . $uploadData['file_name'];
                } else {
                    // Jika upload gagal, tampilkan error dan kembali
                    $this->session->set_flashdata('error', $this->upload->display_errors());
                    redirect('guru/materi/create');
                    return;
                }
            }

            // Data untuk di-insert ke database
            $data_insert = [
                'judul'      => $this->input->post('judul', true),
                'deskripsi'  => $this->input->post('deskripsi'),
                'file_path'  => $file_path,
                'created_by' => $this->session->userdata('user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->Materi_model->insert($data_insert);
            $this->session->set_flashdata('success', 'Materi baru berhasil dibuat!');
            redirect('guru/materi');
        }
    }

    /**
     * UPDATE: Menampilkan form edit materi (GET)
     * dan memproses pembaruan materi (POST)
     */
    public function edit($id)
    {
        $data['title'] = 'Materi';
        $data['user'] = $this->session->userdata();
        $data['materi'] = $this->Materi_model->get($id);

        if (!$data['materi']) {
            show_404();
        }

        // Aturan validasi
        $this->form_validation->set_rules('judul', 'Judul', 'required|trim');
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            // Jika validasi gagal, tampilkan form (materi/form.php)
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('materi/form', $data);
            $this->load->view('templates/footer');
        } else {
            // Jika validasi berhasil, proses update
            
            $materi_lama = $data['materi'];
            $file_path = $materi_lama->file_path; // Default: gunakan file lama

            // Konfigurasi Upload
            $config['upload_path']   = './uploads/materi/';
            $config['allowed_types'] = 'pdf|doc|docx|zip|jpg|jpeg|png|mp4|webm|mov|avi';
            $config['max_size']      = 100000; // 100MB
            $config['encrypt_name']  = TRUE;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, TRUE);
            }

            $this->upload->initialize($config);

            // Cek jika ada file BARU di-upload
            if (!empty($_FILES['file']['name'])) {
                // 1. Upload file baru
                if ($this->upload->do_upload('file')) {
                    $uploadData = $this->upload->data();
                    $file_path = 'uploads/materi/' . $uploadData['file_name']; // Path file baru

                    // 2. Hapus file lama (jika ada)
                    if (!empty($materi_lama->file_path) && file_exists($materi_lama->file_path)) {
                        unlink($materi_lama->file_path);
                    }
                } else {
                    // Jika upload gagal, tampilkan error dan kembali
                    $this->session->set_flashdata('error', $this->upload->display_errors());
                    redirect('guru/materi/edit/' . $id);
                    return;
                }
            }

            // Data untuk di-update
            $data_update = [
                'judul'      => $this->input->post('judul', true),
                'deskripsi'  => $this->input->post('deskripsi'),
                'file_path'  => $file_path, // Path file (bisa lama atau baru)
                // 'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->Materi_model->update($id, $data_update);
            $this->session->set_flashdata('success', 'Materi berhasil diperbarui!');
            redirect('guru/materi');
        }
    }

    /**
     * DELETE: Menghapus materi (termasuk file fisiknya)
     */
    public function delete($id)
    {
        // 1. Ambil data materi untuk mendapatkan file_path
        $materi = $this->Materi_model->get($id);
        if (!$materi) {
            show_404();
        }

        // 2. Hapus file fisik dari server (JIKA ADA)
        if (!empty($materi->file_path) && file_exists($materi->file_path)) {
            unlink($materi->file_path);
        }

        // 3. Hapus data dari database
        $this->Materi_model->delete($id);
        
        $this->session->set_flashdata('success', 'Materi berhasil dihapus!');
        redirect('guru/materi');
    }
}

/* End of file Materi.php */
/* Location: ./application/controllers/Guru/Materi.php */