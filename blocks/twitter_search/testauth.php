<?php

/**
 * Big test for Twitter auth.
 * https://dev.twitter.com/docs/api/1.1/get/search/tweets
 * https://github.com/abraham/twitteroauth
 * http://stackoverflow.com/questions/15220562/twitter-rest-api-1-1-search-without-oauth
 */

require_once('twitteroauth.php');

/**
 * Config options.
 */
$ckey           = 'TNIPByTzhUM9avtcftcYg';
$csecret        = 'GR58VwdTfMJJVdeaPHQr90h6MMjtQh1VjzYrCZpI0Wk';
$atoken         = '69288106-ipACymRHryqrDA2goDMmgHU2H8fDcGuCdbzzatenW';
$atokensecret   = 'i7otHcOVcDTu7DAf69XdzDgk9M0ajDCu6scWpt0AdI';

/**
 * Required parameters.
 */
$searchterm     = '#moodle';
//$searchterm     = '@kevtufc';

/**
 * Optional parameters (defaults I suppose).
 */
$count          = 5;
//$result_type    = 'mixed';
$result_type    = 'recent';
//$result_type    = 'popular';


$connection = new TwitterOAuth($ckey, $csecret, $atoken, $atokensecret);

// Debugging.
//$data = $connection->get('account/verify_credentials');
$data = $connection->get('search/tweets', array('q' => $searchterm, 'count' => $count, 'result_type' => $result_type));

$build = '';
if(isset($data->errors)) {
  foreach ($data->errors as $error) {
    $build .= ' Code '.$error->code.': &quot;'.$error->message.'&quot;.';
  }
  //echo '<p>Sorry, an error occurred. Code '.$data->errors[0]->code.': &quot;'.$data->errors[0]->message.'&quot;</p>';
  echo '<p>Sorry, one or more errors occurred.'.$build.'</p>';
  echo '<hr>';
  die();
}

echo '<p>'.$searchterm.'</p>';

echo '<pre>'; print_r($data); echo '</pre><hr><h1 id="proper">proper stuff</h1>'; 

$build = '';
if(isset($data->statuses)) {
  foreach ($data->statuses as $status) {
    //$build .= ' Code '.$error->code.': &quot;'.$error->message.'&quot;.';
    $build .= 'Name: '.$status->user->name. ' (@'.$status->user->screen_name.'). Tweet: '.$status->text.'<br>';
  }
}

echo $build;