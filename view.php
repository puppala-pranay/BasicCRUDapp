
<html>
<head>
<title>Puppala Pranay's Profile View</title>
</head>
<body>
<div class="container">
<h1>Profile information</h1>


<?php 
require_once "pdo.php";
$profile_id = $_GET['profile_id'];
$stmt = $pdo->prepare('SELECT first_name,last_name,email,headline,summary FROM Profile WHERE profile_id = :profile_id ');
$stmt->execute(array( ':profile_id' => $profile_id));
$row = $stmt->fetch(PDO::FETCH_ASSOC);


$sql = "SELECT * FROM Position WHERE profile_id = :xyz ORDER BY rank" ;
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$rows1 = $stmt->fetchAll(PDO::FETCH_ASSOC);




echo ("<ul>
<p>First Name: ".htmlentities($row['first_name'])."</p>
<p>Last Name: ".htmlentities($row['last_name'])."</p>
<p>Email: ".htmlentities($row['email'])."</p>
<p>Headline:<br/>".htmlentities($row['headline'])."</p>
<p>Summary:<br/>".htmlentities($row['summary'])."</p>
");

echo "<p>Position:<br/>";
echo "<ul>";
foreach ($rows1 as $row1) {
	# code...
	echo ("<li>".$row1['year']." : ".$row1['description']."</li>");

}

echo "</ul>";


$sql = "SELECT Institution.name , Education.year,Education.rank FROM Profile,Institution,Education WHERE Profile.profile_id = Education.profile_id and Institution.institution_id = Education.institution_id AND Profile.profile_id= :xyz ORDER by Education.rank" ;
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array(":xyz" => $_GET['profile_id']));
   $rows1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
echo "<p>Education:<br/>";
echo "<ul>";
foreach ($rows1 as $row1) {
	# code...
	echo ("<li>".$row1['year']." : ".$row1['name']."</li>");

}
echo "</ul>";

?>
<a href="index.php" style="display: inline-block; margin-top: 2em;">Done</a>
</div>
</body>
