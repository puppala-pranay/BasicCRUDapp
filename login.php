<?php // Do not put any HTML above this line

session_start();

require_once 'pdo.php';

$salt = 'XyZzy12*_';
  // Pw is meow123

  // If we have no POST data

// Check to see if we have some POST data, if we do process it
 if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $failure = "User name and password are required";
        $_SESSION['error'] = $failure;
        header("Location: login.php");
        return;
    }
     else {
           if (strstr($_POST['email'],'@')===false) {
             $failure = 'Email must have an at-sign (@)';
              $_SESSION['error'] = $failure;
              header("Location: login.php");
              return;
             }
           else{


        $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users

        WHERE email = :em AND password = :pw');

        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
 
         $row = $stmt->fetch(PDO::FETCH_ASSOC);

         if ( $row !== false ) {

              $_SESSION['name'] = $row['name'];
              $_SESSION['user_id'] = $row['user_id'];
              // Redirect the browser to index.php
              header("Location: index.php");
              return;}

        else {
            $failure = "Incorrect password or email";
             $_SESSION['error'] = $failure;
             header("Location: login.php");
             return;
        }
    }
 }
}

// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Puppala Pranay's Login Page</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( isset($_SESSION['error']) ) { 
    // Look closely at the use of single and double quotes
    
    error_log("Login fail ".$_SESSION['name']."$failure");
    echo('<p style="color: red;">');
    echo ($_SESSION['error']);
    echo ('</p>');
    unset($_SESSION['error']);
}
?>
<form method="POST">
<label for="email">Email
</label>
<input type="email" name="email" id="email"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<a href="index.php">Cancel</a>
</form>

<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the extension of present file (all lower case) followed by 123. -->
</p>
</div>
</body>


<script type="text/javascript">
    function doValidate() {

console.log('Validating...');

try {
addr = document.getElementById('email').value;
pw = document.getElementById('id_1723').value;

console.log("Validating addr="+addr+" pw="+pw);

if (addr == null || addr == "" || pw == null || pw == "") {

alert("Both fields must be filled out");

return false;

}

if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }

return true;

} catch(e) {

return false;

}

return false;

}
</script>
