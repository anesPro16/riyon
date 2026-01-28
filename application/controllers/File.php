<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File extends CI_Controller {

  public function pbl($id)
  {
    $this->load->model('Pbl_model');

    $file = $this->Pbl_model->get_file_by_id($id);
    if (!$file || !$file->file_path) {
      show_404();
    }

    $fullPath = FCPATH . $file->file_path;

    if (!file_exists($fullPath)) {
      show_404();
    }

    $mime = mime_content_type($fullPath);
    header('Content-Type: '.$mime);
    header('Content-Disposition: inline; filename="'.basename($fullPath).'"');
    header('Content-Length: '.filesize($fullPath));

    readfile($fullPath);
    exit;
  }

  public function observasi($filename)
  {
    // ===== 1. WAJIB LOGIN =====
    if (!$this->session->userdata('user_id')) {
        show_404();
    }

    $this->load->model('File_model');
    $file = $this->File_model->get_observasi_file($filename);

    if (!$file) {
        show_404();
    }

    $userId = $this->session->userdata('user_id');
    $roleId = $this->session->userdata('role_id');

    // ===== 2. VALIDASI AKSES =====
    /*$isAllowed = false;

    // Guru / Admin
    if (in_array($roleId, [1, 2])) { // sesuaikan role
        $isAllowed = true;
    }

    // Siswa: hanya file miliknya
    if ($roleId == 3 && $file->user_id == $userId) {
        $isAllowed = true;
    }

    if (!$isAllowed) {
        show_404();
    }*/

    // ===== 3. SERVE FILE =====
    $filePath = FCPATH . 'uploads/observasi/' . $filename;

    if (!file_exists($filePath)) {
        show_404();
    }

    $mime = mime_content_type($filePath);

    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: no-store');

    readfile($filePath);
    exit;
  }

}


/* End of file File.php */
/* Location: ./application/controllers/File.php */