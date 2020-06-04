

<?php 
//require_once 'bootstrap.php';
require_once "pdo.php";
require_once "util.php";

session_start();



if (!isset($_SESSION['name']) || !isset($_SESSION['user_id']) ) {
die("ACCESS DENIED");

}


if (isset($_POST['cancel'] )) {
	header('Location: index.php');
	return;
}



if (isset($_POST['add']))  {
	
  

if (isset($_POST['first_name']) && isset($_POST['last_name'])  && isset($_POST['email'])&& isset($_POST['headline']) && isset($_POST['summary']))
{

   
////////////////////////validating profile////////////////////////
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 ||strlen($_POST['summary']) < 1 ) {
        $_SESSION['error'] = 'All values are required';
        header("Location: add.php");
        return;
    }



	if (strpos($_POST['email'], '@') === false) {
		# code...
        $failure = 'Email address must contain @';
        $_SESSION['error'] = $failure;
        header("Location: add.php");
        return;

	}
////////////////////////vaidating position////////////////
  /*for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      $failure =  "All values are required";
      $_SESSION['error'] = $failure;
        header("Location: add.php");
        return;
      
      }

    if ( ! is_numeric($year) ) {
      $failure= "Position year must be numeric";
      $_SESSION['error'] = $failure;
        header("Location: add.php");
        return;
      }
    }*/

    $failure = validatePos();
    if (is_string($failure)) {
      $_SESSION['error'] = $failure;
        header("Location: add.php");
        return;
      # code...
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
        header("Location: add.php");
        return;
      
      }

    if ( ! is_numeric($year) ) {
      $failure= "Education year must be numeric";
      $_SESSION['error'] = $failure;
        header("Location: add.php");
        return;
      }
    }
  
  
        
  


	

	# code...
/////////////////inserting into profile////////////////		
        $sql = 'INSERT INTO Profile
  (user_id, first_name, last_name, email, headline, summary)
  VALUES ( :uid, :fn, :ln, :em, :he, :su)';
    //echo("<pre>\n".$sql."\n</pre>\n");

   $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
  ':uid' => $_SESSION['user_id'],
  ':fn' => $_POST['first_name'],
  ':ln' => $_POST['last_name'],
  ':em' => $_POST['email'],
  ':he' => $_POST['headline'],
  ':su' => $_POST['summary'])
   );
        

 ////////// inserting into Position//////////////      
        $profile_id = $pdo->lastInsertId();
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
  ':pid' => $profile_id,
  ':rank' => $rank,
  ':year' => $year,
  ':descr' => $desc)
  );

  $rank++;

}

///////////////////inserting into Instituition and Education//////////

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
  ':pid' => $profile_id,
  ':rank' => $rank,
  ':year' => $year,
  ':Iid' => $Institution_id)
  );

  $rank++;

}


/////////////////////////////////////////////////////
$success = 'added';

        $_SESSION['success'] = $success;
        header("Location: index.php");
        return;



    


  }

}




 ?>

 <!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Puppala Pranay's add Page</title>
</head>
<body>
	<h1> Adding Profile for <?php echo $_SESSION['name']; ?></h1>
<div class="container">

<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( isset($_SESSION['error']) ) { 
    // Look closely at the use of single and double quotes
    //error_log("entry failed $failure");
    echo('<p style="color: red;">');
     echo (htmlentities($_SESSION['error']));
     echo ("</p>\n");
     unset($_SESSION['error']);
}
?>


	


<form method="post">
	
<label for="first_name" style="margin-right: 2em;">First Name</label>
<input type="text" name="first_name" id="first_name" size="60"><br/>
<label for="last_name" style="margin-right: 2em;">Last Name</label>
<input type="text" name="last_name" size="60" id="last_name"><br/>
<label for="email" style="margin-right: 3em;">Email</label>
<input type="text" name="email" size="60" id="email"><br/>
<label for="headline" style="margin-right: 1em;">Headline</label>
<input type="text" name="headline" size="60" id="headline"><br/>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea><br/>
</p>
<p>
  Position: <input type="submit" id="addPos" value="+">
</p>
<div id="position_fields">
  
</div>

<p>
  education: <input type="submit" id="addedu" value="+">
</p>
<div id="education_fields">
  
</div>
<p>
<input type="submit" value="Add" name="add" style="margin-top:  2em;">
<input type="submit" name="cancel" value="Cancel" style="margin-top:  2em;">
</p>
</form>

<script id="edu_template">

	 <div id="education%count%" >
	 <p>Year: <input type="text" name="edu_year%count%" value=""/> 
		<input type="button" value="-" onclick="$('#education%count%').remove();return false;" ></p>
	<p>School: <input type="text" size="80" name="edu_school%count%" class="school" value="" /></p>
	</div>

</script>

<script type="text/javascript">
  countPos = 0;
  countEdu=0;
  $(document).ready(function(){
    window.console && console.log('Document ready called');
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


       

});

</script>


</div>