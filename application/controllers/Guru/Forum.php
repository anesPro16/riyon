<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Forum extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // cek login
        is_logged_in();
    }

    public function index()
    {
        $keyword = $this->input->get('q');
        $data['forum'] = $this->Forum_model->get_all_forum($keyword);

        // Jika permintaan dari AJAX → kirim JSON (tanpa view)
        if ($this->input->is_ajax_request()) {
            echo json_encode($data['forum']);
            return;
        }

        // Jika permintaan biasa → tampilkan view penuh
        $data['title'] = 'Forum';
        $data['user'] = $this->session->userdata();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('guru/forum/index', $data);
        $this->load->view('templates/footer');
    }


    public function get_forum($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->model('Forum_model');
        $forum = $this->Forum_model->get_by_id($id);

        if ($forum) {
            echo json_encode($forum);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan.']);
        }
    }


    public function save()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->model('Forum_model');

        $id = $this->input->post('id');
        $judul = $this->input->post('judul');
        // $materi_id = $this->input->post('materi_id');

        if (empty($judul)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Judul wajib diisi.',
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $data = [
            'judul' => $judul,
            // 'materi_id' => $materi_id,
            'dibuat_oleh' => $this->session->userdata('user_id'),
            'tanggal' => date('Y-m-d H:i:s')
        ];

        if ($id) {
            $this->Forum_model->update($id, $data);
            $msg = 'Forum berhasil diperbarui.';
        } else {
            $this->Forum_model->insert($data);
            $msg = 'Forum berhasil ditambahkan.';
        }

        echo json_encode([
            'status' => 'success',
            'message' => $msg,
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash()
        ]);
    }



    public function delete($id)
    {
        $this->Forum_model->delete($id);
        echo json_encode(['status' => 'success', 'message' => 'Forum berhasil dihapus.']);
    }

    public function thread($id)
    {
        $data['thread'] = $this->Forum_model->get_by_id($id);
        if (!$data['thread']) show_404();
        $data['comments'] = $this->Komentar_model->get_by_forum($id);
        $data['title'] = 'Forum';
        $data['user'] = $this->session->userdata();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('guru/forum/thread', $data);
        $this->load->view('templates/footer');
    }

    public function komentar()
    {
        // Pastikan user login
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            // jika ingin AJAX, kembalikan json. Di sini redirect ke login (sesuaikan)
            redirect('auth/login');
            return;
        }

        $forum_id = $this->input->post('forum_id');
        $isi_input = $this->input->post('isi_komentar');

        // 1) Hapus tag HTML/PHP dan encode agar aman dari XSS
        $isi_stripped = strip_tags($isi_input); // hapus tag HTML/PHP
        $isi_encoded = htmlspecialchars($isi_stripped, ENT_QUOTES, 'UTF-8');

        // 2) Filter kata kasar (fungsi di atas)
        $isi_final = $this->filter_kata_kasar($isi_encoded);

        // Simpan ke DB
        $this->Komentar_model->insert([
            'forum_id' => $forum_id,
            'user_id' => $user_id,
            'isi_komentar' => $isi_final,
            'tanggal' => date('Y-m-d H:i:s')
        ]);

        // Set flash / redirect kembali ke thread
        $this->session->set_flashdata('komentar_sukses', true);
        redirect('guru/forum/thread/' . $forum_id);
    }

    /**
     * Masking kata kasar dengan toleransi variasi leetspeak.
     * - Tidak memakai regex kompleks yang rentan error.
     * - Melakukan normalisasi (1 -> i, 0 -> o, dsb.) lalu mencocokkan di level karakter.
     */
    private function filter_kata_kasar($text)
    {
        // Daftar kata kasar dasar (lowercase)
        $bad_words = ['bodoh', 'anjing', 'goblok', 'bangsat', 'idiot', 'tolol', 'bego', 'kampret'];

        // Peta penggantian 1:1 untuk normalisasi (preserve length)
        $leet_map = [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
            '8' => 'b',
            '9' => 'g',
            '@' => 'a',
            '$' => 's',
            '|' => 'l',
            '!' => 'i'
        ];

        // Buat versi normalized (lowercase) dari teks, mengganti leet chars
        // Gunakan mb_ functions untuk safety multi-byte
        $normalized = mb_strtolower(strtr($text, $leet_map));

        // Split ke array karakter agar index sama antara original dan normalized
        $orig_chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $norm_chars = preg_split('//u', $normalized, -1, PREG_SPLIT_NO_EMPTY);

        $n = count($norm_chars);

        foreach ($bad_words as $word) {
            $w = mb_strtolower($word);
            $wlen = mb_strlen($w);

            if ($wlen === 0 || $wlen > $n) continue;

            // scan tanpa regex: sliding window
            for ($i = 0; $i <= $n - $wlen; $i++) {
                // ambil potongan normalized
                $slice = implode('', array_slice($norm_chars, $i, $wlen));
                if ($slice === $w) {
                    // mask di orig_chars dan juga di norm_chars supaya tidak double-mask
                    for ($k = 0; $k < $wlen; $k++) {
                        $orig_chars[$i + $k] = '*';
                        $norm_chars[$i + $k] = '*';
                    }
                }
            }
        }

        // gabungkan kembali
        return implode('', $orig_chars);
    }

    public function hapus_komentar($id)
    {
        // Cek user login
        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role_id');

        // Ambil data komentar
        $komentar = $this->Komentar_model->get_by_id($id);
        if (!$komentar) {
            echo json_encode(['status' => 'error', 'message' => 'Komentar tidak ditemukan.']);
            return;
        }

        // Hanya pembuat komentar atau guru boleh hapus
        if ($komentar->user_id != $user_id && $role != 1) {
            echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk menghapus komentar ini.']);
            return;
        }

        $this->Komentar_model->delete($id);
        echo json_encode(['status' => 'success', 'message' => 'Komentar berhasil dihapus.']);
    }
}
