<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pbl_kuis extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
    is_logged_in();
  }

  // --- [FUNGSI BARU] Halaman Detail Kuis ---
  public function kuis_detail($quiz_id = null)
  {
    if (!$quiz_id) redirect('guru/pbl'); // Arahkan ke halaman utama pbl

    $quiz = $this->Kuis_model->get_quiz_by_id($quiz_id);
    if (!$quiz) show_404();

    $data['title'] = 'Detail Kuis: ' . $quiz->title;
    $data['quiz'] = $quiz;
    $data['user'] = $this->session->userdata();

    $this->load->view('templates/header', $data);
    // $this->load->view('templates/sidebar'); // (Opsional, jika Anda pakai)
    $this->load->view('guru/kuis_detail', $data); // View baru kita
    $this->load->view('templates/footer');
  }

  // --- [FUNGSI BARU] AJAX CRUD untuk Pertanyaan Kuis ---

  // AJAX: Get list pertanyaan
  public function get_quiz_questions($quiz_id)
  {
    $data = $this->Kuis_model->get_questions_by_quiz_id($quiz_id);
    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($data));
  }

  // AJAX: Simpan (Create/Update) pertanyaan
  public function save_quiz_question()
  {
    // Validasi
    $this->form_validation->set_rules('question_text', 'Teks Pertanyaan', 'required');
    $this->form_validation->set_rules('option_a', 'Opsi A', 'required');
    $this->form_validation->set_rules('option_b', 'Opsi B', 'required');
    $this->form_validation->set_rules('option_c', 'Opsi C', 'required');
    $this->form_validation->set_rules('option_d', 'Opsi D', 'required');
    $this->form_validation->set_rules('correct_answer', 'Jawaban Benar', 'required');

    if ($this->form_validation->run() === FALSE) {
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
      return;
    }

    $question_id = $this->input->post('id');
    $payload = [
      'quiz_id' => $this->input->post('quiz_id'),
      'question_text' => $this->input->post('question_text'),
      'option_a' => $this->input->post('option_a'),
      'option_b' => $this->input->post('option_b'),
      'option_c' => $this->input->post('option_c'),
      'option_d' => $this->input->post('option_d'),
      'correct_answer' => $this->input->post('correct_answer'),
    ];

    if ($question_id) {
    	$getQuestion = $this->Kuis_model->get_questions_by_id($question_id);
      if (!$getQuestion) {
        echo json_encode(['status'=>'error','message'=>'Soal Kuis tidak ada!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
      }

      $this->Kuis_model->update_quiz_question($question_id, $payload);
      $msg = 'Pertanyaan diperbarui.';
    } else {
      $payload['question_id'] = generate_ulid();
      $this->Kuis_model->insert_quiz_question($payload);
      $msg = 'Pertanyaan ditambahkan.';
    }

    echo json_encode([
      'status' => 'success',
      'message' => $msg,
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

  // AJAX: Hapus pertanyaan
  public function delete_quiz_question()
  {
    $id = $this->input->post('id');

    $getQuestion = $this->Kuis_model->get_questions_by_id($id);
    if (!$getQuestion) {
      echo json_encode(['status'=>'error','message'=>'Gagal hapus soal Kuis!', 'csrf_hash' => $this->security->get_csrf_hash()]);
      return;
    }

    $this->Kuis_model->delete_quiz_question($id);

    echo json_encode([
      'status' => 'success',
      'message' => 'berhasil hapus pertanyaan',
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

  // --- [FUNGSI BARU] Import / Export (Placeholder) ---
  public function export_quiz($quiz_id)
  {
    // 1. Ambil data
    $quiz = $this->Kuis_model->get_quiz_by_id($quiz_id);
    $questions = $this->Kuis_model->get_questions_by_quiz_id($quiz_id);

    if (!$quiz) {
      show_404();
      return;
    }

    // 2. Tentukan nama file
    $safe_title = preg_replace('/[^a-zA-Z0-9-]/', '', strtolower($quiz->title));
    $filename = 'export-kuis-' . $safe_title . '-' . date('Ymd') . '.csv';

    // 3. Set Header HTTP untuk download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // 4. Buka stream output PHP
    $output = fopen('php://output', 'w');

    // 5. Tulis header CSV
    $headers = [
      'question_text',
      'option_a',
      'option_b',
      'option_c',
      'option_d',
      'correct_answer'
    ];
    fputcsv($output, $headers);

    // 6. Tulis data baris per baris
    foreach ($questions as $q) {
      $row = [
        $q->question_text,
        $q->option_a,
        $q->option_b,
        $q->option_c,
        $q->option_d,
        $q->correct_answer
      ];
      fputcsv($output, $row);
    }

    // 7. Tutup stream
    fclose($output);
    exit(); // Hentikan eksekusi script
  }

  public function import_quiz()
  {
    // 1. Dapatkan Quiz ID dari form
    $quiz_id = $this->input->post('quiz_id_import');
    if (!$quiz_id) {
      $this->session->set_flashdata('import_error', 'Quiz ID tidak ditemukan.');
      redirect('guru/pbl'); // Redirect ke halaman utama
      return;
    }

    // 2. Konfigurasi Upload File
    
    // [PERBAIKAN] Tentukan path folder dengan FCPATH agar absolut dan pasti
    $upload_dir = FCPATH . 'uploads/temp/';

    // [PERBAIKAN] Cek apakah direktori ada dan bisa ditulisi
    if (!is_dir($upload_dir)) {
      // Coba buat folder jika belum ada
      if (!mkdir($upload_dir, 0777, TRUE)) {
        // Gagal membuat folder
        $this->session->set_flashdata('import_error', 'Upload gagal: Folder ' . $upload_dir . ' tidak ada dan tidak bisa dibuat.');
        redirect('guru/pbl_kuis/kuis_detail/' . $quiz_id);
        return;
      }
    }

    if (!is_writable($upload_dir)) {
      // Folder ada tapi tidak bisa ditulisi
      $this->session->set_flashdata('import_error', 'Upload gagal: Folder ' . $upload_dir . ' tidak bisa ditulisi (permission denied).');
      redirect('guru/pbl_kuis/kuis_detail/' . $quiz_id);
      return;
    }

    $config['upload_path']   = $upload_dir; // Gunakan path yang sudah divalidasi
    $config['allowed_types'] = 'csv';
    $config['max_size']      = 2048; // 2MB
    $config['encrypt_name']  = TRUE;

    $this->load->library('upload', $config);
    $this->upload->initialize($config);

    if (!$this->upload->do_upload('import_file')) {
      // 3. Tangani error upload
      $error_msg = $this->upload->display_errors('', '');
      $this->session->set_flashdata('import_error', 'Upload file gagal: ' . $error_msg);
      redirect('guru/pbl_kuis/kuis_detail/' . $quiz_id);
      return;
    }

    // 4. Dapatkan path file yang di-upload
    $file_data = $this->upload->data();
    $file_path = $file_data['full_path'];

    // 5. Baca dan Parse file CSV
    $batch_data = [];
    $required_headers = ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer'];
    $valid_answers = ['A', 'B', 'C', 'D'];

    $handle = fopen($file_path, 'r');
    if ($handle !== FALSE) {
      // Baca baris header
      $headers = fgetcsv($handle);
      if (empty($headers) || count($headers) !== count($required_headers) || array_diff($headers, $required_headers)) {
        $this->session->set_flashdata('import_error', 'Format header CSV tidak valid. Pastikan kolom sesuai template (urutan juga harus sama).');
        unlink($file_path); // Hapus file temp
        redirect('guru/pbl_kuis/kuis_detail/' . $quiz_id);
        return;
      }
      
      $row_number = 2; // Mulai dari baris 2 (baris 1 adalah header)
      // Baca data baris per baris
      while (($row = fgetcsv($handle)) !== FALSE) {
        // Pastikan jumlah kolom cocok
        if (count($row) != count($headers)) {
          $this->session->set_flashdata('import_error', 'Data di baris ' . $row_number . ' tidak lengkap.');
          unlink($file_path);
          redirect('guru/pbl_kuis/kuis_detail/' . $quiz_id);
          return;
        }

        // Gabungkan header dengan data baris
        $data = array_combine($headers, $row);

        // Validasi data sederhana
        if (empty($data['question_text']) || empty($data['option_a']) || empty($data['correct_answer'])) {
          $this->session->set_flashdata('import_error', 'Data pertanyaan, opsi, atau jawaban benar tidak boleh kosong di baris ' . $row_number . '.');
          unlink($file_path);
          redirect('guru/pbl_kuis/kuis_detail/' . $quiz_id);
          return;
        }
        if (!in_array(strtoupper($data['correct_answer']), $valid_answers)) {
          $this->session->set_flashdata('import_error', 'Jawaban benar di baris ' . $row_number . ' harus A, B, C, atau D.');
          unlink($file_path);
          redirect('guru/pbl_kuis/kuis_detail/' . $quiz_id);
          return;
        }

        // Siapkan data untuk batch insert
        $batch_data[] = [
          'question_id' => generate_ulid(),
          'quiz_id' => $quiz_id,
          'question_text' => $data['question_text'],
          'option_a' => $data['option_a'],
          'option_b' => $data['option_b'],
          'option_c' => $data['option_c'],
          'option_d' => $data['option_d'],
          'correct_answer' => strtoupper($data['correct_answer'])
        ];
        $row_number++;
      }
      fclose($handle);
    }

    // 6. Masukkan ke Database
    if (!empty($batch_data)) {
      $this->Kuis_model->insert_quiz_question_batch($batch_data);
      $this->session->set_flashdata('import_success', count($batch_data) . ' pertanyaan berhasil di-import.');
    } else {
      $this->session->set_flashdata('import_error', 'Tidak ada data valid untuk di-import.');
    }

    // 7. Hapus file temp dan redirect
    if (file_exists($file_path)) {
      unlink($file_path);
    }
    redirect('guru/pbl_kuis/kuis_detail/' . $quiz_id);
  }

  public function get_quiz_submissions($quiz_id)
  {
    $data = $this->Kuis_model->get_results_by_quiz_id($quiz_id);
    $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  public function delete_quiz_submission()
  {
    $id = $this->input->post('id'); // ID dari tabel pbl_quiz_results
    
    if(!$this->Kuis_model->get_quiz_result_by_id($id)) {
      echo json_encode(['status'=>'error', 'message'=>'ID tidak ditemukan', 'csrf_hash' => $this->security->get_csrf_hash()]);
      return;
    }

    $this->Kuis_model->delete_quiz_result($id);
    
    echo json_encode([
      'status' => 'success', 
      'message' => 'Nilai siswa dihapus (Siswa dapat mengerjakan ulang)', 
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }

}

/* End of file Pbl_kuis.php */
/* Location: ./application/controllers/Guru/Pbl_kuis.php */