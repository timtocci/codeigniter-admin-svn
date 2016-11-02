<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Panorama extends CI_Controller {

		public function __construct() {
        parent::__construct();
        $this->load->helper('form');
		$this->load->helper('url');
        $this->load->database();
		$this->load->model('Pano_model');
		$this->load->model('Login_model');
		set_time_limit(0);
		ini_set('memory_limit', '120M');

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

	
		$select_array = $this->Pano_model->get_pano_array();
		$this->db->select('instructions');
		$query = $this->db->get('config');
		$result = $query->row_array();

		$data['instructions'] = $result['instructions'];
                $data['select_array'] = $select_array;
                $this->load->view('select_pano',$data);
                //phpinfo();
                //$this->load->view('panorama');
	}


        public function get_url()
        {
                $this->db->select('file');
		$panorama_id = mysql_real_escape_string($_POST['panorama_id']);
                $query = $this->db->get_where('panorams', array('id' =>  $panorama_id));
                $array_results= $query->result_array();
                echo json_encode($array_results[0]['file']);
                
        }

        public function get_pano_info()
        {
                $this->db->select('name, heading');
				$panorama_id = mysql_real_escape_string($_POST['panorama_id']);
                $query = $this->db->get_where('panorams', array('id' =>  $panorama_id));
				$array_results= $query->result_array();



				$query = $this->db->query("
						SELECT m.id, m.name, m.link, i.file, m.x_gradus, m.y_gradus, i.id AS file_id
						FROM markers AS m, images AS i
						WHERE m.panorama_id = $panorama_id
						AND m.image = i.id");

				$array_results2= $query->result_array();

				$array_results[0]['markers'] = $array_results2;

				echo json_encode($array_results[0]);

        }

		public function add_marker()
		{
			$panorama_id = mysql_real_escape_string($_POST['pano_id']);
			$marker_name = mysql_real_escape_string($_POST['marker_name']);
			$marker_link = mysql_real_escape_string($_POST['marker_link']);
			$marker_image_select = mysql_real_escape_string($_POST['marker_image_select']);

			$insert_data = array(
						   'panorama_id' => $panorama_id ,
						   'name' => $marker_name,
						   'link' => $marker_link,
						   'image' => $marker_image_select,
						);

			$this->db->insert('markers', $insert_data);

			$id= $this->db->insert_id();

			$otvet = array();
			$otvet['id'] = $id;

			$this->db->select('file');
            $query = $this->db->get_where('images', array('id' => $marker_image_select));
			$array_results= $query->result_array();
			$otvet['file'] = $array_results[0]['file'];

			echo json_encode($otvet);
			
			exit;


		}

		public function save_markers()
		{
		$data = $_POST['data'];

		foreach ($data as $key => $value)
			{
 
				$updata = array(
							   'name' => $value['name'],
							   'link' => $value['link'],
							   'image' => $value['file_id'],
							   'x_gradus' => $value['x_gradus'],
							   'y_gradus' => $value['y_gradus']
							);

				$this->db->where('id', $key);
				$this->db->update('markers', $updata);
 		
			}

		echo "ok";
		exit;
		}


        public function select()
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

		if (!isset($_POST['panorama_sel']))
		{
			$this->db->limit(1);
			$this->db->select('file,id,name');
			$query = $this->db->get('panorams');
			$array_results= $query->row_array();
			$data['panorama_id'] =$array_results['id'];
			
		}
		else
		{
			$data['panorama_id']= $this->input->post('panorama_sel');
		}
                $this->load->view('panorama',$data);
        }


        public function add_manual()
        {

			foreach ($_FILES['uploadfile']['name'] as $key => $value)
			{
					//echo "$key -> $value <br/>";
				if ($_FILES['uploadfile']['error'][$key] === 0)
				{
					$filename = substr(md5(uniqid(rand(), true)), 0, rand(7, 13));
					//$dir_filename = $filename;

					$filename_old = basename($_FILES['uploadfile']['name'][$key]);
					$result = array ();
					preg_match("/\.(.*?)$/",$filename_old,$result);
					$filename = $filename.$result[0];


					if(copy($_FILES['uploadfile']['tmp_name'][$key],"uploads/".$filename))
						{

							$this->Pano_model->imagecreator($filename);

						   $data= array ();
						   //$data['panorama']=$filename;
						   $data['links']=array();

						   $insert_data = array(
						   'file' => $filename ,
						   'name' => $_POST['name'][$key] ,
						   'adress' => $_POST['adress'][$key] ,
						);

						$this->db->insert('panorams', $insert_data);
						}
				 }

			}

			$data['panorama_id'] = $this->db->insert_id();
			$select_array = $this->Pano_model->get_pano_array();

			$data['select_array'] = $select_array;
			$this->load->view('panorama', $data);
		}

        public function get_links()
        {
          $this->db->select('panorama_id_to_link, title, heading');

	  $panorama_id = mysql_real_escape_string($_POST['panorama_id']);


          $query = $this->db->get_where('links', array('panorama_id' => $panorama_id));
          $array_results= $query->result_array();

          echo json_encode($array_results);
        }

        public function add_link()
        {
          $data = array(
           'panorama_id_to_link' =>  mysql_real_escape_string($_POST['panorama_sel_link']) ,
           'panorama_id' =>  mysql_real_escape_string($_POST['panorama_id']) ,
           'title' =>  mysql_real_escape_string($_POST['title']),
           'heading' => mysql_real_escape_string($_POST['heading'])
            );

          $this->db->insert('links', $data);

  				$heading_user = mysql_real_escape_string($_POST['heading_user']);
				$data = array('heading' => $heading_user);
				$this->db->where('id', $_POST['panorama_id']);
				$this->db->update('panorams', $data);




                $data = array();

                $data['panorama_id']= (int)$_POST['panorama_id'];

                $this->db->select('file,id,name');
				$this->db->where('id !=', mysql_real_escape_string($data['panorama_id']));
                $query = $this->db->get('panorams');
                $array_results= $query->result_array();

                $select_array= array ();

                foreach ($array_results as $value)
                {
                    $select_array[$value['id']] = "{$value['file']} ({$value['name']}) ";
                }

                $data['select_array'] = $select_array;

                $this->load->view('panorama',$data);

        }


        public function add_manual_link()
        {

	foreach ($_FILES['uploadfile']['name'] as $key => $value)
	{
			//echo "$key -> $value <br/>";
		if ($_FILES['uploadfile']['error'][$key] === 0)
		{
			$filename = substr(md5(uniqid(rand(), true)), 0, rand(7, 13));
			$filename_old = basename($_FILES['uploadfile']['name'][$key]);
			$result = array ();
			preg_match("/\.(.*?)$/",$filename_old,$result);
			$filename = $filename.$result[0];


			if(copy($_FILES['uploadfile']['tmp_name'][$key],"uploads/".$filename))
			    {

				   $this->Pano_model->imagecreator($filename);

			       $data= array ();
			       //$data['panorama']=$filename;
			       $data['links']=array();

			       $insert_data = array(
			       'file' => $filename ,
			       'name' => $_POST['name'][$key] ,
			       'adress' => $_POST['adress'][$key] ,
				);

				$this->db->insert('panorams', $insert_data);

				$new_panorama_id = $this->db->insert_id();

				$data = array(
			       'panorama_id_to_link' => $new_panorama_id,
			       'panorama_id' => $_POST['panorama_id'] ,
			       'title' => $_POST['title'],
			       'heading' => $_POST['heading']
				);

				$this->db->insert('links', $data);

				$heading_user = mysql_real_escape_string($_POST['heading_user']);
				$data = array('heading' => $heading_user);
				$this->db->where('id', $_POST['panorama_id']);
				$this->db->update('panorams', $data);

			    }
		 }

	}

	$data['panorama_id']= (int)$_POST['panorama_id'];
	$this->load->view('panorama',$data);


        }

	public function change_user_heading()
	{
	$heading = mysql_real_escape_string($_POST['heading']);
	$data = array('heading' => $heading);
	$this->db->where('id', mysql_real_escape_string($_POST['pano_id']));
	$this->db->update('panorams', $data);

	$_POST['panorama_sel'] = mysql_real_escape_string($_POST['pano_id']);

	$this->select();
	}

		public function data_table()
	{

	if (!isset ($_GET['sSearch']))
	{
	 foreach ($_POST as $key => $value) {
	  $_GET[$key] = $value;
	 }
	}
	$aColumns = array('id','id','name', 'adress','id');

	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";

	/* DB table to use */
	$sTable = "panorams";

	/*
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}


	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}

		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}


	/*
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}

	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}


	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
	//$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());

	$rResult = $this->db->query($sQuery);
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	//$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	
	$rResultFilterTotal = $this->db->query($sQuery);
	
	//$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal->result_id);
	
	$iFilteredTotal = $aResultFilterTotal[0];

	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	//$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$rResultTotal = $this->db->query($sQuery);
	//
	//$aResultTotal = mysql_fetch_array($rResultTotal);
	$aResultTotal= mysql_fetch_array($rResultTotal->result_id);
	
	$iTotal = $aResultTotal[0];


	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);

	while ( $aRow = mysql_fetch_array($rResult->result_id) )
	{
		$id_triger = 0;
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "id" )
			{
				if ($id_triger == 0)
				{
				/* Special output formatting for 'version' column */
				$row[] = '<input type="radio" name="'.$_GET['radio_name'].'" value="'.$aRow[ $aColumns[$i] ].'">';
				}
				if ($id_triger == 1)
				{
				/* Special output formatting for 'version' column */
				$row[] = $aRow[ $aColumns[$i] ];
				}

				if ($id_triger == 2)
				{
				$row[] = '<a class="del_panorama" href="'.base_url().'index.php/panorama/pano_del?id='.$aRow[ $aColumns[$i] ].'">delete</a> ';
				}
				$id_triger += 1;
			}
			else if ( $aColumns[$i] != ' ' )
			{
				/* General output */
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['aaData'][] = $row;
	}

	echo json_encode( $output );
	exit();
	}



	public function data_table_link()
	{

	if (!isset ($_GET['sSearch']))
	{
	 foreach ($_POST as $key => $value) {
	  $_GET[$key] = $value;
	 }
	}
	$aColumns = array('title', 'heading','id');

	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";

	/* DB table to use */
	$sTable = "links";

	/*
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}


	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}

		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}


	/*
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}

	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	if ($sWhere != "")
	{
	$sWhere .=" AND panorama_id='".mysql_real_escape_string($_GET['id'])."'";
	}
	else
	{
	$sWhere .=" WHERE panorama_id='".mysql_real_escape_string($_GET['id'])."'";
	}
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
	//$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());

	$rResult = $this->db->query($sQuery);

	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	//$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());

	$rResultFilterTotal = $this->db->query($sQuery);

	//$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);

	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal->result_id);

	$iFilteredTotal = $aResultFilterTotal[0];

	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	//$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$rResultTotal = $this->db->query($sQuery);
	//
	//$aResultTotal = mysql_fetch_array($rResultTotal);
	$aResultTotal= mysql_fetch_array($rResultTotal->result_id);

	$iTotal = $aResultTotal[0];


	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);

	while ( $aRow = mysql_fetch_array($rResult->result_id) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "id" )
			{
				/* Special output formatting for 'version' column */
				$row[] = '<a class="del_link" href="'.base_url().'index.php/panorama/link_del?id='.$aRow[ $aColumns[$i] ].'&cur_id='.$_GET['id'].'">delete</a> ';
			}
			else if ( $aColumns[$i] != ' ' )
			{
				/* General output */
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['aaData'][] = $row;
	}

	echo json_encode( $output );
	exit();
	}

	public function link_del()
	{
	$id = mysql_real_escape_string($_GET['id']);

	$this->db->where('id', $id);
	$this->db->delete('links');

	$data['panorama_id']= $this->input->get('cur_id');

	$this->load->view('panorama',$data);

	}

	public function  pano_del()
	{
	$id = mysql_real_escape_string($_GET['id']);

	$this->db->select('file');
	$this->db->where('id', $id);
	$query = $this->db->get('panorams');

	if ($query->num_rows() > 0)
	{
	$row = $query->row_array();

	unlink("uploads/".$row['file']) or die ("Can not remove file");

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

	$select_array = $this->Pano_model->get_pano_array();
      $data['select_array'] = $select_array;

		$this->db->select('instructions');
		$query = $this->db->get('config');
		$result = $query->row_array();

		$data['instructions'] = $result['instructions'];


        $this->load->view('select_pano',$data);


	}

	public function instructions()
	{
	$instructions = mysql_real_escape_string($_POST['instructions']);
	$data = array('instructions' => $instructions);
	//$this->db->where('id', 3);
	$this->db->update('config', $data);
	$this->index();
	}

	public function import()
	{

	$content = @file_get_contents("extra/description.txt") OR DIE("Not Found extra/description.txt ".'<br/> <a href="'.base_url().'index.php/panorama">'."return to adminpage");
	//								1		2		3		4		5		6		7		8
	// 
	//<<<#Panorama:::File name:::Panorama Name:::Panorama Address:::centerHeading:::Arrows(degree::label::panorama link):::KML Phomo name:::Line name>>>
	$count = preg_match_all( "/<<<(.*?):::(.+):::(.*?):::(.*?):::(.*?):::(.*?):::(.*?):::(.*?)>>>/", $content,$result);

	$count_insert = 0;

	$new_id = array();

//	return;


	foreach ($result[2] as $key => $value)
	{
			//echo "$key -> $value <br/>";
			$filename = substr(md5(uniqid(rand(), true)), 0, rand(7, 13));

			$filename_old = basename($value);
			$result2 = array ();
			preg_match("/\.(.*?)$/",$filename_old,$result2);
			if (isset($result2[0]))
			{
			$filename = $filename.$result2[0];
			}

			if(@copy("extra/".$value,"uploads/".$filename))
			{

				   $this->Pano_model->imagecreator($filename);
			       $data= array ();
			       $insert_data = array(
			       'file' => $filename ,
			       'name' => $result[3][$key],
				   'adress' => $result[4][$key],
				   'heading' => $result[5][$key]
				);

				$this->db->insert('panorams', $insert_data);
				$id= $this->db->insert_id();
				$new_id[$result[1][$key]] = $id;

				$count_insert++;
			}



	}

	foreach ($result[6] as $key => $value)
	{
			if (isset($new_id[$result[1][$key]]) == false)
			{
			 continue; //Proveray dobavilas li eta panorama pri importe
						// esli ona dobavlena to ona imeet svoi id
			}

			$arrow_count = preg_match_all("/\((.+?)::(.+?)::(.+?)\)/", $value,$arrows);
			if ($arrow_count != 0)
			{
			 foreach ($arrows[3] as $key_arrow => $value_link_to)
				{
				if(preg_match("/panorama_sel=(.+)/", $value_link_to , $id_link_to) != 0)
				{
					$id_link_to= trim($id_link_to[1]);
				}
				else
				{
					@$id_link_to = $new_id[$value_link_to];
				}
				if ($id_link_to == null)
				{
					continue;
				}

				$data = array(
				'panorama_id_to_link' => $id_link_to  ,
				'panorama_id' => $new_id[$result[1][$key]] ,
				'title' =>  trim($arrows[2][$key_arrow]),
				'heading' => trim($arrows[1][$key_arrow])
				);

				$this->db->insert('links', $data);

				}
			}
	}

	
	$points=@simplexml_load_file("extra/description.KML");
	$lines =array();
	if($points !== false)
	{

		// 7  - KLM name
		foreach ($result[7] as $key => $value)
		{

			 foreach ($points->Document->Folder->Placemark as $valueplace)
			 {
			 if ($valueplace->name == $value)
			 {
				   if ($result[8][$key] != '') // t.e Line have name
				   {
					$lines[$result[8][$key]]['coordinates'][] = (string)$valueplace->Point->coordinates;
					// Add link to panorama id
					$lines[$result[8][$key]]['panorams_id'][] = $new_id[$result[1][$key]];
				   }
			 }
			}

		}

		foreach ($lines as $key => $value)
		{
			$data = array(
			'link' => '',
			'line_color' => 'b82cb8' ,
			'fill_color' =>  'b82cb8' ,
			'info' => '',
			'object_type' => 1,
			'link_name' => $key,
			'width' => 4
			);
			$this->db->insert('maps_overlays', $data);
			$id =  $this->db->insert_id();
	
			foreach ($value['coordinates'] as $key => $point)
			{
			 $tep_coord =  explode(",", $point);
			 $a = 0;
			$data = array(
			'overlay_id' => $id,
			'lat' => $tep_coord[1] ,
			'lng' => $tep_coord[0],
			'panorama_id' => $value['panorams_id'][$key]
			);
			$this->db->insert('overlays_items', $data);
			
			}

		}
/*
*/
		}
	




	echo "Total lines in file: $count, Inserted panorams: $count_insert";
	echo '<br/> <a href="'.base_url().'index.php/panorama">'."return to adminpage";

	}

	public function marker()
	{


		if (!isset($_POST['panorama_sel']))
		{
			$this->db->limit(1);
			$this->db->select('file,id,name');
			$query = $this->db->get('panorams');
			$array_results= $query->row_array();
			$data['panorama_id'] =$array_results['id'];

		}
		else
		{
			$data['panorama_id']= $this->input->post('panorama_sel');
		}
            

		$this->load->view('marker',$data);

	}

	function marker_image_table()
	{

	if (!isset ($_GET['sSearch']))
	{
	 foreach ($_POST as $key => $value) {
	  $_GET[$key] = $value;
	 }
	}
	$aColumns = array('id', 'file');

	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";

	/* DB table to use */
	$sTable = "images";

	/*
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}


	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}

		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}


	/*
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}

	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
	//$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());

	$rResult = $this->db->query($sQuery);

	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	//$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());

	$rResultFilterTotal = $this->db->query($sQuery);

	//$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);

	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal->result_id);

	$iFilteredTotal = $aResultFilterTotal[0];

	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	//$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$rResultTotal = $this->db->query($sQuery);
	//
	//$aResultTotal = mysql_fetch_array($rResultTotal);
	$aResultTotal= mysql_fetch_array($rResultTotal->result_id);

	$iTotal = $aResultTotal[0];


	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);

	while ( $aRow = mysql_fetch_array($rResult->result_id) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "id" )
			{
				/* Special output formatting for 'version' column */
				$row[] = '<input type="radio" name="marker_image_select" value="'.$aRow[ $aColumns[$i] ].'">';
			}
			if ($aColumns[$i] == "file")
			{
				/* General output */
				$row[] = '<img src="'.base_url().'uploads/markers/'.$aRow[ $aColumns[$i] ].'">';
			}
		}
		$output['aaData'][] = $row;
	}

	echo json_encode( $output );
	exit();
	}

	public function marker_image_upload()
        {

			foreach ($_FILES['uploadfile']['name'] as $key => $value)
			{
					//echo "$key -> $value <br/>";
				if ($_FILES['uploadfile']['error'][$key] === 0)
				{
					$filename = substr(md5(uniqid(rand(), true)), 0, rand(7, 13));
					//$dir_filename = $filename;

					$filename_old = basename($_FILES['uploadfile']['name'][$key]);
					$result = array ();
					preg_match("/\.(.*?)$/",$filename_old,$result);
					$filename = $filename.$result[0];


					if(copy($_FILES['uploadfile']['tmp_name'][$key],"uploads/markers/".$filename))
						{


						   $data= array ();
						   //$data['panorama']=$filename;
						   $data['links']=array();

						   $insert_data = array(
						   'file' => $filename
						);

						$this->db->insert('images', $insert_data);
						}
				 }

			}

			$data['panorama_id'] = $_POST['panorama_id'];
			$select_array = $this->Pano_model->get_pano_array();

			$data['select_array'] = $select_array;
			$this->load->view('panorama', $data);
		}

	public function delete_marker()
		{

			$id = mysql_real_escape_string($_POST['marker_id']);

			$this->db->where('id', $id);
			$this->db->delete('markers');

			echo "ok";
			exit;
		}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */