<!DOCTYPE html">
<html style="width: 100%; height: 100%;">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?echo $name?></title>
        <script type="text/javascript" src="<?=base_url()?>application/views/js/jquery-1.5.2.min.js"></script>
	<link href="http://code.google.com/apis/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />
        <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
	<script type="text/javascript">

        var panorama;
        var entryPanoId = null;
        var mapid = 1;
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
                     //alert(data);
                    desct = data;
					window.center_head = data.heading;
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
                            'heading': data[arr].heading -window.center_head,
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
<body style="width: 100%; height: 100%; background-color: #4E6CA3">
	<div id="instr" style="height: 25%; width: 25%; margin:auto; display: inherit; position:absolute; top:3%; right:3%; z-index: 1000; background-color: #DADCFF; border: 1px solid #0063DC "><span class="instr"><? echo $instructions ?></span></div>
<div id="pan1" style="<? echo $pano_style?>"></div></body>
</html>
