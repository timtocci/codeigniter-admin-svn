
<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<style type="text/css" title="currentStyle">
	@import "<?=base_url()?>application/views/css/demo_table.css";
	</style>
        <title>Panorama</title>
        <script type="text/javascript" src="<?=base_url()?>application/views/js/jquery-1.5.2.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>application/views/js/jquery-ui-1.8.11.custom.min.js"></script>
	<link href="http://code.google.com/apis/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />
        <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
   <script type="text/javascript" src="<?=base_url()?>application/views/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript">
       window.pano_id = <?=$panorama_id?>;
       window.pano_id = <?=$panorama_id?>;
        var panorama;
        var entryPanoId = null;
        var mapid = 1;

		function drug_apply() {

					$(".int_link_div").draggable({
                    containment:'parent',
					start: function(event, ui) {

						$(".int_link_div").css("border", "solid #D6FF5C");

						if(window.selected_object == undefined)
						{
							window.selected_object = parseInt(this.id);
						}
						else
						{
							markers[window.selected_object].link = 	$("#marker_link").val();
							markers[window.selected_object].name = $("#marker_name").val();
							window.selected_object = parseInt(this.id);
						}

						$("#marker_link").val(markers[window.selected_object].link);
						$("#marker_name").val(markers[window.selected_object].name);

						$(this).css("border", "solid lime");



					},
                    stop: function(event, ui) {
                        var c=ui.position.left;
                        var d=ui.position.top;

							markers[parseInt(this.id)].x_gradus = c/(1000/360.0);
							markers[parseInt(this.id)].y_gradus = d/(400/90.0);
						}

					});

		}




        function initialize() {
            // Set up Street View and initially set it visible. Register the
            // custom panorama provider function. Set the StreetView to display
            // the custom panorama 'reception' which we check for below.
            var panoOptions = {
                pano: '<?=$panorama_id?>',
                visible: true,
                linksControl:true,
                panoProvider: getCustomPanorama
            }
            panorama = new google.maps.StreetViewPanorama(document.getElementById('pan1'),panoOptions);

            google.maps.event.addListener(panorama, 'links_changed', createCustomLinks);
        }
        // Return a pano image given the panoID.
        function getCustomPanoramaTileUrl(pano,zoom,tileX,tileY) {
            // Note: robust custom panorama methods would require tiled pano data.
            // Here we're just using a single tile, set to the tile size and equal
            // to the pano "world" size.
            //alert(mapid);

  //          var A = $("#panorama").css("width");
  //          var B = $("#panorama").css("heigth");
            $("#flatimg").attr("src", '<?=base_url()?>uploads/'+ CustomPanoramaTileUrlurl);
			$("#flatimg2").attr("src", '<?=base_url()?>uploads/'+ CustomPanoramaTileUrlurl);
			$("#flatimg3").attr("src", '<?=base_url()?>uploads/'+ CustomPanoramaTileUrlurl);
      //      $("#flatimg").css("width", $("#panorama").css("width") );
      //      $("#flatimg").css("heigth", $("#panorama").css("heigth") );

		return '<?=base_url()?>uploads/'+"dir_"+window.CustomPanoramaTileUrlurl+ "/1_"+zoom+"_" + tileX+ "_"+tileY +"_.jpeg";

        }
        // Construct the appropriate StreetViewPanoramaData given
        // the passed pano IDs.
        function getCustomPanorama(pano,zoom,tileX,tileY) {


          window.CustomPanoramaTileUrlurl = 0;

                $.post("<?=base_url()?>index.php/panorama/get_url",{ "panorama_id": pano },
                 function(data){
                     //alert(data);
                    window.CustomPanoramaTileUrlurl = data;
                 }, "json");


                 var desct;
                 $.post("<?=base_url()?>index.php/panorama/get_pano_info",{ "panorama_id": pano },
                 function(data){

                    desct = data;
					$('#input_user_heading_m').attr("value",data.heading);
					$('#input_user_heading_m2').attr("value",data.heading);
					$('#input_user_heading_m3').attr("value",data.heading);
					window.center_head = data.heading;


					var wid =1000;
					var hid =400;

					wid = wid/360.0;
					hid = hid/90.0;

					markers = {};
					new_markers = new Array();

					for (var n in data.markers)
						{
							var id = data.markers[n].id
							markers[id] = {};
							markers[id].name = data.markers[n].name;
							markers[id].link = data.markers[n].link;
							markers[id].file = data.markers[n].file;
							markers[id].x_gradus = data.markers[n].x_gradus;
							markers[id].y_gradus = data.markers[n].y_gradus;
							markers[id].file_id = data.markers[n].file_id;

							$("#panorama3").append('<div class="int_link_div" id="'+id+'marker" style="border: solid #D6FF5C;	width: 50px; height: 50px; position: absolute;left:'+parseInt(data.markers[n].x_gradus * wid) +'px; top:'+parseInt(data.markers[n].y_gradus * hid)+'px"><img style="width: 50px; height: 50px;" src="<?= base_url() ?>uploads/markers/'+data.markers[n].file+'"></div>')

						}

					drug_apply();


					//
                 }, "json");

                    return {
                        location: {
                            pano: pano ,
                            description: desct.name
                        },

                        // The text for the copyright control.
                        //copyright: 'Imagery (c) 2010 Google',
                        // The definition of the tiles for this panorama.
                        tiles: {
                            tileSize: new google.maps.Size(512, 512),
                            worldSize: new google.maps.Size(8192, 4096),
                            centerHeading: (180 - parseInt(desct.heading,0)),
                            getTileUrl: getCustomPanoramaTileUrl
                        }
                 };
            }

        function createCustomLinks() {

                var links = panorama.getLinks();
                var panoId = panorama.getPano();
                // делаю аякс запрос и по номеру panoId выбираю из базы ссылки

                function get_links(data){


                    for (var arr in data)
                        {

                             links.push({
                            'heading': data[arr].heading - window.center_head,
                            'description' : data[arr].title ,
                            'pano' : data[arr].panorama_id_to_link
                             });

                        }
                 }

                $.post("<?=base_url()?>index.php/panorama/get_links", { "panorama_id": panoId },
                 get_links, "json");
                                /*
                        links.push({
                            'heading': 0,
                            'description' : '0 градусов',
                            'pano' : 'NAstor6'
                        });
                        links.push({
                            'heading': 30,
                            'description' : '30 град',
                            'pano' : 'SAstor26'

            */
        }




          $(document).ready(init);

          function init(){

            $.ajaxSetup(
             {
              type:'POST',
              scriptCharset:'utf-8',
              async:false,
              error: function (xhr,ajaxOptions,thrownError)
              {
               alert(thrownError);
              }
             });

              $("#arrow").draggable({
                    containment:'parent',
                    stop: function(event, ui) {
                        var a=event.type;
                        var c=ui.position.left;
                        var d=ui.position.top;
                        var e=ui.offset.left;
                        var f=ui.offset.top;
                        var wid =$("#flatimg").css("width");
                        wid = parseInt(wid,0);
                        if (c < 0) c = 0;
                        if (c > wid) c = wid;
                        var grad = (c/wid)*360;
                        $('#input1').attr("value",grad);
                        $('#heading').attr("value",grad);
                        $('#heading_m').attr("value",grad);

                        window.grad = grad;
                    }

              });

          $("#arrow2").draggable({
                    containment:'parent',
                    stop: function(event, ui) {
                        var a=event.type;
                        var c=ui.position.left;
                        var d=ui.position.top;
                        var e=ui.offset.left;
                        var f=ui.offset.top;
                        var wid =$("#flatimg").css("width");
                        wid = parseInt(wid,0);
                        if (c < 0) c = 0;
                        if (c > wid) c = wid;
                        var grad = (c/wid)*360;
                        $('#input_user_heading_m').attr("value",grad);
						$('#input_user_heading_m2').attr("value",grad);
						$('#input_user_heading_m3').attr("value",grad);

                        window.grad = grad;
                    }

              });


            $("#add_link").click(function(){

                var panoId = panorama.getPano();

                $("#panorama_id").attr("value", panoId);


		var n = $('input[name="panorama_sel_link"]:checked').length;
		if (n == 0)
			{
				alert("Please select a panorama to link using radio switch")
				return false
			}
                return true;
            })

            $("#upload").click(function(){

                var panoId = panorama.getPano();

                $("#panorama_id_m").attr("value", panoId);

                return true;
            });

		   $("#upload_marker_image").click(function(){

                var panoId = panorama.getPano();

                $("#panorama_id_marker").attr("value", panoId);

                return true;
            })

	        initialize();


		$('#pano_table').dataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "<?=base_url()?>index.php/panorama/data_table?radio_name=panorama_sel_link"
		} );

		$('#pano_table2').dataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "<?=base_url()?>index.php/panorama/data_table?radio_name=panorama_sel"
		} );

		$("#form_sub2").click(function(){

		var n = $('input[name="panorama_sel"]:checked').length;
		if (n == 0)
			{
				alert("Please select a panorama using radio switch")
				return false
			}
		//var val = $("input:checked").val();
		return true;
		});

		$('#pano_table3').dataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "<?=base_url()?>index.php/panorama/data_table_link?id=<?=$panorama_id?>"
		} );

		$('#marker_table').dataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "<?=base_url()?>index.php/panorama/marker_image_table"
		} );



	$('.del_link').live('click',function(){

		return confirm("Do you really want to delete the link?");

		});

	$('.del_panorama').live('click',function(){

		return confirm("Do you really want to delete the panorama?");

		});

	$('#marker_add').live('click',function(){



		var n = $('input[name="marker_image_select"]:checked').length;
		if (n == 0)
			{
				alert("Please select a picture using radio switch")
				return false
			}

		if ($("#marker_link").val() == "")
			{
				alert("Please input marker link");
				return false
			}


		$.post("<?=base_url()?>index.php/panorama/add_marker",
				{ "marker_name": $("#marker_name").val(),
				  "marker_link": $("#marker_link").val(),
				  "marker_image_select" : $('input[name="marker_image_select"]:checked').val(),
				  "pano_id":  panorama.pano },
                 function(data){

							markers[data.id] = {};
							markers[data.id].name = $("#marker_name").val();
							markers[data.id].link = $("#marker_link").val();
							markers[data.id].file = data.file;
							markers[data.id].x_gradus = 0;
							markers[data.id].y_gradus = 0;
							markers[data.id].file_id = $('input[name="marker_image_select"]:checked').val();

							$("#panorama3").append('<div class="int_link_div" id="'+data.id+'marker" style="border: solid #D6FF5C;	width: 50px; height: 50px; position: absolute;left:'+0+'px; top:'+0+'px"><img style="width: 50px; height: 50px;" src="<?= base_url() ?>uploads/markers/'+data.file+'"></div>')

							drug_apply();

				}
				 , "json");


	});

	$('#delete_marker').click(function(){

	if (window.selected_object == undefined)
	{
		alert("Select marker to delete");
		return false;
	}

	$.post("<?=base_url()?>index.php/panorama/delete_marker", { "marker_id": window.selected_object },
                 function(data){
					 if(data == "ok")
						 {
							 $("#"+window.selected_object+"marker").remove();
							 window.selected_object = undefined;
						 }
				 }
				 , "text");


	});


	$('#save_marker').click(function(){

		$.post("<?=base_url()?>index.php/panorama/save_markers",
				{ "data": markers},
                 function(data){

					if( data !="ok")
						{
							alert("Save Error");
						}
				}
				 , "text");


	});

};

        </script>
    </head>
<body style="margin-left: auto; margin-right: auto; width: 80% ">
        <?php

        ?>
	<h3>Direct USER Link to this panorama</h3>
	<h2><?=base_url()."?panorama_sel=$panorama_id"?></h2>
        <div id="pan1" style="width: 1000px; height: 800px; border: solid #002166 ; ">
        </div>
        <div style="height: 10px;"></div>
        <div  style="width: px; height: 200px; border: solid #002166; position: absolute; ">
		<img id="flatimg"  width="1000" height="200">
        </div>
        <div id="panorama" style="width: 1035px; height: 200px; border: solid #002166">


		<div id="arrow" style="border: solid red; width: 30px; height: 150px; margin-top : 20px; margin-bottom: 20px">
			<img src="<?= base_url() ?>application/views/img/arrow.png">
		</div>
        </div>



        Rotation <input id="input1" type="text" name="rotation" value="0">
        <br>

        To choose the direction of new link drag the <span style="text-decoration: blink; color: red;">red arrow</span>


    <div style="margin: 10px; width: 1000px">
       <div style="border: solid #0134c5">
               <form action="<?=base_url()?>index.php/panorama/add_manual_link" method=post enctype=multipart/form-data>

               Select NEW panorama file to upload and create link "THIS->NEW" panorama:<br/>
               <input type=file name=uploadfile[]><br/>
               Input panorama name:<br/>  <input type="text" name="name[]"><br/>
               Input panorama adress:<br/>  <input type="text" name="adress[]"><br/>
               Input link title:<br/>  <input type="text" name="title" size="100" value="http://integralstable.com/top10/"><br/>
                <input type="hidden" name="panorama_id" id="panorama_id_m" value="">
                <input type="hidden" name="heading" id="heading_m" value="0">
				<input type="hidden" name="heading_user" id="input_user_heading_m2" value="0">


               <input type=submit value="Upload and create a link" id="upload">
            </form>
       </div>
   <br/>

   OR
   <br/>
   <br/>

   <div style="border: solid red">
        <?echo form_open('panorama/add_link');?>
	<div id="dynamic1" >
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
			<tbody class="linkf">
				<tr>
					<td colspan="5" class="dataTables_empty" class="table1">Loading data from server</td>
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

       <input type="hidden" name="panorama_id" id="panorama_id" value="">
       <input type="hidden" name="heading" id="heading" value="0">
	   <input type="hidden" name="heading_user" id="input_user_heading_m3" value="0">
        <br/>Input link title:<br/>
        <input type="text" name="title" id="form_sub" size="100" value="http://integralstable.com/top10/">
        <?
        echo form_submit('selectpano', 'Create a link',"id='add_link'");
        echo form_close();
        ?>

        </div>

   <br/>
   <!-- Select Link to delete -->

   <div style="border: solid #990000">
	   Choose link to delete <br/>

	   <div id="dynamic3" >
		   <table cellpadding="0" cellspacing="0" border="0" class="display" id="pano_table3">
			   <thead>
				   <tr>
					   <th width="60%">Link title</th>
					   <th width="30%">Link heading</th>
					   <th width="10%">Link del</th>
				   </tr>
			   </thead>
			   <tbody>
				   <tr>
					   <td colspan="5" class="dataTables_empty">Loading data from server</td>
				   </tr>
			   </tbody>

			   <tfoot>
				   <tr>
					   <th width="60%">Link title</th>
					   <th width="30%">Link heading</th>
					   <th width="10%">Link del</th>
				   </tr>
			   </tfoot>
		   </table>
	   </div>




   </div>



   <br/>


    <!-- Select Link to delete -->
	<!-- centerHeading: ,  -->
        <div style="height: 10px;"></div>

        <div  style="width: 1000px; height: 200px; border: solid #002166; position: absolute; ">
		<img id="flatimg2"  width="1000" height="200">
        </div>
        <div id="panorama2" style="width: 1035px; height: 200px; border: solid #002166">


		<div id="arrow2" style="border: solid red; width: 30px; height: 150px; margin-top : 20px; margin-bottom: 20px">
			<img src="<?= base_url() ?>application/views/img/arrow.png">
		</div>
        </div>

		Choose centerHeading panoram on USER view, If you do not touch the arrow, the orientation of the panorama will remain unchanged <br/>
<br/>
	<?echo form_open('panorama/change_user_heading');?>
    <input type="hidden" name="heading" id="input_user_heading_m" value="0">
	<input type="hidden" name="pano_id" value="<?=$panorama_id?>">
	<? echo form_submit('selectpano', 'Change USER heading');
        echo form_close();?>
	<br/>


	<div style="border: solid #1c94c4; width: 1000px;">





		Internal Panorama Markers
        <div style="height: 10px;"></div>

        <div  style="width: 1000px; height: 400px; border: solid #002166; position: absolute; ">
		<img id="flatimg3"  width="1000" height="400">
        </div>
        <div id="panorama3" style="width: 1000px; height: 400px; border: solid #002166; position: relative">
        </div>
		</br>
		<input type="button" id="delete_marker" value="Delete selected marker">
		<input type="button" id="save_marker" value="Save markers changes"></br>
		Marker name </br>
		<input type="text" id="marker_name" name="marker_name" value=""></br>
		Marker link </br>
		<input type="text" id="marker_link" name="marker_link" value="" style="width: 90%"></br> </br>

		<input type="button" id="marker_add" name="marker_name" value="Add new marker"></br>
		Choose image for Marker</br>
		<div id="dynamic3" style="border: solid #002166;">
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="marker_table">
			<thead>
				<tr>
					<th width="10%">Image select</th>
					<th width="90%">Image</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="5" class="dataTables_empty">Loading data from server</td>
				</tr>
			</tbody>

			<tfoot>
				<tr>
					<th width="10%">Image select</th>
					<th width="90%">Image</th>
				</tr>
			</tfoot>
		</table>
	</div>

	<br/>

		</br>

		</br>
		</br>


		<div style="border: double #D6FF5C ; margin: 5 5 5 5">
		Upload image for Marker
	           <form action="<?=base_url()?>index.php/panorama/marker_image_upload" method=post enctype=multipart/form-data>


               <input type=file name=uploadfile[]><br/>
               <input type="hidden" name="panorama_id" id="panorama_id_marker" value="">


               <input type=submit value="Upload image for Marker" id="upload_marker_image">
            </form>

		</div>

		</div>

<br/><br/>

	<div style="border: solid #1c94c4">

	Choose another panorama to view <br/>
	<?echo form_open('panorama/select');



	?>

	<div id="dynamic2" >
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="pano_table2">
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

	<? echo form_submit('selectpano', 'Select panorama','id="form_sub2"');
        echo form_close();?>

   <a href="<?=base_url()?>panorama">Click to upload another panoramas</a>

   </div>


    </body>
</html>
