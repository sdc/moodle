
<?php
	
	/******************************* Contacts *************************************/
	
	$pluginname = 'theme_roshni';
  $contacts = 'contacts';
  $contact = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$contacts.'"');
  if($contact) {
    $fcontact = json_decode($contact->value,true);
  } else {
    $fcontact = '';
  }
  
  if(!empty($fcontact)) {
  	$fcontactArray = array();
  	
		foreach ($fcontact as $fcontactkey => $fcontactval) {
			foreach($fcontactval as $fcontactvalkey => $fcontactvalValue) {
				$fcontactArray[$fcontactvalkey][$fcontactkey] = $fcontactvalValue;
			} 
		}
		?>
		<div class="contact-items">
		<?php
		foreach($fcontactArray as $fcontactArraydetails) {
			if(!empty($fcontactArraydetails["contacticon"]) && !empty($fcontactArraydetails["contactdetails"])) {
				?>
				<div class="contact-item">
					<i class="fa <?php echo $fcontactArraydetails["contacticon"]; ?> fa-2x" style="margin:0px 0px 0px 10px;"></i>
					<p><?php echo $fcontactArraydetails["contactdetails"]; ?></p>
				</div>
				<?php
			} else { ?>
				<div class="contact-items">
					<div class="contact-item">
						<i><img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/css/img/i-label.png" alt=""></i>
						<p>Kolkata, India</p>
					</div>
					<div class="contact-item">
						<i><img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/css/img/i-email.png" alt=""></i>
						<p>roshni@dualcube.com</p>
					</div>
					<div class="contact-item">
						<i><img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/css/img/i-phone.png" alt=""></i>
						<p>+ 91 33 64578322</p>
					</div>
				</div>
				<?php
			}
		}		
		?>
		</div>
		
		<?php 
	} else { ?>
		<div class="contact-items">
			<div class="contact-item">
				<i><img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/css/img/i-label.png" alt=""></i>
				<p>Kolkata, India</p>
			</div>
			<div class="contact-item">
				<i><img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/css/img/i-email.png" alt=""></i>
				<p>roshni@dualcube.com</p>
			</div>
			<div class="contact-item">
				<i><img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/css/img/i-phone.png" alt=""></i>
				<p>+ 91 33 64578322</p>
			</div>
		</div>
	<?php } ?>
</div><!-- END of .contacts -->
