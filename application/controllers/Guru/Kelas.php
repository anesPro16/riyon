<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Guru_model');
        /*$this->load->library('form_validation');
        $this->load->library('session');
        $this->load->helper('url');*/
        
        // Helper 'string' untuk membuat ID unik jika diperlukan
        $this->load->helper('string'); 

    }

    /**
     * CREATE: Menampilkan dan memproses form tambah kelas baru.
     * @param string $school_id ID sekolah tempat kelas akan dibuat
     */
    public function tambah($school_id = null)
    {
        if (!$school_id) redirect('guru/sekolah');

        // Ambil detail sekolah untuk judul
        $data['sekolah'] = $this->Guru_model->get_school_by_id($school_id);
        if (!$data['sekolah']) {
            show_error('Sekolah tidak ditemukan.');
        }

        $data['title'] = 'Buat Kelas Baru di ' . $data['sekolah']->name;
        $data['user'] = $this->session->userdata();

        // Aturan validasi
        $this->form_validation->set_rules('name', 'Nama Kelas', 'required|trim');
        $this->form_validation->set_rules('code', 'Kode Kelas', 'required|trim|is_unique[classes.code]');

        if ($this->form_validation->run() == FALSE) {
            // Tampilkan form jika validasi gagal
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar');
            $this->load->view('guru/kelas_tambah', $data);
            $this->load->view('templates/footer');
        } else {
            // Proses data jika validasi berhasil
            $user_id = $this->session->userdata('user_id');
            
            $data_kelas = [
                // Hasilkan ID unik (sesuai skema varchar(26))
                'id' => random_string('alnum', 26), 
                'user_id' => $user_id,
                'school_id'  => $school_id,
                'name'       => $this->input->post('name'),
                'code'       => $this->input->post('code')
                // created_at akan terisi otomatis oleh database
            ];

            if ($this->Guru_model->insert_kelas($data_kelas)) {
                $this->session->set_flashdata('success', 'Kelas baru berhasil ditambahkan.');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan kelas baru.');
            }
            redirect('guru/dashboard/detail/' . $school_id);
        }
    }

    /**
     * UPDATE: Menampilkan dan memproses form edit kelas.
     * @param string $kelas_id ID kelas yang akan diedit
     */
    public function edit($kelas_id = null)
    {
        if (!$kelas_id) redirect('guru/sekolah');
        
        $user_id = $this->session->userdata('user_id');
        
        // PENTING: Ambil data kelas HANYA jika milik guru yang login
        $data['kelas'] = $this->Guru_model->get_kelas_by_id_dan_guru($kelas_id, $user_id);

        if (!$data['kelas']) {
            $this->session->set_flashdata('error', 'Kelas tidak ditemukan atau Anda tidak memiliki hak akses.');
            redirect('guru/sekolah');
        }

        $data['title'] = 'Edit Kelas: ' . $data['kelas']->name;
        $data['user'] = $this->session->userdata();

        // Aturan validasi (kode mungkin perlu penanganan khusus jika 'is_unique'
        $this->form_validation->set_rules('name', 'Nama Kelas', 'required|trim');
        $this->form_validation->set_rules('code', 'Kode Kelas', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            // Tampilkan form edit
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar');
            $this->load->view('guru/kelas_edit', $data);
            $this->load->view('templates/footer');
        } else {
            // Proses update
            $data_update = [
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code')
            ];

            // PENTING: Update HANYA jika ID dan teacher_id cocok
            if ($this->Guru_model->update_kelas($kelas_id, $user_id, $data_update)) {
                $this->session->set_flashdata('success', 'Data kelas berhasil diperbarui.');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data kelas.');
            }
            // Redirect kembali ke halaman detail sekolah
            redirect('guru/dashboard/detail/' . $data['kelas']->school_id);
        }
    }

    /**
     * DELETE: Menghapus kelas.
     * @param string $kelas_id ID kelas yang akan dihapus
     */
    public function hapus($kelas_id = null)
    {
        if (!$kelas_id) redirect('guru/sekolah');

        $user_id = $this->session->userdata('id');

        // PENTING: Cek kepemilikan sebelum menghapus
        $kelas = $this->Guru_model->get_kelas_by_id_dan_guru($kelas_id, $user_id);

        if ($kelas) {
            // PENTING: Hapus HANYA jika ID dan teacher_id cocok
            $this->Guru_model->delete_kelas($kelas_id, $user_id);
            $this->session->set_flashdata('success', 'Kelas berhasil dihapus.');
            
            // Redirect kembali ke halaman detail sekolah
            redirect('guru/dashboard/detail/' . $kelas->school_id);
        } else {
            $this->session->set_flashdata('error', 'Kelas tidak ditemukan atau Anda tidak memiliki hak akses.');
            redirect('guru/sekolah');
        }
    }
}

/* End of file Kelas.php */
/* Location: ./application/controllers/Guru/Kelas.php */