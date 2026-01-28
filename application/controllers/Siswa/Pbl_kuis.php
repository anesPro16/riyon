<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_kuis extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in(); 
    }

    // Halaman Pengerjaan Kuis
    public function kuis_detail($quiz_id = null)
    {
        if (!$quiz_id) redirect('siswa/pbl');
        
        $quiz = $this->Kuis_model->get_quiz_by_id($quiz_id);
        if (!$quiz) show_404();

        $user_id = $this->session->userdata('user_id');
        $result = $this->Kuis_model->check_submission($quiz_id, $user_id);

        $data = [
            'title' => 'Kuis: ' . $quiz->title,
            'quiz' => $quiz,
            'result' => $result,
            'is_done' => ($result) ? true : false, // Flag untuk JS
            'class_id' => $quiz->class_id,
            'user' => $this->session->userdata(),
            'url_name' => 'siswa'
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('siswa/kuis_detail', $data);
        $this->load->view('templates/footer');
    }

    // AJAX: Ambil Soal (Load via CrudHandler)
    public function get_questions($quiz_id)
    {
        $data = $this->Kuis_model->get_questions_for_student($quiz_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    // AJAX: Ambil Review (Mode Lihat Nilai & Jawaban)
    public function get_review($quiz_id)
    {
        $user_id = $this->session->userdata('user_id');
        $data = $this->Kuis_model->get_quiz_review($quiz_id, $user_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    // AJAX: Submit Jawaban
    public function submit_quiz()
    {
        $quiz_id = $this->input->post('quiz_id');
        $answers = $this->input->post('answers'); // Array [question_id => 'A']
        $user_id = $this->session->userdata('user_id');

        if (!$quiz_id || empty($answers)) {
            echo json_encode(['status' => 'error', 'message' => 'Jawaban tidak boleh kosong.']);
            return;
        }

        // Cek duplikasi submit
        $exists = $this->Kuis_model->check_submission($quiz_id, $user_id);
        if ($exists) {
            echo json_encode(['status' => 'error', 'message' => 'Anda sudah mengerjakan kuis ini.']);
            return;
        }

        $result = $this->Kuis_model->submit_answers($quiz_id, $user_id, $answers);

        echo json_encode([
            'status' => 'success',
            'message' => 'Kuis berhasil dikirim. Nilai Anda: ' . $result['score'],
            'score' => $result['score'],
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }
}

/* End of file Pbl_kuis.php */
/* Location: ./application/controllers/Siswa/Pbl_kuis.php */