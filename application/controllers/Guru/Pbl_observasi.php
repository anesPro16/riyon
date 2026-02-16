<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_observasi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in(); // Pastikan helper login aktif
        $this->load->model('Pbl_observasi_model');
    }

    // Halaman Detail (Menampilkan List Upload Siswa)
    public function detail($slot_id = null)
    {
        if (!$slot_id) redirect('guru/pbl');

        $slot = $this->Pbl_observasi_model->get_slot_by_id($slot_id);
        if (!$slot) show_404();

        $data['title'] = 'Halaman Detail Ruang Observasi';
        $data['slot'] = $slot;
        $data['class_id'] = $slot->class_id; // Untuk tombol kembali
        
        // Data user & role untuk view
        $data['user'] = $this->session->userdata();
        $data['url_name'] = 'guru'; // atau dinamis sesuai role
        $role_id = $this->session->userdata('role_id');
        // $data['is_admin_or_guru'] = ... (logika cek role Anda)
        $data['is_admin_or_guru'] = true; // Hardcode true karena ini controller Guru

        $this->load->view('templates/header', $data);
        $this->load->view('guru/pbl_observasi_detail', $data);
        $this->load->view('templates/footer');
    }

    // --- AJAX METHODS ---

    // Get Data untuk Tabel
    public function get_uploads($slot_id)
    {
        $data = $this->Pbl_observasi_model->get_uploads_by_slot($slot_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    // Delete Upload
    public function delete_upload($id = null)
    {
        $getData = $this->Pbl_observasi_model->get_observation_id($id);

        if ($getData) {
            $this->Pbl_observasi_model->delete_upload($id);
            $this->Pbl_observasi_model->delete_grade_by_observation_id_by_user_id($getData->observation_id, $getData->user_id);
            $response = ['status' => 'success', 'message' => 'File observasi berhasil dihapus.'];
        } else {
            $response = ['status' => 'error', 'message' => 'ID tidak valid.'];
        }
        
        // CSRF Hash refresh
        $response['csrf_hash'] = $this->security->get_csrf_hash();
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function get_grades($slot_id)
    {
        $data = $this->Pbl_observasi_model->get_grades_by_slot($slot_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function save_grade()
    {
        $this->form_validation->set_rules('user_id', 'Siswa', 'required');
        $this->form_validation->set_rules('score', 'Nilai', 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('feedback', 'Feedback', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
            return;
        }

        $id = $this->input->post('id'); // ID dari tabel results (jika edit)
        $observation_id = $this->input->post('observation_slot_id');
        $user_id = $this->input->post('user_id');
        
        $payload = [
            'observation_id' => $observation_id,
            'user_id' => $user_id,
            'score' => $this->input->post('score'),
            'feedback' => $this->input->post('feedback')
        ];


        // Cek apakah siswa ini sudah dinilai sebelumnya di slot ini?
        $existing = $this->Pbl_observasi_model->check_grade_exists($observation_id, $user_id);

        if ($id) {
            // Mode Edit via ID
            $this->Pbl_observasi_model->update_grade($id, $payload);
            $msg = 'Nilai diperbarui.';
        } elseif ($existing) {
            // Mode Insert tapi data sudah ada -> Update saja
            $this->Pbl_observasi_model->update_grade($existing->id, $payload);
            $msg = 'Nilai diperbarui (Siswa sudah dinilai sebelumnya).';
        } else {
            // Mode Insert Baru
            $payload['id'] = generate_ulid();
            $this->Pbl_observasi_model->insert_grade($payload);
            $msg = 'Nilai berhasil disimpan.';
        }

        echo json_encode(['status' => 'success', 'message' => $msg, 'csrf_hash' => $this->security->get_csrf_hash()]);
    }

    public function delete_grade()
    {
        $id = $this->input->post('id');

        $this->Pbl_observasi_model->delete_grade($id);
        echo json_encode(['status' => 'success', 'message' => 'Nilai dihapus.', 'csrf_hash' => $this->security->get_csrf_hash()]);
    }
    
}

/* End of file Pbl_observasi.php */
/* Location: ./application/controllers/Guru/Pbl_observasi.php */