<?php
session_start();
require_once "pdo.php";
require_once "util.php";
flashMessages();
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}
$salt = 'XyZzy12*_';
if ( isset($_POST['email']) && isset($_POST['pass'])  ) {

        if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
            $_SESSION['error'] = "Email and Password are required";
            header("Location: login.php");
            return;
        }
        else   if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){      
            // check if e-mail address is well-formed
            $_SESSION['error'] = "Email must have an at-sign (@)";
            header("Location: login.php");
            return;
          }
          
           
         else {
            $check = hash('md5', $salt.$_POST['pass']);
            $stmt = $pdo->prepare('SELECT user_id, name FROM users
            WHERE email = :em AND password = :pw');
            $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $row !== false ) {
                $_SESSION['name'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['success']="Logged In";
                header("Location: index.php");
                return;
            } else {
                $_SESSION['error'] = "Incorrect password";
                header("Location: login.php");
                return;
            }
        }
}

?>
<!DOCTYPE html>
<html>
<head>
<title> Daniel Vásquez</title>
</head>
<body>
<div class="container">
<p>Please Login</p>
<script>
function doValidate() {

    console.log('Validating...');
    try {
        pw = document.getElementById('id_1').value;
        em = document.getElementById('id_2').value;
        console.log("Validating pw="+pw);
        if (pw == null || pw == "") {
        alert("Both fields must be filled out");
        return false;
        }
        console.log("Validating email="+em);
        if(em == null || em ==""){
        alert("Email field must be filled out");
        return false;
        }
        
        return true;
        } 
    catch(e) {
        return false;
        }
        return false;
    }
</script>
<form method="post">
<p>Email:
<input type="text" size="40" name="email" id="id_2"></p>
<p>Password:
<input type="password" name="pass" id="id_1">
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>

<p>