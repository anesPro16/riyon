<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbl_kuis_model extends CI_Model
{
    private $table_quizzes = 'quizzes';
    private $table_questions = 'quiz_questions';
    private $table_results = 'quiz_results';
    private $table_answers = 'quiz_answers';

    public function get_quiz_by_id($id)
    {
        return $this->db->where('id', $id)->get($this->table_quizzes)->row();
    }

    // Mengambil pertanyaan (tanpa kunci jawaban agar tidak bocor di inspect element)
    public function get_questions_for_student($quiz_id)
    {
        $this->db->select('id, quiz_id, question_text, option_a, option_b, option_c, option_d');
        $this->db->where('quiz_id', $quiz_id);
        $this->db->order_by('RAND()'); // Acak urutan soal
        return $this->db->get($this->table_questions)->result();
    }

    // Load Review (Soal + Jawaban Siswa + Kunci Jawaban)
    public function get_quiz_review($quiz_id, $user_id)
    {
        // Ambil ID result dulu
        $result = $this->check_submission($quiz_id, $user_id);
        if (!$result) return [];

        $this->db->select('q.*, a.selected_option, a.is_correct');
        $this->db->from($this->table_questions . ' q');
        // Join ke tabel jawaban siswa
        $this->db->join($this->table_answers . ' a', 'a.question_id = q.id');
        $this->db->where('a.result_id', $result->id);
        $this->db->order_by('q.id', 'ASC'); 
        return $this->db->get()->result();
    }

    // Cek apakah siswa sudah mengerjakan
    public function check_submission($quiz_id, $user_id)
    {
        return $this->db->where('quiz_id', $quiz_id)
            ->where('user_id', $user_id)
            ->get($this->table_results)
            ->row();
    }

    // Proses Penilaian
    public function submit_answers($quiz_id, $user_id, $answers)
    {
        $questions = $this->db->where('quiz_id', $quiz_id)->get($this->table_questions)->result();
        
        $score = 0;
        $correct_count = 0;
        $total_questions = count($questions);
        $result_id = (function_exists('generate_ulid')) ? generate_ulid() : uniqid();
        
        $detail_answers = [];

        foreach ($questions as $q) {
            $user_ans = isset($answers[$q->id]) ? $answers[$q->id] : null;
            $is_correct = ($user_ans == $q->correct_answer) ? 1 : 0;

            if ($is_correct) $correct_count++;

            // Siapkan data untuk batch insert ke quiz_answers
            $detail_answers[] = [
                'id' => (function_exists('generate_ulid')) ? generate_ulid() : uniqid('', true),
                'result_id' => $result_id,
                'question_id' => $q->id,
                'selected_option' => $user_ans ?? '',
                'is_correct' => $is_correct
            ];
        }

        if ($total_questions > 0) {
            $score = ($correct_count / $total_questions) * 100;
        }

        // 1. Simpan Header Result
        $data_result = [
            'id' => $result_id,
            'quiz_id' => $quiz_id,
            'user_id' => $user_id,
            'score' => round($score),
            'total_correct' => $correct_count,
            'total_questions' => $total_questions
        ];
        $this->db->insert($this->table_results, $data_result);

        // 2. Simpan Detail Jawaban (Batch Insert)
        if (!empty($detail_answers)) {
            $this->db->insert_batch($this->table_answers, $detail_answers);
        }

        return $data_result;
    }

    /**
     * Mengambil daftar nilai siswa pada kuis tertentu.
     * Join dengan tabel users untuk mendapatkan Nama Siswa.
     */
    public function get_results_by_quiz_id($quiz_id)
    {
        $this->db->select('r.*, u.name as student_name, u.username');
        $this->db->from($this->table_results . ' r');
        $this->db->join('users u', 'u.id = r.user_id');
        $this->db->where('r.quiz_id', $quiz_id);
        $this->db->order_by('r.score', 'DESC'); // Urutkan nilai tertinggi
        return $this->db->get()->result();
    }

    public function get_quiz_result_by_id($id)
    {
        // return $this->db->where('result_id', $id)->get($this->table_results)->row();
        $this->db->select('result_id');
        $this->db->where('result_id', $id);
        return $this->db->get($this->table_results)->row();
    }

    /**
     * Menghapus data nilai (reset attempt siswa)
     */
    public function delete_quiz_result($id)
    {
        $this->db->where('result_id', $id);
        return $this->db->delete($this->table_results);
    }
}

/* End of file Pbl_kuis_model.php */
/* Location: ./application/models/Pbl_kuis_model.php */