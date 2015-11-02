<?php

function getFontAwesomeIconArray(){
   $pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';

   if( ini_get('allow_url_fopen') ) {
     $subject = file_get_contents( 'http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css' );
   } else {
     $subject = file_get_contents_curl( 'http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css' );
   }
   preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);

   $icons = array();
   $icons[''] = '';
   foreach($matches as $match){
     $icons[$match[1]] = $match[1];
   }

   return $icons;
 }
 
function file_get_contents_curl($url) {
  $ch = curl_init();
  
  curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
  
  $data = curl_exec($ch);
  curl_close($ch);
  
  return $data;
}
 
function iconpicker_settings_field($settings, $value = '') {
  $icon_array = getFontAwesomeIconArray();
  $output = '<div class="iconpicker">';
  foreach( $icon_array as $key => $option ) {
    if($value == $key)
      $active = ' active';
    else
      $active = '';
    $output .= '<i class="fa ' . $key . $active . '" data-name="' . $key . '"></i>';
  }
  $output .= '</div>';
  
  $output .= '<input type="hidden" class="' . $settings['class'] . '" name="' . $settings['param_name'] . '" data-field="' . $settings['param_name'] . '" value="' . $value . '" />' . "\n";
  echo $output;
}

?>
