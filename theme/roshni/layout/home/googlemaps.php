<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAjU0EJWnWPMv7oQ-jjS7dYxSPW5CJgpdgO_s4yyMovOaVh_KvvhSfpvagV18eOyDWu7VytS6Bi1CWxw"></script>

<script>
var map = {};
var geocoder = {};



function initialize(map_canvas, index) {

	if (GBrowserIsCompatible()) {

		map[index] = new GMap2($('.map_canvas').get(index));

		

		map[index].setCenter(new GLatLng(37.4419, -122.1419), 1);

		map[index].setUIToDefault();

		geocoder[index] = new GClientGeocoder();

	}

}

  

function showAddress(address, index) {

	if (geocoder[index]) {

		geocoder[index].getLatLng(

			address,

			function(point) {

				if (!point) {

					alert(address + " not found");

				} else {

					map[index].setCenter(point, 15);

					var marker = new GMarker(point, {draggable: true});

					map[index].addOverlay(marker);

					

					GEvent.addListener(marker, "dragend", function() {

						//marker.openInfoWindowHtml(document.createTextNode(address));

					});

					GEvent.addListener(marker, "click", function() {

						marker.openInfoWindowHtml(document.createTextNode(address));

					});

					GEvent.trigger(marker, "click");

				}

			}

		);

	}

}

$(window).load(function () {

	$('.map_canvas').each(function(index) {

			initialize($(this), index); 

			givenaddress = $(".map-id:eq(" + index + ")").val(); 

			showAddress(givenaddress, index); 

	}); 

});

</script>
<div class = "clearfix"></div>
<div class="contact">
	<?php
	/******************************* Google Map *************************************/
	
	$pluginname = 'theme_roshni';
	$place = 'contactus';
	$mapplace = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$place.'"');
	if(!empty($mapplace)) { 
		$map = json_decode($mapplace->value,true);
	} else {
		$map = '';
	}
	$mapArray=array();
	if(!empty($map)) {
		foreach ($map as $key => $mapval) {
			foreach($mapval as $mapvalue) {
				$mapArray[$key] = $mapvalue;
			}
		}
		?>
		<div class="container">
				<?php
				if(!empty($mapArray['mapmhead']) && $mapArray['mapmhead'][0] != NULL) {
					if(str_word_count($mapArray['mapmhead']) == 2) { 
						$lastwordstart = strrpos($mapArray['mapmhead'], ' ') + 1; 
						$lastword = substr($mapArray['mapmhead'], $lastwordstart);
						$zap = '';
						$firststring = str_replace($lastword, $zap, $mapArray['mapmhead']);?>
						<h1 class="h-large"><?php echo $firststring; ?><span><?php echo $lastword?></span></h1>
				<?php } else { ?>
						<h1 class="h-large"><?php echo $mapArray['mapmhead']; ?></h1>
				<?php } ?>
				<?php } else { ?>
					<h1 class="h-large">CONTACT <span>ROSHNI</span></h1>
				<?php } 
				if(!empty($mapArray['mapshead']) && $mapArray['mapshead'][0] != NULL) { ?>
					<h3 class="header-b-2"><?php echo $mapArray['mapshead']; ?></h3>
				<?php } else { ?>	
					<h3 class="header-b-2">A Beautiful Contact Us Block - It Has a Map As Well As Contact Coordinates</h3>
				<?php } ?>
		</div><!-- END of .container -->
		<?php 
		if(!empty($mapArray['place']) && !empty($mapArray['country'])) { ?>
			<div class="map_canvas map-wr" style="max-width:100%; height: 450px;"></div> 
			<input type="hidden" name="show-address" value="<?php echo $mapArray['place']; ?>,<?php echo $mapArray['country']; ?>" class="map-id">
			<?php 
		} else { ?>
			<div class="map_canvas map-wr" style="max-width:100%; height: 450px;"></div> 
			<input type="hidden" name="show-address" value="Kolkata,India" class="map-id">
			<?php	
		}
	} else { ?>
		<div class="container">
			<h1 class="h-large">CONTACT <span>ROSHNI</span></h1>
			<h3 class="header-b-2">A Beautiful Contact Us Block - It Has a Map As Well As Contact Coordinates</h3>
		</div><!-- END of .container -->
		<div class="map_canvas map-wr" style="max-width:100%; height: 450px;"></div> 
		<input type="hidden" name="show-address" value="Kolkata,India" class="map-id">
		<?php 
	} ?>
	
	