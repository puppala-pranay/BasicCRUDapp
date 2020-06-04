<?php

session_start();

?>
<html>
<head>
    <title>Puppala Pranay</title>
</head>
<body>


<h1> Puppala Pranay's Resume Registry </h1>

<?php

if(!isset($_SESSION['name'])){
$str1 = '<p><a href="login.php" style="text-decoration: none;">Please log in</a></p>';

echo $str1;



}
else {


    if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}




echo "<p>";
echo "<a href='add.php'>Add New Entry</a></br>";
echo "<a href='logout.php'>Logout</a></br>";
echo "</p>";



}

require_once "pdo.php";
$stmt = $pdo->query("SELECT first_name,last_name,headline,profile_id FROM profile ");


$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count = count($rows);


  if ($count ==0) {
      # code...
    echo (false);
  }
  else{
    echo "<table>";
echo('<table border="1">'."\n");

    echo "<tr><th>";
    echo('Name');
    echo("</th><th>");
    echo('Headline');
    echo("</th>");
    if (isset($_SESSION['name'])) {
      # code...
      echo "<th>";
      echo "Action";
      echo "</th>";
    }
   echo("</tr>\n");



    foreach ( $rows as $row ) {
        echo "<tr><td>";
        $name = htmlentities($row['first_name'].' '.$row['last_name']);
    echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.$name.'</a>');
    echo("</td><td>");
    echo(htmlentities($row['headline']));
    echo("</td>");
    if (isset($_SESSION['name'])) {
      # code...
      echo "<td>";
     echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
      echo "</td>";
    }
   echo("</tr>\n");


    
    
    }
  echo "</table>";

  }

  ?>

</body>
</html>








