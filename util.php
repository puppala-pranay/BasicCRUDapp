<?php

function flashMessages(){
  if (isset($_SESSION['error'])) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
  }else if (isset($_SESSION['success'])) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
  }
}

function head($title){
  echo('
  <!DOCTYPE html>
  <html>
  <head>
  <title>Jon Ibarguren\'s '.$title.'</title>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
  
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
  <script src="myFunctions.js"></script>

  </head>
  <body><div class="container">
  ');
  
}

function insertIntoEducation($profile_id, $pdo){
  for ($rank=1; $rank<=9; $rank++){
    if (isset($_POST['edu_year'.$rank])){
      
      $school = $_POST['edu_school'.$rank];
      $stmt = $pdo->prepare('SELECT institution_id FROM institution WHERE name LIKE :name');
      $stmt->execute(array(':name' => $school));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row === false){
        $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
        $stmt->execute(array(':name' => $school));
        $institution_id = $pdo->lastInsertId();
      }else $institution_id = $row['institution_id'];
      
      $stmt = $pdo->prepare('INSERT INTO education (profile_id, institution_id, `rank`, year) VALUES ( :pid, :iid, :rank, :year)');
      $stmt->execute(array(
        ':pid' => $profile_id,
        ':iid' => $institution_id,
        ':rank' => $rank,
        ':year' => $_POST['edu_year'.$rank])
      );
    }   
  } 
}

function insertIntoPosition($profile_id, $pdo){
  for ($rank=1; $rank<=9; $rank++){
    if (isset($_POST['year'.$rank])){
      $stmt = $pdo->prepare('INSERT INTO Position (profile_id, `rank`, year, description) VALUES ( :pid, :rank, :year, :desc)');
      $stmt->execute(array(
        ':pid' => $profile_id,
        ':rank' => $rank,
        ':year' => $_POST['year'.$rank],
        ':desc' => $_POST['desc'.$rank])
      );
    }   
  } 
}

function loadEdu($profile_id, $pdo){
  $stmt = $pdo->prepare('SELECT year, `rank`, name FROM education 
  JOIN institution ON education.institution_id=institution.institution_id
  WHERE profile_id=:profile_id ORDER BY `rank`');
  $stmt->execute(array(':profile_id' => $profile_id));
  $rowEdu = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $rowEdu;
}

function loadPos($profile_id, $pdo){
  $stmt = $pdo->prepare('SELECT year, description, `rank` FROM position WHERE profile_id=:profile_id ORDER BY `rank`');
  $stmt->execute(array(':profile_id' => $profile_id));
  $rowPos = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $rowPos;
}

function validatePos() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) return "All fields are required";
    
    if ( ! is_numeric($year) ) return "Position year must be numeric";
  }
  return true;
}

function validateEdu() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;

    $edu_year = $_POST['edu_year'.$i];
    $edu_school = $_POST['edu_school'.$i];

    if ( strlen($edu_year) == 0 || strlen($edu_school) == 0 ) return "All fields are required";
    
    if ( ! is_numeric($edu_year) ) return "Education year must be numeric";
  }
  return true;
}
