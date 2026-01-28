<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tugas extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Jika ada model untuk TTS/kuis, load di sini
        // $this->load->model('Tugas_model');
        $this->load->model('TtsModel');
    }

    public function index()
    {
        // $data['title'] = 'Tugas Interaktif (Tahap 2 - Organisasi Belajar)';
        $data['title'] = 'Tugas';
        $data['user'] = $this->session->userdata();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('guru/tugas/index', $data);
        $this->load->view('templates/footer');
    }

    public function get_all()
    {
        $data = $this->TtsModel->get_all();
        echo json_encode($data);
    }

    // === Tambah TTS via AJAX ===
    public function store()
    {
        $judul = $this->input->post('judul');
        $deskripsi = $this->input->post('deskripsi');
        $grid_size = $this->input->post('grid_size');

        $insert = $this->TtsModel->insert([
            'judul' => $judul,
            'deskripsi' => $deskripsi,
            'grid_size' => $grid_size
        ]);

        echo json_encode(['status' => $insert ? 'success' : 'error']);
    }

    // === Hapus TTS via AJAX ===
    public function delete($id)
    {
        $delete = $this->TtsModel->delete($id);
        echo json_encode(['status' => $delete ? 'success' : 'error']);
    }

    // ====== FUNGSI UNTUK TTS ======
    public function tts()
    {
        $data['title'] = 'Teka Teki Silang – Konsep Dasar Masalah';
        // Nanti bisa diisi data pertanyaan/koordinat TTS dari DB
        $this->load->view('guru/tts', $data);
    }

    public function tts_interaktif()
    {
        // $data['title'] = 'TTS Interaktif – Konsep Dasar Masalah';
        // Contoh data dummy (bisa dari database)
        $data['grid_size'] = 10;
        $data['questions'] = [
            ['nomor' => 1, 'arah' => 'mendatar', 'x' => 0, 'y' => 0, 'jawaban' => 'KONSEP', 'pertanyaan' => 'Dasar dari sebuah ide disebut...'],
            ['nomor' => 2, 'arah' => 'menurun', 'x' => 2, 'y' => 0, 'jawaban' => 'MASALAH', 'pertanyaan' => 'Hal yang perlu dicari solusinya disebut...'],
        ];
        $data['title'] = 'Tugas';
        $data['user'] = $this->session->userdata();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('guru/tugas/tts_interaktif', $data);
        $this->load->view('templates/footer');
    }

    public function detail($id)
    {
        $data['tts'] = $this->TtsModel->get($id);
        if (!$data['tts']) show_404();

        // $data['title'] = 'Detail TTS: ' . $data['tts']->judul;

        $data['title'] = 'Tugas';
        $data['user'] = $this->session->userdata();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('guru/tugas/tts_detail', $data);
        $this->load->view('templates/footer');
    }

    // === Ambil semua pertanyaan untuk 1 TTS ===
    public function get_questions($tts_id)
    {
        $data = $this->TtsModel->get_questions($tts_id);
        echo json_encode($data);
    }

    // === Simpan pertanyaan baru via AJAX ===
    public function store_question()
{
    $this->load->model('TtsModel');

    $tts_id     = $this->input->post('tts_id');
    $nomor      = (int)$this->input->post('nomor');
    $arah       = $this->input->post('arah');
    $pertanyaan = trim($this->input->post('pertanyaan'));
    $jawaban    = strtoupper(trim($this->input->post('jawaban')));
    $start_x    = (int)$this->input->post('start_x');
    $start_y    = (int)$this->input->post('start_y');

    // ===== Validasi dasar =====
    if (!$tts_id || !$arah || !$pertanyaan || !$jawaban) {
        echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']);
        return;
    }

    // Ambil data TTS untuk tahu grid_size
    $tts = $this->db->get_where('tts', ['id' => $tts_id])->row();
    if (!$tts) {
        echo json_encode(['status' => 'error', 'message' => 'Data TTS tidak ditemukan.']);
        return;
    }

    $grid_size = (int)$tts->grid_size;
    $panjang_jawaban = strlen($jawaban);

    // ===== Validasi panjang jawaban =====
    if ($panjang_jawaban > $grid_size) {
        echo json_encode(['status' => 'error', 'message' => 'Panjang jawaban melebihi ukuran grid.']);
        return;
    }

    // ===== Validasi posisi start dalam grid =====
    if ($start_x < 0 || $start_y < 0 || $start_x >= $grid_size || $start_y >= $grid_size) {
        echo json_encode(['status' => 'error', 'message' => 'Posisi awal berada di luar batas grid.']);
        return;
    }

    // ===== Validasi agar kata tidak keluar batas grid =====
    if ($arah === 'mendatar' && ($start_x + $panjang_jawaban) > $grid_size) {
        echo json_encode(['status' => 'error', 'message' => 'Jawaban mendatar melewati batas grid.']);
        return;
    }
    if ($arah === 'menurun' && ($start_y + $panjang_jawaban) > $grid_size) {
        echo json_encode(['status' => 'error', 'message' => 'Jawaban menurun melewati batas grid.']);
        return;
    }

    // ===== Validasi duplikasi nomor pada TTS yang sama =====
    $cekNomor = $this->db->get_where('tts_questions', [
        'tts_id' => $tts_id,
        'nomor'  => $nomor
    ])->row();

    if ($cekNomor) {
        echo json_encode(['status' => 'error', 'message' => 'Nomor soal sudah digunakan pada TTS ini.']);
        return;
    }

    // ===== Simpan ke database =====
    $data = [
        'tts_id'     => $tts_id,
        'nomor'      => $nomor,
        'arah'       => $arah,
        'pertanyaan' => $pertanyaan,
        'jawaban'    => $jawaban,
        'start_x'    => $start_x,
        'start_y'    => $start_y,
    ];

    $insert = $this->TtsModel->insert_question($data);

    if ($insert) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pertanyaan.']);
    }
}


    // === Hapus pertanyaan ===
    public function delete_question($id)
    {
        $delete = $this->TtsModel->delete_question($id);
        echo json_encode(['status' => $delete ? 'success' : 'error']);
    }

    /*public function preview($id)
    {
        $tts = $this->TtsModel->find($id);
        if (!$tts) show_404();

        $questions = $this->TtsModel->get_questions($id);

        $data = [
            'title' => 'Preview TTS: ' . $tts->judul,
            'tts' => $tts,
            'mode' => 'siswa',
            'questions' => $questions
        ];
        $this->load->view('layouts/header', $data);
        $this->load->view('guru/tugas/tts_preview', $data);
        $this->load->view('layouts/footer');
    }*/
    public function preview($tts_id)
{
    $this->load->model('TtsModel');
    $data['tts'] = $this->TtsModel->find($tts_id);
    if (!$data['tts']) show_404();

    $data['questions'] = $this->TtsModel->get_questions($tts_id);
    $data['title'] = 'Preview TTS: ' . $data['tts']->judul;
    $data['mode'] = 'siswa';

    // $data['title'] = 'Tugas';
    $data['user'] = $this->session->userdata();
    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
    $this->load->view('guru/tugas/tts_preview', $data);
    $this->load->view('templates/footer');
}



    // ====== FUNGSI UNTUK KUIS ESAI ======
    public function kuis()
    {
        $data['title'] = 'Kuis Esai – Pemahaman Dasar Masalah';
        // Nanti bisa ambil data kuis dari DB
        $this->load->view('guru/kuis', $data);
    }
}