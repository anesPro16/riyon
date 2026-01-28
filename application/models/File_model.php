<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File_model extends CI_Model {

  public function get_observasi_file($filename)
  {
    return $this->db
      ->select('u.id, u.user_id, u.observation_id, s.class_id')
      ->from('observation_uploads u')
      ->join('observation s', 's.observation_id = u.observation_id')
      ->where('u.file_name', $filename)
      ->get()
      ->row();
  }
}


/* End of file File_model.php */
/* Location: ./application/models/File_model.php */