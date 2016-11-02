<!DOCTYPE html">
<html style="width: 100%; height: 100%;">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Map</title>
        <script type="text/javascript" src="<?=base_url()?>application/views/js/jquery-1.5.2.min.js"></script>
		<link href="http://code.google.com/apis/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />
        <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
		<script type="text/javascript">

	$(document).ready(function(){

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

		var latlng = new google.maps.LatLng(6.22543854155607, -75.56733351589969);
		var myOptions = {
			zoom: 13,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			disableDoubleClickZoom: true
		};
		map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);


    $.post("<?=base_url()?>index.php/overlay/load_all",
				{	"all": "ok"
				},
				update_map,
                "json");

	function update_map(data)
	{



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
									strokeColor: '#' + data[i].line_color,
									strokeOpacity: 0.5,
									strokeWeight: data[i].width
									});

								}
							else
								{

								    var flightPath = new google.maps.Polygon({
									paths: flightPlanCoordinates,
									strokeColor: '#' + data[i].line_color,
									strokeOpacity: 0.8,
									strokeWeight: data[i].width,
									fillColor: '#' + data[i].fill_color,
									fillOpacity: 0.35
									});

								}

							flightPath.info = data[i].info;
							flightPath.link = data[i].link;
							flightPath.link_name = data[i].link_name;
							flightPath.Latlng = Latlng_array[0];
							flightPath.Latlng_array = Latlng_array;

							flightPath.setMap(map);


							google.maps.event.addListener(flightPath, 'click', function(event)
							{


				// Add markers to panorams

							if 	(typeof markers_object_array == 'object')
								{
								for(var m in markers_object_array)
									{
									if (typeof markers_object_array[m] != 'function')
									{
									markers_object_array[m].setMap(null);
									}
									}

								}

							markers_object_array = new Array();

								for(var m in this.Latlng_array)
									{
									if (typeof this.Latlng_array[m] != 'function' && this.Latlng_array[m].panorama_id != -1)
									{
							///////////////////////////////////////

												var marker = new google.maps.Marker({
													position: this.Latlng_array[m],
													map: map,
													draggable:false,
													panorama_id: this.Latlng_array[m].panorama_id
												});

										marker.setMap(map);
										markers_object_array.push(marker);

										google.maps.event.addListener(marker, 'click', function(event)
										{

											window.open ('<?=base_url()?>?panorama_sel=' +this.panorama_id);
											//window.open ('http://topmedellin.com/panosrc/?panorama_sel');


										});


	///////////////////////////////////////////
									}
									}


							/*	var contentString = '<div><a target="_blank" href="'+this.link+'">'+this.link_name+'</a><br>'+this.info+'</div>';

								var infowindow1 = new google.maps.InfoWindow({
									content: contentString,
									position: this.Latlng
								});
								infowindow1.open(map);
							*/
							window.open (this.link);

							});

	}
	}

		});
        </script>
    </head>
<body style="width: 100%; height: 100%; background-color: #4E6CA3">
	<div id="map_canvas" style="width: 100%; height: 100%; position: relative; margin: auto"></div>
</body>
</html>
