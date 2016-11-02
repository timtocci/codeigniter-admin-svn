<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

		public function __construct() {
        parent::__construct();
        $this->load->helper('form');
		$this->load->helper('url');
        $this->load->database();
	
		$this->load->model('Login_model');
		set_time_limit(0);
		ini_set('memory_limit', '120M');
		$this->load->library('session');

    }

	function index()
	{
	die('index/login');
	}

	function dologin()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		 $status = $this->Login_model->unblocked_login();
		 if ($status === true)
		 {

				if ($this->Login_model->check_user($username,$password))
				{
				 redirect($this->session->userdata('rdirect'));
				}
				else
				{
				$data = array();
				$this->load->view('login',$data);

				}


		}
		else
		 {
				$time = date('F j, Y, g:i a',$status);
				$data = array('time' => $time);
				$this->load->view('login_denided',$data);
		 }

	}

}
