<?php // line 1 added to enable color highlight
session_start();
require_once "pdo.php";
require_once "util.php";
require_once "head.php";
flashMessages();
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}
if ( ! isset($_SESSION['name']) ) {
    die("Not logged in");
  }
if (isset($_SESSION['user_id']) && isset($_POST['first_name']) && isset($_POST['last_name']) && 
isset($_POST['email']) && isset($_POST['headline']) &&  isset($_POST['summary'])){
          // Data validation
    $msg = validateProf();
    if(is_string($msg)){
      $_SESSION['error'] = $msg;
      header("Location: add.php" );
      return;
    }
    $msg = validatePos();
    if(is_string($msg)){
      $_SESSION['error'] = $msg;
      header("Location: add.php" );
      return;
    }
    $msg = validateEdu();
    if(is_string($msg)){
      $_SESSION['error'] = $msg;
      header("Location: add.php");
      return;
    }
    
           
    $stmt = $pdo->prepare('INSERT INTO Profile
    (user_id, first_name, last_name, email, headline, summary)
    VALUES ( :uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(array(
    ':uid' => $_SESSION['user_id'],
    ':fn' => $_POST['first_name'],
    ':ln' => $_POST['last_name'],
    ':em' => $_POST['email'],
    ':he' => $_POST['headline'],
    ':su' => $_POST['summary']));
    $profile_id = $pdo->lastInsertId();
    insertPositions($pdo, $profile_id);
    insertEducations($pdo, $profile_id);
    $_SESSION['success']="record added";
    header("Location: index.php");
    return;


}

?>
<html>
<title> Daniel Vásquez</title>
<div class="container">
<h1> Add A New profile entry</h1>
<div class="row row-content">
<div class="row">
<form method="post">
<p> First name:</p>
</p><p><input type="text" name="first_name" size="40"></p>
<p>Last name:</p><p>
<input type="text" name="last_name"></p>
<p> Email:</p><p>
<input type="text" name="email"></p>
<p> Headline:</p>
<p><input type="text" name="headline"></p>
<p> Summary:</p>
<p><textarea name="summary" rows="8" cols="80"></textarea></p>
<p> Education: <input type="submit" id="addEdu" value="+"></p>
<div id="education_fields"></div>
<p> Position: <input type="submit" id="addPos" value="+"></p>
<div id="position_fields"></div>



<input type="submit"  value="Add">
<input type="submit" name="cancel" value="cancel">

</form>
<script>
countEdu=0;
countPos = 0;
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){

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
    $('#addEdu').click(function(event){
      
      event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
      countEdu++;
      window.console && console.log("Adding education "+countEdu);
       var source = $("#edu-template").html();
       $('#education_fields').append(source.replace(/@COUNT@/g, countEdu));
       $('.school').autocomplete({
         source: "school.php"
       });
    });
    
});
</script>
<script id="edu-template" type="text">
 <div id="edu@COUNT@">
 <p>Year: <input type="text" name="edu_year@COUNT@" value=""/>
 <input type="button" value="-" 
                onclick="$('#edu@COUNT@').remove();return false;"><br>
  <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value=""/>
  </p> 
  </div>
</script>
</div>
</div>
</div>