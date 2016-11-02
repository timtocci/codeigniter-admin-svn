<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Edit extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
		$this->load->helper('url');
        $this->load->database();
		$this->load->model('Pano_model');
		$this->load->model('Login_model');
    }

	public function index()
	{
	if($this->input->is_ajax_request() === false)
		{
			if ($this->Login_model->is_user() == false)
			{
				$this->load->library('session');
				$data = array();
				$this->session->set_userdata('rdirect', $this->uri->uri_string());
				$this->load->view('login',$data);
				return;
			}
		}


		$data = array();
		$this->load->view('edit',$data);
	}

	public function data_get()
	{
	header('Content-Type: application/json');

	$filter = array();
	$filter = $this->input->post('filter');

	$start = $this->input->post('start');
	$limit = $this->input->post('limit');
	$this->db->select('id, file, name, adress, heading');


	if	($filter)
	{
	foreach ($filter as $value)
		{
		$this->db->like($value['field'], $value['data']['value'], 'both');
		}
	}
	$this->db->order_by("id", "asc");
	$this->db->limit( $limit, $start);
	$query = $this->db->get('panorams');
	$array_results= $query->result_array();

	$results = $this->db->count_all_results('panorams');
	
	Echo '{success:true,rows:'.json_encode($array_results).', results :'.$results.', limit:'.$limit.'}';
	exit;
	}

	public function data_update()
	{
	header('Content-Type: application/json');


	$id = $this->input->post('id');
	$field = $this->input->post('field');
	$value = $this->input->post('value');

	$this->db->update('panorams', array($field => $value), "id = $id");

	Echo '{success:true}';
	exit;
	}


	public function  pano_del()
	{
	$id = $id = $this->input->post('id');

	$this->db->select('file');
	$this->db->where('id', $id);
	$query = $this->db->get('panorams');

	if ($query->num_rows() > 0)
	{
	$row = $query->row_array();

	unlink("uploads/".$row['file']) ;

	//unlink("uploads/dir_".$row['file']) or die ("Can not remove dir");

	$dir = "uploads/dir_".$row['file'];

    if ($objs = glob($dir."/*")) {
        foreach($objs as $obj) {
            is_dir($obj) ? removeDirRec($obj) : unlink($obj);
        }
    }
    rmdir($dir);


	$this->db->where('id', $id);
	$this->db->delete('panorams');

	$this->db->where('panorama_id', $id);
	$this->db->or_where('panorama_id_to_link', $id);
	$this->db->delete('links');

	}

	header('Content-Type: application/json');
	Echo '{success:true}';
	exit;

	}



	public function data_get_links()
	{
	header('Content-Type: application/json');


	$id = $this->input->post('id');

	$this->db->select('id, title, heading');

	$this->db->where('panorama_id', $id);

	$this->db->order_by("id", "asc");
	$query = $this->db->get('links');
	$array_results= $query->result_array();

	$results = $this->db->count_all_results('links');

	Echo '{success:true,rows:'.json_encode($array_results).', results :'.$results.'}';
	exit;
	}

	public function data_link_update()
	{
	header('Content-Type: application/json');


	$id = $this->input->post('id');
	$field = $this->input->post('field');
	$value = $this->input->post('value');

	$this->db->update('links', array($field => $value), "id = $id");

	Echo '{success:true}';
	exit;
	}

	public function  link_del()
	{
	$id = $id = $this->input->post('id');
	$this->db->where('id', $id);
	$query = $this->db->get('links');

	if ($query->num_rows() > 0)
	{
	$this->db->where('id', $id);
	$this->db->delete('links');
	}

	header('Content-Type: application/json');
	Echo '{success:true}';
	exit;

	}



}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */