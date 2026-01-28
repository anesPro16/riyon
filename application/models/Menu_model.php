<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{
	private $table = 'user_menu';
	
	// Ambil semua data menu
  public function get_all()
  {
    return $this->db->order_by('id', 'DESC')->get($this->table)->result_array();
  }

  // Cek duplikat nama menu (kecuali id yang sedang diedit)
  public function check_duplicate($menu, $exclude_id = null)
  {
    $this->db->where('menu', $menu);
    if ($exclude_id) {
      $this->db->where('id !=', $exclude_id);
    }
    return $this->db->get($this->table)->num_rows() > 0;
  }

  // Insert data baru
  public function insert($data)
  {
    return $this->db->insert($this->table, $data);
  }

  // Update data
  public function update($id, $data)
  {
    return $this->db->where('id', $id)->update($this->table, $data);
  }

  // Hapus data
  public function delete($id)
  {
    return $this->db->delete($this->table, ['id' => $id]);
  }

	public function getSubMenu()
	{
		$query = "SELECT `user_sub_menu`.*, `user_menu`.`menu`
		FROM `user_sub_menu` JOIN `user_menu`
		ON `user_sub_menu`.`menu_id` = `user_menu`.`id`
		";
		return $this->db->query($query)->result_array();
	}

	public function getSubmenuById($id)
	{
		$this->db->select('user_sub_menu.*, user_menu.menu as menu_name');
		$this->db->from('user_sub_menu');
		$this->db->join('user_menu', 'user_sub_menu.menu_id = user_menu.id');
		$this->db->where('user_sub_menu.id', $id);
		return $this->db->get()->row_array();
	}


	public function getAllSubmenu()
  {
    $this->db->select('user_sub_menu.*, user_menu.menu as menu_name');
    $this->db->from('user_sub_menu');
    $this->db->join('user_menu', 'user_sub_menu.menu_id = user_menu.id');
    $this->db->order_by('user_sub_menu.id', 'DESC');
    return $this->db->get()->result_array();
  }

	public function saveSubmenu($data, $id = null)
  {
    if ($id) {
      $this->db->where('id', $id);
      $this->db->update('user_sub_menu', $data);
    } else {
      $this->db->insert('user_sub_menu', $data);
    }
    return $this->db->affected_rows() > 0;
  }

  public function deleteSubmenu($id)
  {
    $this->db->delete('user_sub_menu', ['id' => $id]);
    return $this->db->affected_rows() > 0;
  }

  public function findSubmenu($id)
  {
    return $this->db->get_where('user_sub_menu', ['id' => $id])->row_array();
  }

	/**
     * Ambil menu dan submenu berdasarkan role_id
     * + caching via session agar tidak query DB berulang
     */
	public function getMenuByRole($role_id)
	{
        // Cek apakah data sudah ada di session
		$cache_key = 'menu_sidebar_' . $role_id;
		$cached = $this->session->userdata($cache_key);
		if ($cached) {
            return $cached; // langsung return dari session
          }

        // Jika belum ada, ambil dari database
          $this->db->select('user_menu.id, user_menu.menu');
          $this->db->from('user_menu');
          $this->db->join('user_access_menu', 'user_menu.id = user_access_menu.menu_id');
          $this->db->where('user_access_menu.role_id', $role_id);
          $this->db->order_by('user_access_menu.menu_id', 'ASC');
          $menus = $this->db->get()->result_array();

          $result = [];

          foreach ($menus as $menu) {
          	$this->db->select('*');
          	$this->db->from('user_sub_menu');
          	$this->db->join('user_menu', 'user_sub_menu.menu_id = user_menu.id');
          	$this->db->where('user_sub_menu.menu_id', $menu['id']);
          	$this->db->where('user_sub_menu.is_active', 1);
          	$subMenu = $this->db->get()->result_array();

          	$result[] = [
          		'menu' => $menu['menu'],
          		'submenu' => $subMenu
          	];
          }

        // Simpan hasil ke session untuk cache (durasi 1 sesi login)
          $this->session->set_userdata($cache_key, $result);

          return $result;
        }

    /**
     * Hapus cache sidebar untuk role tertentu
     * (berguna setelah admin ubah menu/submenu)
     */
    public function clearMenuCache($role_id)
    {
    	$cache_key = 'menu_sidebar_' . $role_id;
    	$this->session->unset_userdata($cache_key);
    }
  }
