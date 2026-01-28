<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		is_logged_in();
		$this->load->model('Menu_model', 'menu');
	}

	public function index()
	{
		$data['title'] = 'Kelola Menu';
		$data['user'] = $this->db->get_where('users', ['username' => $this->session->userdata('username')])->row_array();

		$data['menu'] = $this->db->get('user_menu')->result_array();
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('menu/index', $data);
		$this->load->view('templates/footer');

	}

	public function getMenuList()
	{
		$menu = $this->menu->get_all();
	    // kembalikan JSON agar bisa dipakai di JS fetch
		echo json_encode($menu);
	}


	public function saveMenu() {
		$id = $this->input->post('id');
		$menu = trim($this->input->post('menu'));
		if (empty($menu)) {
			echo json_encode(['status' => 'error', 'message' => 'Menu tidak boleh kosong', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		// Cek duplikat
		$exists = $this->menu->check_duplicate($menu, $id);
		if ($exists) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Nama menu sudah ada, gunakan nama lain!',
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		// Simpan data
		if ($id) {
      	// Update
			$this->menu->update($id, ['menu' => $menu]);
			$msg = 'Menu berhasil diperbarui!';
		} else {
      	// Insert
			$this->menu->insert(['menu' => $menu]);
			$msg = 'Menu baru berhasil ditambahkan!';
		}

		echo json_encode([
			'status' => 'success',
			'message' => $msg,
			'csrf_hash' => $this->security->get_csrf_hash()
		]);

	}

	// === Hapus data ===
	public function deleteMenu($id)
	{
		if (!$id) {
			echo json_encode([
				'status' => 'error',
				'message' => 'ID tidak ditemukan.',
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$deleted = $this->menu->delete($id);

		if ($deleted) {
			echo json_encode([
				'status' => 'success',
				'message' => 'Menu berhasil dihapus.',
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Gagal menghapus menu.',
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
		}
	}


	public function submenu()
	{
		$data['title'] = 'Kelola Submenu';
		$data['user'] = $this->db->get_where('users', ['username' => $this->session->userdata('username')])->row_array();

		$data['subMenu'] = $this->menu->getAllSubmenu();;
		$data['menu'] = $this->db->get('user_menu')->result_array();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('menu/submenu', $data);
		$this->load->view('templates/footer');

	}

	public function getSubmenuList()
    {
        // Panggil model untuk mengambil semua data submenu
        $submenus = $this->menu->getAllSubmenu();
        
        // Kirim sebagai JSON
        header('Content-Type: application/json');
        echo json_encode($submenus);
    }

    // ===================================================================
    // FUNGSI YANG DIMODIFIKASI (TIDAK LAGI MENGIRIM HTML)
    // ===================================================================
	public function saveSubmenu()
	{
		$this->load->model('Menu_model', 'menu');

		$id = $this->input->post('id');
		$title = trim($this->input->post('title'));
		$menu_id = $this->input->post('menu_id');
		$url = trim($this->input->post('url'));
		$icon = trim($this->input->post('icon'));
		$is_active = $this->input->post('is_active') ? 1 : 0;

        // Validasi sederhana
		if ($title == '' || $menu_id == '' || $url == '' || $icon == '') {
			echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

        // Cegah duplikat title untuk menu yang sama
		$this->db->where('title', $title);
		$this->db->where('menu_id', $menu_id);
		if ($id) $this->db->where('id !=', $id);
		$cek = $this->db->get('user_sub_menu')->row();
		if ($cek) {
			echo json_encode(['status' => 'error', 'message' => 'Submenu dengan judul ini sudah ada pada menu tersebut.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		$data = [
			'title' => $title,
			'menu_id' => $menu_id,
			'url' => $url,
			'icon' => $icon,
			'is_active' => $is_active
		];

		$success = $this->menu->saveSubmenu($data, $id);

		if ($success) {
            // MODIFIKASI: Kirim status sukses saja, BUKAN HTML
			echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan!', 'csrf_hash' => $this->security->get_csrf_hash()]);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Tidak ada perubahan data.', 'csrf_hash' => $this->security->get_csrf_hash()]);
		}
		
        // Hapus cache (ini tetap)
        $role_id = $this->session->userdata('role_id');
		$this->menu->clearMenuCache($role_id);
	}


	public function deleteSubmenu($id)
	{
		$this->load->model('Menu_model', 'menu');
		$deleted = $this->menu->deleteSubmenu($id);

		if ($deleted) {
            // MODIFIKASI: Kirim status sukses saja, BUKAN HTML
			echo json_encode(['status' => 'success', 'message' => 'Submenu berhasil dihapus.', 'csrf_hash' => $this->security->get_csrf_hash()]);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus submenu.', 'csrf_hash' => $this->security->get_csrf_hash()]);
		}
	}
}
