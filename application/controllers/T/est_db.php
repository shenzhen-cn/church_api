<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_db extends CI_Controller {

	public function index()
	{
		$this->load->database();
		$rs  =  $this->db->get('user')->result();

		var_dump($rs);

		// $this->load->view('welcome_message');
	}
}
