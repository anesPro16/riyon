<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Dompdf\Dompdf;
use Dompdf\Options;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Laporan extends CI_Controller {

	public function __construct()
  {
    parent::__construct();
    is_logged_in();
  }

	/* =========================
   * GURU – DAFTAR Kelas
   * ========================= */
  public function index()
  {
    $user = $this->session->userdata();
    $role_id = $user['role_id'];
    $user_id = $user['user_id'];

    // 1. Validasi apakah user adalah Guru
    if (!$this->User_model->check_is_teacher($role_id)) {
        show_error('Akses ditolak. Halaman ini khusus Guru.', 403);
    }

    // 2. Ambil Data Kelas milik Guru tersebut
    // Kita asumsikan Guru_model menghandle logika pengambilan kelas berdasarkan user_id guru
    $data = [
        'title'      => 'Pilih Kelas',
        'user'       => $user,
        'kelas_list' => $this->Guru_model->get_all_classes($user_id), 
        'url_name'   => 'guru'
    ];

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('guru/laporan', $data);
    $this->load->view('templates/footer');
  }

  public function detail_kelas($class_id = null)
  {
    if (!$class_id) {
      redirect('guru/dashboard');
    }

    $data['title'] = 'Laporan Hasil Belajar';
    $data['class_id'] = $class_id;
    $data['user'] = $this->session->userdata();
    $data['url_name'] = 'guru';
    $role_id = $this->session->userdata('role_id');    
    $data['is_admin_or_guru'] = $this->User_model->check_is_teacher($role_id);

    $data['exam_subjects'] = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'PPKN'];

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('guru/detail_laporan', $data);
    $this->load->view('templates/footer');
  }

  public function get_student_recap($class_id)
  {
    $students = $this->Laporan_model->getAllStudentScores($class_id);
    
    // Return JSON langsung untuk ditangkap fetch JS
    echo json_encode($students);
  }

  public function save_reflection()
  {
    $this->form_validation->set_rules('user_id', 'ID Siswa', 'required');
    $this->form_validation->set_rules('class_id', 'ID Kelas', 'required');

    if ($this->form_validation->run() === FALSE) {
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
          'status' => 'error', 
          'message' => validation_errors(),
          'csrf_hash' => $this->security->get_csrf_hash() // Update CSRF
        ]));
      return;
    }


  // 3. Siapkan Data
  $data = [
    'class_id' => $this->input->post('class_id'),
    'user_id' => $this->input->post('user_id'),
    'teacher_reflection' => $this->input->post('teacher_reflection'),
    'student_feedback' => $this->input->post('student_feedback'),
  ];

  // 4. Simpan ke Database
  // Model akan menangani logika Insert (jika baru) atau Update (jika sudah ada)
  $saved = $this->Laporan_model->save_reflection($data);

  // 5. Return JSON sukses + CSRF Hash baru
  if ($saved) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Refleksi dan Feedback berhasil disimpan.',
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  } else {
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menyimpan data ke database.',
        'csrf_hash' => $this->security->get_csrf_hash()
      ]);
    }
  }

  /**
   * Fitur Export Data Laporan ke Excel
   */
  public function export_excel($class_id)
  {
    // 1. Ambil Data Mentah dari Model
    $students = $this->Laporan_model->getAllStudentScores($class_id);
    
    // 2. Daftar Mapel (Hardcode sesuai controller Anda, idealnya dari DB)
    $exam_subjects = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'PPKN'];

    // 3. Inisialisasi Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // 4. Set Header Kolom
    // Baris 1: Judul Mapel (Merge Cells)
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Nama Siswa');
    $sheet->mergeCells('A1:A2');
    $sheet->mergeCells('B1:B2');

    $col = 2; // Mulai index kolom C (0=A, 1=B, 2=C)
    
    foreach ($exam_subjects as $subject) {
      // Konversi index angka ke huruf kolom (C, H, M, dst)
      $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
      $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 5);
      
      $sheet->setCellValue($startCol . '1', $subject);
      $sheet->mergeCells("$startCol"."1:$endCol"."1");

      // Baris 2: Sub-kolom (UTS, UAS, Kuis, Obs, Esai)
      $sheet->setCellValueByColumnAndRow($col + 1, 2, 'UTS');
      $sheet->setCellValueByColumnAndRow($col + 2, 2, 'UAS');
      $sheet->setCellValueByColumnAndRow($col + 3, 2, 'Kuis');
      $sheet->setCellValueByColumnAndRow($col + 4, 2, 'Obs');
      $sheet->setCellValueByColumnAndRow($col + 5, 2, 'Esai');

      $col += 5; // Geser 5 kolom untuk mapel berikutnya
    }

    // Kolom Total (Akhir)
    $lastColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
    $sheet->setCellValue($lastColStr . '1', 'Total');
    $sheet->mergeCells("$lastColStr"."1:$lastColStr"."2");

    // 5. Styling Header
    $headerStyle = [
      'font' => ['bold' => true],
      'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
      'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
      'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E0E0']]
    ];
    $sheet->getStyle("A1:$lastColStr"."2")->applyFromArray($headerStyle);

    // 6. Isi Data Siswa
    $row = 3;
    $no = 1;

    foreach ($students as $s) {
      // Parsing Data String (Logic sama seperti di JS, tapi versi PHP)
      $examData  = $this->_parseExamScores($s->exam_data);
      $quizData  = $this->_calculateAvg($s->quiz_data);
      $obsData   = $this->_calculateAvg($s->obs_data);
      $essayData = $this->_calculateAvg($s->essay_data);

      $sheet->setCellValue('A' . $row, $no++);
      $sheet->setCellValue('B' . $row, $s->student_name);

      $col = 2; // Reset kolom ke C
      $allScores = [];

      foreach ($exam_subjects as $subj) {
        // Ambil Nilai
        $valUTS   = $examData[$subj]['UTS'] ?? 0;
        $valUAS   = $examData[$subj]['UAS'] ?? 0;
        $valQuiz  = $quizData[$subj] ?? 0;
        $valObs   = $obsData[$subj] ?? 0;
        $valEssay = $essayData[$subj] ?? 0;

        // Tulis ke Cell (Kosongkan jika 0 agar bersih)
        $sheet->setCellValueByColumnAndRow($col + 1, $row, $valUTS ?: '-');
        $sheet->setCellValueByColumnAndRow($col + 2, $row, $valUAS ?: '-');
        $sheet->setCellValueByColumnAndRow($col + 3, $row, $valQuiz ?: '-');
        $sheet->setCellValueByColumnAndRow($col + 4, $row, $valObs ?: '-');
        $sheet->setCellValueByColumnAndRow($col + 5, $row, $valEssay ?: '-');

        // Kumpulkan untuk Grand Total (Hanya yang ada nilainya)
        if ($valUTS) $allScores[] = $valUTS;
        if ($valUAS) $allScores[] = $valUAS;
        if ($valQuiz) $allScores[] = $valQuiz;
        if ($valObs) $allScores[] = $valObs;
        if ($valEssay) $allScores[] = $valEssay;

        $col += 5;
      }

      // Hitung Rata-rata Total
      $grandTotal = 0;
      if (count($allScores) > 0) {
          $grandTotal = round(array_sum($allScores) / count($allScores));
      }
      $sheet->setCellValueByColumnAndRow($col + 1, $row, $grandTotal);

      $row++;
    }

    // Auto Size Columns
    foreach (range('A', $lastColStr) as $colID) {
      $sheet->getColumnDimension($colID)->setAutoSize(true);
    }

    // 7. Output File Download
    $filename = 'Laporan_Hasil_Belajar_' . date('Ymd_His') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
  }

  // --- HELPER FUNCTIONS (Versi PHP dari JS Anda) ---

  private function _parseExamScores($str) {
    $result = [];
    if (!$str) return $result;
    
    $entries = explode('||', $str);
    foreach ($entries as $entry) {
        $parts = explode('::', $entry);
        if (count($parts) == 3) {
            $result[$parts[0]][$parts[1]] = $parts[2];
        }
    }
    return $result;
  }

  private function _calculateAvg($str) {
    $sums = [];
    $counts = [];
    
    if (!$str) return [];

    $entries = explode('||', $str);
    foreach ($entries as $entry) {
      $parts = explode('::', $entry);
      if (count($parts) == 2) {
        $subj = $parts[0];
        $score = floatval($parts[1]);

        if (!isset($sums[$subj])) { $sums[$subj] = 0; $counts[$subj] = 0; }
        $sums[$subj] += $score;
        $counts[$subj]++;
      }
    }

    $avgs = [];
    foreach ($sums as $subj => $total) {
      $avgs[$subj] = round($total / $counts[$subj]);
    }
    return $avgs;
  }

  public function cetak_pdf($class_id = null)
    {
        if (!$class_id) show_404();

        // 1. Ambil Data Mentah
        $students = $this->Laporan_model->getAllStudentScores($class_id);
        
        // 2. Konfigurasi Mapel
        $exam_subjects = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'PPKN'];

        // 3. Olah Data (Parsing String ke Array)
        // Kita butuh data matang untuk dikirim ke View, karena DOMPDF tidak baca JS
        $processed_data = [];
        foreach ($students as $s) {
            $row = (array) $s; // Cast object ke array
            
            // Parsing menggunakan helper function private
            $row['parsed_exam']  = $this->_parseExamScores($s->exam_data);
            $row['parsed_quiz']  = $this->_calculateAvg($s->quiz_data);
            $row['parsed_obs']   = $this->_calculateAvg($s->obs_data);
            $row['parsed_essay'] = $this->_calculateAvg($s->essay_data);
            
            // Hitung Grand Total Rata-rata
            $allScores = [];
            foreach ($exam_subjects as $subj) {
                if (isset($row['parsed_exam'][$subj]['UTS'])) $allScores[] = $row['parsed_exam'][$subj]['UTS'];
                if (isset($row['parsed_exam'][$subj]['UAS'])) $allScores[] = $row['parsed_exam'][$subj]['UAS'];
                if (isset($row['parsed_quiz'][$subj])) $allScores[] = $row['parsed_quiz'][$subj];
                if (isset($row['parsed_obs'][$subj]))  $allScores[] = $row['parsed_obs'][$subj];
                if (isset($row['parsed_essay'][$subj])) $allScores[] = $row['parsed_essay'][$subj];
            }
            
            $row['grand_total'] = count($allScores) > 0 ? round(array_sum($allScores) / count($allScores)) : 0;
            $processed_data[] = $row;
        }

        $data = [
            'class_id' => $class_id,
            'exam_subjects' => $exam_subjects,
            'students' => $processed_data,
            'title' => 'Laporan Hasil Belajar Siswa'
        ];

        // 4. Load View khusus PDF ke dalam variabel string
        $html = $this->load->view('guru/cetak_laporan', $data, true);

        // 5. Inisialisasi DOMPDF
        $options = new Options();
        $options->set('isRemoteEnabled', true); // Agar bisa load gambar/bootstrap dr CDN jika perlu
        $options->set('defaultFont', 'Helvetica');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // Set ukuran kertas (A4 Landscape karena kolomnya banyak)
        // $dompdf->setPaper('A4', 'landscape');
        $dompdf->setPaper('legal', 'landscape');

        // Render PDF
        $dompdf->render();

        // Output ke browser (Stream)
        $dompdf->stream("Laporan_Hasil_Belajar_" . date('d-m-Y') . ".pdf", array("Attachment" => 0));
    }

}

/* End of file Laporan.php */
/* Location: ./application/controllers/Guru/Laporan.php */