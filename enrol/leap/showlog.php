<?php

$refreshevery = 300;

$mysqli = new mysqli('localhost', 'root', str_rot13( 'gerohpurg1977' ), 'moodle');
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit(1);
}
$res = $mysqli->query("SELECT *, from_unixtime(timelogged) AS timeactual FROM mdl_block_leap_log ORDER BY id ASC LIMIT 1000;");

?><html>
<head>
  <title>Leap Log</title>
  <meta http-equiv="refresh" content="<?php echo $refreshevery; ?>">
  <style>
  table {border: 1px solid #333; border-collapse: collapse;}
  th, td {border: 1px solid #bbb; padding: 2px 5px;}
  td { text-align: center; font-size: .8em;}
  td.left { text-align: left;}
  th {min-width: 2em;}
  </style>
</head>
<body>
  <h1>Leap Log</h1>
  <p>Refreshing every: <?php echo $refreshevery;?><br>
  Last refreshed: <?php echo date( 'H:i:s a, jS F Y.', time() ); ?></p>
<?php
if ( !$res ) {
  echo '  <p>Sorry, no table to query, or some other error which made the database return nothing.</p>'."\n";
} else if ( $res->num_rows == 0 ) {
  echo '  <p>Sorry, no rows were returned.</p>'."\n";
} else {

?>
  <table>
    <tr>
      <th>#</th>
      <th>type</th>
      <th>content</th>
      <th>time logged</th>
      <th>actual time</th>
    </tr>
<?php

  while ($row = $res->fetch_assoc()) {
    echo '    <tr>'."\n";
    echo '      <td>'.$row['id'].'</td>'."\n";
    echo '      <td>'.$row['type'].'</td>'."\n";
    echo '      <td class="left">'.$row['content'].'</td>'."\n";
    echo '      <td>'.$row['timelogged'].'</td>'."\n";
    echo '      <td>'.$row['timeactual'].'</td>'."\n";
    echo '    <tr>'."\n";
  }

?>
  </table>
<?php

}

?>
</body>
</html>
