<?php
$googleanalytics = get_config("theme_roshni","ganalytics");
$ganalytics = json_decode($googleanalytics, true);
if($ganalytics != NULL) {
	foreach ($ganalytics as $key => $ganalyticsvalue) {
		if ($key == "trackingcode") {
			echo $ganalyticsvalue[0];
		}
	}
}
?>
