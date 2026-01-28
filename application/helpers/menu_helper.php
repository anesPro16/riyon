<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('get_menu_sidebar')) {
    function get_menu_sidebar($force_refresh = false)
    {
        $CI = &get_instance();
        $CI->load->model('Menu_model', 'menu');

        $role_id = $CI->session->userdata('role_id');

        if ($force_refresh) {
            $CI->menu->clearMenuCache($role_id);
        }

        return $CI->menu->getMenuByRole($role_id);
    }
}
