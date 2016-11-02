
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<style type="text/css" title="currentStyle">
	@import "<?=base_url()?>application/views/css/demo_table.css";
	</style>
    <script type="text/javascript" src="<?=base_url()?>application/views/js/jquery-1.5.2.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>application/views/js/jquery.dataTables.min.js"></script>



	<script type="text/javascript">

	$(document).ready(function() {

		$("#add_file").click(function(){
			var name = $(".name:last").val();
			var adress = $(".adress:last").val();
			$("#uploads_files").append('Select panorama file to upload:<br/><input type=file name=uploadfile[]><br/>   Input panorama name:<br/>  <input class="name" type=text name="name[]"><br/>   Input panorama adress:<br/>  <input class="adress" type=text name="adress[]"><br/><br/>');
			$(".name:last").val(name);
			$(".adress:last").val(adress);

		});

		$('#pano_table').dataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "<?=base_url()?>index.php/panorama/data_table?radio_name=panorama_sel"
		} );


		$("#form_sub").click(function(){

		var n = $("input:checked").length;
		if (n == 0)
			{
				alert("Please select a panorama using radio switch")
				return false
			}
		//var val = $("input:checked").val();
		return true;
		});

		$('.del_panorama').live('click',function(){

		return confirm("Do you really want to delete the panorama?");

		});


	});


	</script>
        <title>Upload panorams</title>
    </head>
<body style="margin-left: auto; margin-right: auto; width: 80% ">
<div style="margin: 10;">

<div style="border: solid #c77405">
<? echo form_open_multipart('panorama/import');?>
 Import files from directory "extra"<br/>
 The directory must contain a file description.txt
 File exemple:<br/>
<<<#Panorama:::File name:::Panorama Name:::Panorama Address:::centerHeading:::Arrows(degree::label::panorama link)>>><br/>
<br/>
<br/>
<?
echo <<<END
<<<#Panorama:::File name:::Panorama Name:::Panorama Address:::centerHeading:::Arrows(degree::label::panorama link):::KML Phomo name:::Line name>>>
	<br>
	<br>
<<<6:::1.jpeg:::Pano1 Import New:::Pan Adr1:::278:::(14.5::Lable Import link1::http://topmedellin.com/panosrc/?panorama_sel=23)(167::Lable Import link2::7):::P855:::Line1>>><br>
<<<22:::2.jpeg:::Pano2 Import New:::Pan Adr2:::78.5:::(80::Lable Import link1::http://topmedellin.com/panosrc/?panorama_sel=38)(167::Lable Import link2::6):::P856:::Line1>>><br>
<<<9:::3.jpeg:::Pano3 Import New:::Pan Adr3:::25:::(92::Lable Import link1::22):::P857:::Line2>>><br>
<<<7:::4.jpeg:::Pano4 Import New:::Pan Adr4:::89.1:::():::P858:::Line2>>><br>
<br>
<br>
You should call the KML file as description.KML
END;

?>
<? echo form_submit("mysubmit", 'Import');
   echo form_close();?>
</div>
<br/>

<div style="border: solid #1c94c4">
<? echo form_open_multipart('panorama/add_manual');?>
<div id="uploads_files" >
   Select panorama file to upload:<br/><input type=file name=uploadfile[]><br/>
   Input panorama name:<br/>  <input class="name" type=text name="name[]"><br/>
   Input panorama adress:<br/>  <input class="adress" type=text name="adress[]"><br/><br/>
</div>
<input type="button" id="add_file" style="border: solid #1c94c4; margin: 2em" value="+ Click to add another panorama to upload"> <br/>


<? echo form_submit("mysubmit", 'Upload');
   echo form_close();?>
</div>
   <br/>
	<div style="border: solid #1c94c4">
		<? echo form_open('panorama/instructions');?>
		Input instructions:<br/>  <textarea  cols="135" rows="20"  name="instructions"><? echo "$instructions";?></textarea><br/>
		<? echo form_submit("instruct", 'Change instructions');
		   echo form_close();?>
	</div>

   <br/>



	<?echo form_open('panorama/select');


	?>

	<div id="dynamic" >
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="pano_table">
			<thead>
				<tr>
					<th width="10%">Panorama select</th>
					<th width="10%">Panorama id</th>
					<th width="30%">Panorama name</th>
					<th width="40%">Panorama adress</th>
					<th width="10%">Delete panorama</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="5" class="dataTables_empty">Loading data from server</td>
				</tr>
			</tbody>

			<tfoot>
				<tr>
					<th width="10%">Panorama select</th>
					<th width="10%">Panorama id</th>
					<th width="30%">Panorama name</th>
					<th width="40%">Panorama adress</th>
					<th width="10%">Delete panorama</th>
				</tr>
			</tfoot>
		</table>
	</div>

	<? echo form_submit('selectpano', 'Select panorama','id="form_sub"');
        echo form_close();?>
       </div >

	   <a href="<?=base_url()?>index.php/edit"> EDIT PAGE</a>
    </body>
</html>
