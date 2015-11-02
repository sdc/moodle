<?php

include("../../../config.php");
if(isset($_POST) && !empty($_POST)) {
	foreach($_POST as $key => $val) {
		$flag = true;
		if(is_array($val)) {
			foreach($val as $valkey => $valVal) {
				if(!empty($valVal)) {
					$flag = false;
				}
			}
		}
		set_config("$key", json_encode($_POST[$key]), 'theme_roshni');
	}
}

$uploadplbcimage = $_FILES['uploadpl']['name'][0];
if(count($_FILES['uploadpl']['name']) > 0){
  //Loop through each file
  for($i=0; $i<count($_FILES['uploadpl']['name']); $i++) {
    //Get the temp file path
    $tmpFilePath = $_FILES['uploadpl']['tmp_name'][$i];
    //Make sure we have a filepath
    if($tmpFilePath != ""){
      //save the filename
      $shortname = $_FILES['uploadpl']['name'][$i];
      //save the url and the file
      $filePath = $CFG->dirroot.'/theme/'.$CFG->theme."/uploaded/" . date('d-m-Y-H-i-s').'-'.$_FILES['uploadpl']['name'][$i];
      $filepathforstoreindb = $CFG->wwwroot.'/theme/'.$CFG->theme."/uploaded/" . date('d-m-Y-H-i-s').'-'.$uploadplbcimage;
      //Upload the file into the temp dir
      if(move_uploaded_file($tmpFilePath, $filePath)) {
          $files[] = $shortname;
          //insert into db

          if($_POST['backgrondimage'] == NULL) {
            //echo $filepathforstoreindb;
            set_config("backgrondimage", $filepathforstoreindb, 'theme_roshni');
          }
          //use $filepathforstoreindb for the relative url to the file
      }
    }
  }
}

if(count($_FILES['uploadfavicon']['name']) > 0){
  //Loop through each file
  for($i=0; $i<count($_FILES['uploadfavicon']['name']); $i++) {
    //Get the temp file path
    $tmpFilePath = $_FILES['uploadfavicon']['tmp_name'][$i];
    //Make sure we have a filepath
    if($tmpFilePath != ""){
      //save the filename
      $shortname = $_FILES['uploadfavicon']['name'][$i];
      //save the url and the file
      $filePath = $CFG->dirroot.'/theme/'.$CFG->theme."/uploaded/" . date('d-m-Y-H-i-s').'-'.$_FILES['uploadfavicon']['name'][$i];
      $filepathforstoreindb = $CFG->wwwroot.'/theme/'.$CFG->theme."/uploaded/" . date('d-m-Y-H-i-s').'-'.$_FILES['uploadfavicon']['name'][$i];
      //Upload the file into the temp dir
      if(move_uploaded_file($tmpFilePath, $filePath)) {
          $files[] = $shortname;
          //insert into db 
          if($_POST['faviconimg'] == NULL) {
            set_config("faviconimg", $filepathforstoreindb, 'theme_roshni');
          }
          //use $filePath for the relative url to the file
      }
    }
  }
}
if(count($_FILES['uploadlogo']['name']) > 0){
  //Loop through each file
  for($i=0; $i<count($_FILES['uploadlogo']['name']); $i++) {
    //Get the temp file path
    $tmpFilePath = $_FILES['uploadlogo']['tmp_name'][$i];
    //Make sure we have a filepath
    if($tmpFilePath != ""){
      //save the filename
      $shortname = $_FILES['uploadlogo']['name'][$i];
      //save the url and the file
      $filePath = $CFG->dirroot.'/theme/'.$CFG->theme."/uploaded/" . date('d-m-Y-H-i-s').'-'.$_FILES['uploadlogo']['name'][$i];
      $filepathforstoreindb = $CFG->wwwroot.'/theme/'.$CFG->theme."/uploaded/" . date('d-m-Y-H-i-s').'-'.$_FILES['uploadlogo']['name'][$i];
      //Upload the file into the temp dir
      if(move_uploaded_file($tmpFilePath, $filePath)) {
          $files[] = $shortname;
          //insert into db 
          //echo $filepathforstoreindb;
          if($_POST['logoimg'] == "") {
            set_config("logoimg", $filepathforstoreindb, 'theme_roshni');
          }
          //use $filePath for the relative url to the file
      }
    }
  }
}

if(count($_FILES['uploadsb']['name']) > 0){
  //Loop through each file
  for($i=0; $i<count($_FILES['uploadsb']['name']); $i++) {
    //Get the temp file path
    $tmpFilePath = $_FILES['uploadsb']['tmp_name'][$i];
    //Make sure we have a filepath
    if($tmpFilePath != ""){
      //save the filename
      $shortname = $_FILES['uploadsb']['name'][$i];
      //save the url and the file
      $filePath = $CFG->dirroot.'/theme/'.$CFG->theme."/uploaded/" . date('d-m-Y-H-i-s').'-'.$_FILES['uploadsb']['name'][$i];
      $filepathforstoreindb = $CFG->wwwroot.'/theme/'.$CFG->theme."/uploaded/" . date('d-m-Y-H-i-s').'-'.$_FILES['uploadsb']['name'][$i];
      //Upload the file into the temp dir
      if(move_uploaded_file($tmpFilePath, $filePath)) {
          $files[] = $shortname;
          //insert into db 
          if($_POST['avcstripbackgrondimage'] == NULL) {
            set_config("avcstripbackgrondimage", $filepathforstoreindb, 'theme_roshni');
          }
          //use $filepathforstoreindb for the relative url to the file
      }
    }
  }
}

redirect('index.php');
?>
