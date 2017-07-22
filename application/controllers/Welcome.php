<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once('Common.php');
class Welcome extends Common {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->model('records/User_model',"userInfo");
		$this->userInfo->init_with_id('551b8d46511dee7e100041a8');
		// $this->userInfo->init_with_where(['name'=>'郭佳']);
		var_dump($this->userInfo->is_inited);
		var_dump($this->userInfo->field_list);
		// $this->output->cache(10);
		// var_dump($this->db);
		$this->load->view('welcome_message');
	}
}
