<?php

class Pano_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}


	function get_pano_array()
	{
		$this->db->select('file,id,name');
		$query = $this->db->get('panorams');
		$array_results = $query->result_array();
		$select_array = array();
		foreach ($array_results as $value) {
			$select_array[$value['id']] = "{$value['name']}";
		}

		return $select_array;
	}

	function imagecreator($filename)
	{


			$nx = 16;
			$ny = 8;

			$zoom = 4;


			$im = imageCreateFromJpeg("uploads/".$filename);


			$temp_dir = "dir_$filename";

			mkdir("uploads/".$temp_dir, 0744);

			$temp_dir = "uploads/".$temp_dir;

			//return;

			$imSX = imageSX($im);
			$imSY = imageSY($im);

			$kusokX = $imSX/$nx;
			$kusokY = $imSY/$ny;

			for ($k = 0; $k <$ny; $k++)
			{

				for ($i = 0; $i < $nx; $i++)
				{
					$im2 = imagecreatetruecolor(512,512);

					imagecopyresized($im2,$im,0,0,$i*$kusokX,$k*$kusokY, 512,512,$kusokX,$kusokY);

					//imageInterlace($im2, 1);

					imagejpeg($im2,"$temp_dir/"."1_{$zoom}_{$i}_{$k}_.jpeg", 90);

					$im2 = null;
				}
			}

			$factor = 2;

			$zoom = $zoom - 1;

			for ($k = 0; $k <$ny/$factor; $k++)
			{

				for ($i = 0; $i < $nx/$factor; $i++)
				{
					$im2 = imagecreatetruecolor(512,512);

					imagecopyresized($im2,$im,0,0,$i*$kusokX*$factor,$k*$kusokY*$factor, 512,512,$kusokX*$factor,$kusokY*$factor);

					//imageInterlace($im2, 1);

					imagejpeg($im2,"$temp_dir/"."1_{$zoom}_{$i}_{$k}_.jpeg", 90);

					$im2 = null;
				}
			}

			$factor = 4;

			$zoom = $zoom - 1;

			for ($k = 0; $k <$ny/$factor; $k++)
			{

				for ($i = 0; $i < $nx/$factor; $i++)
				{
					$im2 = imagecreatetruecolor(512,512);

					imagecopyresized($im2,$im,0,0,$i*$kusokX*$factor,$k*$kusokY*$factor, 512,512,$kusokX*$factor,$kusokY*$factor);

					//imageInterlace($im2, 1);

					imagejpeg($im2,"$temp_dir/"."1_{$zoom}_{$i}_{$k}_.jpeg", 90);

					$im2 = null;
				}
			}

			$factor = 8;

			$zoom = $zoom - 1;

			for ($k = 0; $k <$ny/$factor; $k++)
			{

				for ($i = 0; $i < $nx/$factor; $i++)
				{
					$im2 = imagecreatetruecolor(512,512);

					imagecopyresized($im2,$im,0,0,$i*$kusokX*$factor,$k*$kusokY*$factor, 512,512,$kusokX*$factor,$kusokY*$factor);

					//imageInterlace($im2, 1);

					imagejpeg($im2,"$temp_dir/"."1_{$zoom}_{$i}_{$k}_.jpeg", 90);

					$im2 = null;
				}
			}


			$im2 = imagecreatetruecolor(512,512);

			imagecopyresized($im2,$im,0,0,0,0, 512,256,$imSX,$imSY);

			imagejpeg($im2,"$temp_dir/"."1_0_0_0_.jpeg", 90);



	}

}

?>
