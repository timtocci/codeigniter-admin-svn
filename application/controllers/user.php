<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
	$this->load->helper('url');
        $this->load->database();
	$this->load->model('Pano_model');
    }

	public function index()
	{
	//Chrome
		$data = array();

		if (!isset($_REQUEST['panorama_sel']))
		{
			$this->db->limit(1);
			$this->db->select('file,id,name');
			$query = $this->db->get('panorams');
			$array_results= $query->row_array();
			$data['panorama_id'] =$array_results['id'];


			$this->db->select('name');
			$query = $this->db->get_where('panorams', array('id' => $data['panorama_id']));
			$array_results= $query->row_array();
			$data['name']=$array_results['name'];


		}
		else
		{
			$data['panorama_id']= $this->input->get_post('panorama_sel');
			$this->db->select('name');
			$query = $this->db->get_where('panorams', array('id' => $data['panorama_id']));
			$array_results= $query->row_array();
			$data['name']=$array_results['name'];
		}

			$this->db->select('instructions');
			$query = $this->db->get('config');
			$result = $query->row_array();
			$data['instructions'] = $result['instructions'];

			$str = $_SERVER['HTTP_USER_AGENT'];
			$patern = '/.*?Chrome.*?/i';
			$rez = preg_match($patern,$str);
			if (!$rez)
			{
			$data['pano_style'] = "width: 100%; height: 100%;" ;
			}
			else
			{
			$data['pano_style'] = "width: 60%; height: 60%; position:absolute; top:20%; left:20%" ;
			}
			
			//$data['pano_style'] = "width: 100%; height: 100%;" ;
			$data['pano_style'] = "width: 100%; height: 100%; position:absolute;" ;
			$this->load->view('marker',$data);

	}

       public function select()
        {


                $data = array();

		if (!isset($_POST['panorama_sel']))
		{
			$this->db->limit(1);
			$this->db->select('file,id,name');
			$query = $this->db->get('panorams');
			$array_results= $query->row_array();
			$data['panorama_id'] =$array_results['id'];

			$this->db->select('name');
			$query = $this->db->get_where('panorams', array('id' => $data['panorama_id']));
			$array_results= $query->row_array();
			$data['name']=$array_results['name'];

		}
		else
		{
			$data['panorama_id']= $this->input->post('panorama_sel');

			$this->db->select('name');
			$query = $this->db->get_where('panorams', array('id' => $data['panorama_id']));
			$array_results= $query->row_array();
			$data['name']=$array_results['name'];

	


		}
        		$this->db->select('instructions');
			$query = $this->db->get('config');
			$result = $query->row_array();
			$data['instructions'] = $result['instructions'];

			$this->load->view('marker',$data);
        }

		public function map()
		{
			$this->load->view('map');
		}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */