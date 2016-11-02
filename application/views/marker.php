<!DOCTYPE html">
<html style="width: 100%; height: 100%;">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Topmedellin.com</title>
        <script type="text/javascript" src="<?=base_url()?>application/views/js/jquery-1.5.2.min.js"></script>
	<link href="http://code.google.com/apis/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />
    <style>
		#instr{
		height: 133px;
		width: 359px;
		margin:auto;
		display: inherit;
		position:absolute;
		top:3%;
		right:3%;
		z-index: 1500;
		background-image: url(<?=base_url()?>application/views/img/message.jpg);
		border: 2px solid #10a3d4;
		-moz-border-radius: 13px; /* Firefox */
		-webkit-border-radius: 13px; /* Safari, Chrome */
		-khtml-border-radius: 13px; /* KHTML */
		border-radius: 13px; /* CSS3 */
		behavior: url(/application/views/css/border-radius.htc); /* учим IE border-radius */
		color: #30424e;
		font: bold 12px Arial;
		}

		#home{
		position:absolute;
		top: 51px;
		left: 98px;
		z-index: 1500;
}
}
	</style>
	<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
	<script type="text/javascript">

 
        var entryPanoId = null;
        var mapid = 1;


		function markers_apply()
		{

				var head = panorama.getPov().heading;
				var zoom = panorama.getPov().zoom;
				var tmp = parseFloat(head) + parseFloat(center_head);
				tmp = tmp*(-1);
				// Privogu pvprot golovi 0 - 360
				if (tmp > 0)
					{
						while(tmp >= 360)
							{
								tmp = tmp-360;
							}
					}
				else{
							while(tmp < 0)
							{
								tmp = tmp + 360;
							}
					}



			if (zoom >= 0 && zoom <= 1)
					{
						var scale_x = 180 - 90 * zoom;
					}

				if (zoom >= 1 && zoom <= 2)
					{
						var scale_x = 135 - 45 * zoom;
					}

				if (zoom >= 2 && zoom <= 3)
					{
						var scale_x = 90 - 22.5 * zoom;
					}

				if (zoom > 3 )
					{
						var scale_x = 56.25 - 11.25*zoom;
					}

				for (var n in markers)
				{
					if (typeof markers[n] != "function")
					{
						var a_width = markers[n].obj[0].parentNode.clientWidth;
						var a_height = markers[n].obj[0].parentNode.clientHeight;
						var container1 = markers[n].obj;
						var pano_pix_w = a_width/scale_x;

						//delay korrekciu na center_head


						//var correction = ((tmp) * pano_pix_w);

						var cor_tmp = tmp + parseFloat(markers[n].x_gradus) + scale_x/2;

						if (cor_tmp > 360) cor_tmp = cor_tmp - 360;
						if (cor_tmp < 0) cor_tmp = cor_tmp + 360;


						var correction2 = ((cor_tmp) * pano_pix_w);

						correction2 = correction2;
						container1.css("left", parseInt(correction2));

						//input1.val(tmp);
						//input3.val(correction2);

						var pitch = panorama.getPov().pitch;
						//input2.val(pitch);

						var pano_pix_h = a_height/90.0;

						var pitch_cor = pitch + parseFloat(markers[n].y_gradus);

						container1.css("top", parseInt(pitch_cor*pano_pix_h));

						if (correction2 > a_width || correction2 < 0 || (pitch_cor*pano_pix_h + 50) > a_height || pitch_cor*pano_pix_h < 0)
							{
							container1.css("display", "none");

							}
						else
							{
								container1.css("display", "block");
							}
					}

				}


		}


        function initialize() {


	

            // Set up Street View and initially set it visible. Register the
            // custom panorama provider function. Set the StreetView to display
            // the custom panorama 'reception' which we check for below.
            var panoOptions = {
                pano: '<?=$panorama_id?>',
                visible: true,
                linksControl:true,
				disableDoubleClickZoom: true,
                panoProvider: getCustomPanorama,
				addressControlOptions:
								{
									style: {
											"z-index" : 1001
										   }

								},
				zoomControlOptions: {
									
									}
						


            }
            panorama = new google.maps.StreetViewPanorama(document.getElementById('pan1'),panoOptions);
            google.maps.event.addListener(panorama, 'links_changed', createCustomLinks);

			google.maps.event.addListener(panorama, 'pov_changed', function() {
				
			markers_apply();


		});


        }
        // Return a pano image given the panoID.
        function getCustomPanoramaTileUrl(pano,zoom,tileX,tileY) {
            // Note: robust custom panorama methods would require tiled pano data.
            // Here we're just using a single tile, set to the tile size and equal
            // to the pano "world" size.
            //alert(mapid);

			return '<?=base_url()?>uploads/'+"dir_"+window.CustomPanoramaTileUrlurl+ "/1_"+zoom+"_" + tileX+ "_"+tileY +"_.jpeg";




        }
        // Construct the appropriate StreetViewPanoramaData given
        // the passed pano IDs.
        function getCustomPanorama(pano,zoom,tileX,tileY) {

                $('.mymarkers').remove();

				window.CustomPanoramaTileUrlurl = 0;

                $.post("<?=base_url()?>index.php/panorama/get_url",{ "panorama_id": pano },
                 function(data){
                     //alert(data);
                    window.CustomPanoramaTileUrlurl = data;
                 }, "json");


                 var desct;
                 $.post("<?=base_url()?>index.php/panorama/get_pano_info",{ "panorama_id": pano },
                 function(data){
                     //alert(data);
                    desct = data;
					center_head = data.heading;
					//input1.val(center_head - panorama.getPov().heading);


				// Insert Markers

					markers = {};

					var main_div = $('#main_div');

					for (var n in data.markers)
						{

							var wid = 1;
							var hid = 1;

							var id = data.markers[n].id
							markers[id] = {};
							markers[id].name = data.markers[n].name;
							markers[id].link = data.markers[n].link;
							markers[id].file = data.markers[n].file;
							markers[id].x_gradus = data.markers[n].x_gradus;
							markers[id].y_gradus = data.markers[n].y_gradus;
							markers[id].file_id = data.markers[n].file_id;

							main_div.append('<div class="mymarkers" id="'+id+'marker" style="z-index:1000 ; position: absolute;left:'+parseInt(markers[id].x_gradus * wid ) +'px; top:'+parseInt(markers[id].y_gradus * hid )+'px"><div align="center" style="color: red">'+markers[id].name+'</div><div align="center"><a href="'+markers[id].link+'"><img style="width: 50px; height: 50px;" src="<?= base_url() ?>uploads/markers/'+markers[id].file+'"></a></div></div>')

							markers[id].obj = $("#"+id+"marker");
						}
						
						markers_apply();

                 }, "json");

                    return {
                        location: {
                            pano: pano ,
                            description: desct.name
							
                        },


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
                function get_links(data){
                    for (var arr in data)
                        {

                             links.push({
                            'heading': data[arr].heading -center_head,
                            'description' : data[arr].title ,
                            'pano' : data[arr].panorama_id_to_link
                             });

                        }

                 }

                $.post("<?=base_url()?>index.php/panorama/get_links", { "panorama_id": panoId },
                 get_links, "json");

		
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


	        initialize();

		$("#instr").click(function(){
			$(this).hide('slow');
		});


};
        </script>
    </head>
<body style="width:100%; height: 100%; overflow: hidden; background-color: #4E6CA3">
	<div id="instr"><span class="instr"><?// echo $instructions ?></span></div>
	<div id="home"><a href="http://topmedellin.com/"><img src="<?=base_url()?>application/views/img/iconhome.gif" alt="HOME" title="Home"></a></div>
	<div id="main_div" style="width: 100%; height: 100%; overflow: hidden;" >
	<div id="pan1" style="width: 100%; height: 100%; z-index: 10;"></div>
	</div>



</body>
</html>

