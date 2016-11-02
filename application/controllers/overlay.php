<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Overlay extends CI_Controller {

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
	//Chrome

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

		$this->load->view('overlay',$data);

	}

	public function save()
	{
		$select_id = mysql_real_escape_string($_POST['select_id']);

		$line_color = mysql_real_escape_string($_POST['line_color']);
		$fill_color = mysql_real_escape_string($_POST['fill_color']);
		$link = mysql_real_escape_string($_POST['link']);
		$link_name = mysql_real_escape_string($_POST['link_name']);
		$width = mysql_real_escape_string($_POST['width']);

		$info = mysql_real_escape_string($_POST['info']);
		$object_type = mysql_real_escape_string($_POST['object_type']);
		$markers_array = ($_POST['markers_array']);

		$insert_data = array(
						   'line_color' => $line_color ,
						   'fill_color' => $fill_color,
						   'link' => $link,
						   'link_name' => $link_name,
						   'info' => $info,
						   'object_type' => $object_type,
						   'width' => $width
						);

		if ($select_id == "zeroo")
		{
			// insert


			$this->db->insert('maps_overlays', $insert_data);

			$id= $this->db->insert_id();

			foreach ($markers_array as $key => $value)
			{
			 $markers_array[$key]['overlay_id'] = $id;
			}

			$this->db->insert_batch('overlays_items', $markers_array);

			echo "$id";


		}
		else
		{
			$this->db->where('id', $select_id);
			$this->db->update('maps_overlays', $insert_data);
			$this->db->delete('overlays_items', array('overlay_id' => $select_id));

			foreach ($markers_array as $key => $value)
			{
			 $markers_array[$key]['overlay_id'] = $select_id;
			}

			$this->db->insert_batch('overlays_items', $markers_array);

			echo "$select_id";

			
		}
	}

	public function load_all()
	{

		$overlays = array();
		$i = 0;
		$all = mysql_real_escape_string($_POST['all']);

		if ($all == "ok")
		{
			$query = $this->db->get('maps_overlays');
		}
		else
		{
			$query = $this->db->get_where('maps_overlays', array('id' => $all));
		}
		
		foreach ($query->result_array() as $row)
		{
			$overlays[$i]['id'] = $row['id'];
			$overlays[$i]['link'] = $row['link'];
			$overlays[$i]['line_color'] = $row['line_color'];
			$overlays[$i]['fill_color'] = $row['fill_color'];
			$overlays[$i]['info'] = $row['info'];
			$overlays[$i]['object_type'] = $row['object_type'];
			$overlays[$i]['link_name'] = $row['link_name'];
			$overlays[$i]['width'] = $row['width'];

			$markers = array();

			$query2 = $this->db->get_where('overlays_items', array('overlay_id' => $row['id']));

			foreach ($query2->result_array() as $marker)
			{
				$markers[] = array ('lat' => $marker['lat'], 'lng' => $marker['lng'], 'panorama_id' => $marker['panorama_id']);
			}

			$overlays[$i]['markers'] = $markers;

			$i++;
		}

		echo json_encode($overlays);
	}


	public function delete()
	{
		$select_id = mysql_real_escape_string($_POST['select_id']);
		$this->db->delete('overlays_items', array('overlay_id' => $select_id));
		$this->db->delete('maps_overlays', array('id' => $select_id));
		
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */