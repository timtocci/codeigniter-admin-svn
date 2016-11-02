<!DOCTYPE html">
<html style="width: 100%; height: 100%;">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Overlays</title>
        <script type="text/javascript" src="<?=base_url()?>application/views/js/jquery-1.5.2.min.js"></script>
		<link rel="stylesheet" media="screen" type="text/css" href="<?=base_url()?>application/views/css/colorpicker.css" />
		<script type="text/javascript" src="<?=base_url()?>application/views/js/colorpicker.js"></script>
		<link href="http://code.google.com/apis/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />
        <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
		<script type="text/javascript">

	$(document).ready(function(){

		$('#save').click(db_save);
		select_id = 'zeroo';

		$('#delete').click(db_delete);



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

		var latlng = new google.maps.LatLng(6.257029, -75.563793);
		var myOptions = {
			zoom: 12,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);

        $.post("<?=base_url()?>index.php/overlay/load_all",
				{	"all": "ok"
				},
				update_map,
                "json");



	


		google.maps.event.addListener(map, 'click', function(event) {
		placeMarker(event.latLng);

		});




		$("#generate").click(function()
		{
			regen_overlay();
		});

		$('#colorpickerLine, #colorpickerfillColor').ColorPicker({
				onSubmit: function(hsb, hex, rgb, el) {
				$(el).val(hex);
				$(el).ColorPickerHide();
				regen_overlay();
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			}
		}).bind('keyup', function(){
			$(this).ColorPickerSetColor(this.value);
		});


	
});

	function regen_overlay()
		{
			if ( flightPath_triger != 0)
				{
					flightPath.setMap(null);
				}
			flightPath_triger = 1;
			var flightPlanCoordinates = markers_array;
			if ($("#object_type").val() == 1)
				{

				   flightPath = new google.maps.Polyline({
					path: flightPlanCoordinates,
					strokeColor: $('#colorpickerLine').val(),
					strokeOpacity: 1.0,
					strokeWeight: $('#width').val()
				  });

				  flightPath.setMap(map);
				}
			else
				{

				  flightPath = new google.maps.Polygon({
					paths: flightPlanCoordinates,
					strokeColor: $('#colorpickerLine').val(),
					strokeOpacity: 0.8,
					strokeWeight: $('#width').val(),
					fillColor: $('#colorpickerfillColor').val(),
					fillOpacity: 0.35
				  });
				  flightPath.setMap(map);
				}
		}


	function db_save()
	{
		if (typeof markers_object_array != 'object')
			{
			return;
			}

		var markers_array_post = {};
		var i = 0;
		for (var m in markers_object_array)
			{
				markers_array_post[i] = {};
				markers_array_post[i]['lat']=markers_object_array[m].position.lat();
				markers_array_post[i]['lng']=markers_object_array[m].position.lng();
				markers_array_post[i]['panorama_id']=markers_object_array[m].panorama_id;
				i = i +1;
			}

        $.post("<?=base_url()?>index.php/overlay/save",
				{	"markers_array": markers_array_post,
					"line_color" : $('#colorpickerLine').val(),
					"fill_color" : $('#colorpickerfillColor').val(),
					"link" : $('#link').val(),
					"width": $('#width').val(),
					"link_name": $('#link_name').val(),

					"info" : $('#info').val(),
					"object_type" : $('#object_type').val(),
					"select_id" : window.select_id
				},
                 function(data){

					for(var m in markers_object_array)
						{
							if (typeof markers_object_array[m] != 'function')
								{
									markers_object_array[m].setMap(null);
								}
						}

					flightPath.setMap(null);
					flightPath_triger = 0;
					window.select_id = 'zeroo';
					markers_array = undefined;


					$.post("<?=base_url()?>index.php/overlay/load_all",
					{ "all": data },
					update_map,
					"json");




                 }, 
				 "text"
			 );
	}
		
	function db_delete()
	{
		if (typeof markers_object_array != 'object')
			{
			return;
			}
			
			
		if	(window.select_id != 'zeroo')
			{


				$.ajax({
				  type: 'POST',
				  url: "<?=base_url()?>index.php/overlay/delete",
				  data: { "select_id": window.select_id }
				  });

			}
			
			for(var m in markers_object_array)
						{
							if (typeof markers_object_array[m] != 'function')
								{
									markers_object_array[m].setMap(null);
								}
						}

					flightPath.setMap(null);
					flightPath_triger = 0;
					window.select_id = 'zeroo';
					markers_array = undefined;
		
	
	}






	function update_map(data)
	{

				 a = 10;

				 for (var i=0; i < data.length; i++)
					 {
						 var Latlng_array = new Array();
						 for (var k=0; k < data[i].markers.length; k++)
							 {
								var myLatlng = new google.maps.LatLng(data[i].markers[k].lat, data[i].markers[k].lng);
								myLatlng.panorama_id = data[i].markers[k].panorama_id;
								Latlng_array.push(myLatlng);
							 }

						var flightPlanCoordinates = Latlng_array;
							if ( data[i].object_type == 1)
								{

									var flightPath = new google.maps.Polyline({
									path: flightPlanCoordinates,
									strokeColor: data[i].line_color,
									strokeOpacity: 1.0,
									strokeWeight: data[i].width
									});

								}
							else
								{

								    var flightPath = new google.maps.Polygon({
									paths: flightPlanCoordinates,
									strokeColor: data[i].line_color,
									strokeOpacity: 0.8,
									strokeWeight: data[i].width,
									fillColor: data[i].fill_color,
									fillOpacity: 0.35
									});

								}

							flightPath.line_color = data[i].line_color;
							flightPath.fill_color = data[i].fill_color;
							flightPath.object_type = data[i].object_type;
							flightPath.info = data[i].info;
							flightPath.link = data[i].link;
							flightPath.link_name = data[i].link_name;
							flightPath.width = data[i].width;

							flightPath.select_id = data[i].id;
							flightPath.Latlng_array = Latlng_array;

							flightPath.setMap(map);

							google.maps.event.addListener(flightPath, 'click', function(event)
							{
			
								if 	(typeof markers_array != 'object')
								{
									// create markers object
									

									for(var m in this.Latlng_array)
										{
											placeMarker(this.Latlng_array[m]);
										}

									window.select_id = this.select_id;
									this.setMap(null);

									$('#colorpickerLine').val(this.line_color);
									$('#colorpickerfillColor').val(this.fill_color);
									$('#link').val(this.link);
									$('#info').val(this.info);
									$('#object_type').val(this.object_type);
									$('#link_name').val(this.link_name);
									$('#width').val(this.width);
									regen_overlay();


								}
								else
									{
										if (confirm("Save pervios object shanges") == true)
											{
												db_save();
											}
											else
											{
												if (window.select_id != 'zeroo')
													{
													$.post("<?=base_url()?>index.php/overlay/load_all",
													{ "all": window.select_id },
													  update_map,
													  "json");	
													}

													for(var m in markers_object_array)
													{
													if (typeof markers_object_array[m] != 'function')
														{
														markers_object_array[m].setMap(null);
														}
													}

													window.flightPath.setMap(null);
													flightPath_triger = 0;
													window.select_id = 'zeroo';
													markers_array = undefined;

													for(var m in this.Latlng_array)
													{
														placeMarker(this.Latlng_array[m]);
													}

													window.select_id = this.select_id;
													this.setMap(null);

													$('#colorpickerLine').val(this.line_color);
													$('#colorpickerfillColor').val(this.fill_color);
													$('#link').val(this.link);
													$('#info').val(this.info);
													$('#object_type').val(this.object_type);
													$('#link_name').val(this.link_name);
													$('#width').val(this.width);

													regen_overlay();


											}

									}

								window.select_id = 	this.select_id;

							});

	}
	}


		function placeMarker(location)
		{
			if 	(typeof markers_array != 'object')
				{
					markers_array = new Array();
					ind = 0;
					flightPath_triger = 0;
					markers_object_array = new Array();
				}

			var marker = new google.maps.Marker({
				position: location,
				map: map,
				draggable:true,
				ind: ind,
				panorama_id: location.panorama_id
			});

			markers_array[ind] = marker.position;
			markers_object_array[ind]= marker;

			ind = ind + 1;
//
		google.maps.event.addListener(marker, 'drag', function(event)
		{
			markers_array[this.ind] = this.position;
			if ( flightPath_triger == 0)
			{
			return;
			}
			flightPath.setMap(null);

			flightPath_triger = 1;

			regen_overlay();

		});

		google.maps.event.addListener(marker, 'click', function(event)
		{

		//alert(111);
		var new_val = prompt('Edit pano ID link',this.panorama_id);
		if (new_val !=null && new_val !="")
		  {
		  this.panorama_id = new_val;
		  markers_array[this.ind]
		  }
		}
		);


		//map.setCenter(location);
		}
        </script>
    </head>
<body style="width: 100%; height: 100%; background-color: #4E6CA3">
	<div id="map_canvas" style="width: 100%; height: 50%; position: relative; margin: auto"></div>
	<br>
	<div style="margin-left: 30px">
	<select name="object_type" id="object_type">
		<option selected="selected" value="1">Line</option>
		<option value="2">Polygon</option>
	</select>
	
	<br>
	Line color 
	<input type="text" id="colorpickerLine" value="" size="8">    Fill color <input type="text" id="colorpickerfillColor" value="" size="8">
	<br>

	<br>
	Input line width <input style="width: 100%" type="text" id="width" value="2">
	<br>

	<br>
	Input object link <input style="width: 100%" type="text" id="link" value="http://">
	<br>

	<br>
	Input object link name<input style="width: 100%" type="text" id="link_name" value="Link">
	<br>


	<br>
	Input object info <input type="text" style="width: 100%" id="info" value="">
	<br>
	<br>
	<input type="button" id="generate" value="Generate layer">
	<br>
	<br>
	<input type="button" id="save" value="Save changes">

	<input type="button" id="delete" value="Delete object">

	</div>


</body>
</html>
