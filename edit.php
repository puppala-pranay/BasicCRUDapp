<?php
require_once 'bootstrap.php';
require_once "pdo.php";
session_start();

if ( ! isset($_GET['profile_id']) ) {
   $_SESSION['error'] = "Missing profile_id";
   header('Location: index.php');
   return;
}

if (isset($_POST['cancel'])) {
    # code...
    header('Location: index.php');
    return;
}

if (isset($_POST['first_name']) && isset($_POST['last_name'])  && isset($_POST['email'])&& isset($_POST['headline']) && isset($_POST['summary'])) {

    // Data validation
  if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 ||strlen($_POST['summary']) < 1 ) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }

    if (strpos($_POST['email'], '@') === false) {
        # code...
        $failure = 'Email address must contain @';
        $_SESSION['error'] = $failure;
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;

    }

////////////validate position entries//////////////
    for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      $failure =  "All fields are required";
      $_SESSION['error'] = $failure;
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
      
      }

    if ( ! is_numeric($year) ) {
      $failure= "Position year must be numeric";
      $_SESSION['error'] = $failure;
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
      }
    }

////////////////////////validating  education ///////////////


for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;

    $year = $_POST['edu_year'.$i];
    $school = $_POST['edu_school'.$i];

    if ( strlen($year) == 0 || strlen($school) == 0 ) {
      $failure =  "All values are required";
      $_SESSION['error'] = $failure;
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
      
      }

    if ( ! is_numeric($year) ) {
      $failure= "Education year must be numeric";
      $_SESSION['error'] = $failure;
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
      }
    }
  
  //////////////////updating profile table..////////////////////

    $sql = "UPDATE Profile SET first_name = :first_name,
            last_name = :last_name, email = :email , headline = :headline, summary = :summary
            WHERE profile_id = :profile_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
  ':last_name' => $_POST['last_name'],
  ':email' => $_POST['email'],
  ':headline' => $_POST['headline'],
  ':summary' => $_POST['summary'],
   ':profile_id' => $_GET['profile_id']
   ));
   
        


///////////deleting existing position tables//////////////
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');

    $stmt->execute(array( ':pid' => $_GET['profile_id']));
/////////////////////deleting existing education tables///////////////////////////
   $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');

    $stmt->execute(array( ':pid' => $_GET['profile_id']));



//////////////////// inserting into Position//////////////      
       
        $rank = 1;
for($i=1; $i<=9; $i++) {
  if ( ! isset($_POST['year'.$i]) ) continue;
  if ( ! isset($_POST['desc'.$i]) ) continue;

  $year = $_POST['year'.$i];
  $desc = $_POST['desc'.$i];
  $stmt = $pdo->prepare('INSERT INTO Position
    (profile_id, rank, year, description)
    VALUES ( :pid, :rank, :year, :descr)');

  $stmt->execute(array(
  ':pid' => $_GET['profile_id'],
  ':rank' => $rank,
  ':year' => $year,
  ':descr' => $desc)
  );

  $rank++;

}

//////////////////////inserting into education and institution//////////
$rank = 1;
for($i=1; $i<=9; $i++) {
  if ( ! isset($_POST['edu_year'.$i]) ) continue;
  if ( ! isset($_POST['edu_school'.$i]) ) continue;

  $year = $_POST['edu_year'.$i];
  $school = $_POST['edu_school'.$i];
  $Institution_id = false;
  $stmt = $pdo->prepare('SELECT * FROM Institution WHERE name = :school');
  $stmt->execute(array(':school' => $school));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row===false) {
    $stmt = $pdo->prepare('INSERT INTO Institution
    (name)
    VALUES (:name)');

    $stmt->execute(array(
    ':name' => $school));
   }else{$Institution_id=$row['institution_id']; }

  if ($Institution_id===false) {
    $Institution_id=$pdo->lastInsertId();
  }


  $stmt = $pdo->prepare('INSERT INTO Education
    (profile_id, rank, year, institution_id)
    VALUES ( :pid, :rank, :year, :Iid)');

  $stmt->execute(array(
  ':pid' => $_GET['profile_id'],
  ':rank' => $rank,
  ':year' => $year,
  ':Iid' => $Institution_id)
  );

  $rank++;

}


////////////////////////////////////////////////////////

 $_SESSION['success'] = 'Record updated';
    header( 'Location: index.php' ) ;
    return;


}

// Guardian: Make sure that user_id is present


$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}








// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$mi = htmlentities($row['headline']);
$su = htmlentities($row['summary']);



?>

    <title>Puppala Pranay</title>
</head>
<body>
<h1>Editing Profile for <?php echo $_SESSION['name'];  ?></h1>
<form method="post" style="margin-left: 2em" >

<p>First Name:
<input type="text" name="first_name" size="60"
value="<?php echo($fn) ?>"
/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"
value="<?php echo($ln) ?>"
/></p>
<p>Email:
<input type="text" name="email" size="30"
value="<?php echo($em) ?>"
/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"
value="<?php echo($mi) ?>"
/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80">
<?php echo($su) ?></textarea>
<p>
<input type="hidden" name="profile_id"
value=<?php echo($_GET['profile_id']) ?>
/><br/>
</p>
<p>
  Position: <input type="submit" id="addPos" value="+">
</p>
<div style="margin: 2em" id="position_fields">
  
</div>

<p>
  education: <input type="submit" id="addedu" value="+">
</p>
<div id="education_fields">
  <?php
//////////////////////////////////////////////////////adding pre submitted values of education///////////////////

   $sql = "SELECT Institution.name , Education.year,Education.rank FROM Profile,Institution,Education WHERE Profile.profile_id = Education.profile_id and Institution.institution_id = Education.institution_id AND Profile.profile_id= :xyz ORDER by Education.rank" ;
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array(":xyz" => $_GET['profile_id']));
   $rows1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
   if ( $rows1 === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

   $countEdu=0;
   foreach ($rows1 as $row1) {
     # code...
    $countEdu++;
   


   echo "<div id=\"education".$countEdu."\" >
   <p>Year: <input type=\"text\" name=\"edu_year".$countEdu."\" value=\"".$row1['year']."\"/> 
    <input type=\"button\" value=\"-\" onclick=\"$('#education".$countEdu."').remove();return false;\" ></p>
  <p>School: <input type=\"text\" size=\"80\" name=\"edu_school".$countEdu."\" class=\"school\" value=\"".$row1['name']."\" /></p>
  </div>";

  echo "<script>$('.school').autocomplete({
            source: \"school.php\" });</script>";

   }

   echo "<script>countEdu = ".$countEdu.";</script>";
   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ?>


</div>
<input type="submit" value="Save" name="save" style="margin-top:  2em;">
<input type="submit" name="cancel" value="Cancel" style="margin-top:  2em;">
</form>
</body>


<!-- template for adding education fields into div -->
<script id="edu_template">

   <div id="education%count%" >
   <p>Year: <input type="text" name="edu_year%count%" value=""/> 
    <input type="button" value="-" onclick="$('#education%count%').remove();return false;" ></p>
  <p>School: <input type="text" size="80" name="edu_school%count%" class="school" value="" /></p>
  </div>

</script>
<!---------------------------------------------------------->



<?php 
///////////////////////////adding pre submited entries for position////////////////////
$sql = "SELECT * FROM Position WHERE profile_id = :xyz ORDER BY rank" ;
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$rows1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ( $rows1 === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

$countPos=1;
 echo "<pre>";
 echo (count($rows1));
 print_r($rows1);
  echo "<script>";
echo "$(document).ready(function(){

    window.console && console.log('Document ready called');";

foreach ($rows1 as $row1) {
  # code...
 
echo "countPos = ".$countPos;

 
  echo "
  $('#position_fields').append(
            '<div id=\"position'+countPos+'\"> \
            <p>Year: <input type=\"text\" name=\"year'+countPos+'\" value="
            .$row1['year']. " /> \
            <input type=\"button\" value=\"-\" \
                onclick=\"$(\'#position'+countPos+'\').remove();return false;\"></p> \
            <textarea name=\"desc'+countPos+'\" rows=\"8\" cols=\"80\">".$row1['description']."</textarea>\
            </div>');
  ";

 $countPos++;

}
  echo "});";
  echo "</script>";
  echo "</pre>";

///////////////////////////////////////////////////////////////////////////////////////




 ?>


<!--script for adding positions and instituitions  on click-->
<script type="text/javascript">
    
  
    $('#addPos').click(function(event){
        console.log('preventing default');
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });


    $('#addedu').click(function(event){
      console.log('preventing default');
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);
        $('#education_fields').append(
            $('#edu_template').html().replace(/%count%/g,countEdu) );


        $('.school').autocomplete({
            source: "school.php" });

    });


</script>
<!-------------------------------------------------------------------------->

