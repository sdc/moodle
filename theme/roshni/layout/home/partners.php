<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/homescript.js"></script>
<?php
	$pluginname = 'theme_roshni';
		$aboutsite = 'partners';
		$partners_icons = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$aboutsite.'"');
		if(!empty($partners_icons)) { 
			$partners_icon = json_decode($partners_icons->value,true);
		} else {
			$partners_icon = '';
		}
		if(!empty($partners_icon) && $partners_icon["particon"][0] != null) {

			foreach ($partners_icon as $key => $partners_iconval) {
				foreach($partners_iconval as $keydetails => $partners_iconvalValue) {
					$partnerArray[$keydetails] = $partners_iconvalValue;
				}
			}
			
			$partnerArraycount = count($partnerArray);
			?>
			<div class="clearfix"></div>
			<div class="partners">
				<input type = "hidden" class = "countpart" value = "<?php echo $partnerArraycount; ?>"/>
				<ul id="autoplay-image" class="blocks-1">
					<?php foreach ($partnerArray as $key => $partnerArrayvalue) { ?>
						<li class="clnimg">
							<?php if(!empty($partnerArrayvalue)) { ?>
								<img src="<?php echo $partnerArrayvalue; ?>">
							<?php } else { ?>
								<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/lg_1.png">
							<?php } ?>
						</li> 
					<?php } ?>
				</ul>
			</div>
		<?php } else { ?>
			<div class="clearfix"></div>
			<div class="partners">
				<input type = "hidden" class = "countpart" value = "4"/>
				<ul id="autoplay-image" class="blocks-1">
					<li class="clnimg">
						<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/lg_1.png" alt>
					</li>                                                                                         
					<li class="clnimg">                                                                           
						<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/lg_2.png" alt>
					</li>                                                                                         
					<li class="clnimg">                                                                           
						<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/lg_3.png" alt>
					</li>                                                                                         
					<li class="clnimg">                                                                           
						<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/lg_4.png" alt>
					</li>
				</ul>
			</div> 
		<?php } ?>

