<?php

class Login_model extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('cookie');


	}
	
	function is_user()
	{
	//$this->input->is_ajax_request() insert in controller

	if ($this->session->userdata('user') == false)
		{
			return false;
		}
	else
		{
			return true;

		}

	}
	function save_user()
	{

	}

	function check_user($username,$password)
	{

  	  $username = mysql_real_escape_string($username);
	  $password = mysql_real_escape_string($password);
      $query = $this->db->get_where('users', array('user' =>  $username, 'password' => md5($password) ));
	  $array_results= $query->result_array();
	  if (count($array_results) == 1)
	  {
		  $this->session->set_userdata('user', true);

		  $data = array('incorrec_login' => 0, 'date' => time(), 'locked' => 0);
		  $this->db->update('config', $data);
		  return true;
	  }
	  else
	  {

		  $this->db->select('incorrec_login');
		  $query = $this->db->get('config');
		  $array_results = $query->result_array();
		  $logins =  $array_results[0]['incorrec_login'];
		  

		  $logins = $logins + 1;
		  if ($logins > 5)
		  {
			  $logins = 5;
			  $data = array(
               'incorrec_login' => $logins,
			   'date' => time() + 3600,
			   'locked' => 1
            );
		  }
		  else

			  $data = array(
               'incorrec_login' => $logins,
            );


		  
		  $this->db->update('config', $data);

		  $this->session->set_userdata('user', false);
		  return false;
	  }


	  }


	  function unblocked_login()
	  {
		$this->db->select('incorrec_login, date, locked');
		$query = $this->db->get('config');
		$array_results = $query->result_array();
		$logins =  $array_results[0]['incorrec_login'];
		$date =  $array_results[0]['date'];
		$locked =  $array_results[0]['locked'];

		if (time() > $date && $locked == 1)
		{
			$data = array(
            'incorrec_login' => 0,
			'date' => 0,
			'locked' => 0
            );
			$this->db->update('config', $data);
			return true;
		}

		if (time() > $date)
		{
			return true;
		}


			return $date;
		}
	  

	

}
?>
