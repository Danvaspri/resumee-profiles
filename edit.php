<?php
require_once "pdo.php";
require_once "util.php";
require_once "head.php";
session_start();


if ( ! isset($_SESSION['name']) ) {
    die("Not logged in");
  }
  if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}
// Guardian: Make sure that user_id is present
if ( ! isset($_REQUEST['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
  }  


if (isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline']) && 
     isset($_POST['summary']) && isset($_POST['profile_id'])) {

    // Data validation
    $msg = validateProf();
    if(is_string($msg)){
      $_SESSION['error'] = $msg;
      header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
      return;
    }
    $msg = validatePos();
    if(is_string($msg)){
      $_SESSION['error'] = $msg;
      header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
      return;
    }
    $msg = validateEdu();
    if(is_string($msg)){
      $_SESSION['error'] = $msg;
      header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
      return;
    }
       
  
    $stmt = $pdo->prepare("UPDATE profile SET 
     first_name= :fn, last_name= :ln, email= :em, headline= :hl, summary= :sm WHERE profile_id = :pri");
    $stmt->execute(array(
        ':fn' => htmlentities($_POST['first_name']),
        ':ln' => htmlentities($_POST['last_name']),
         ':em' => htmlentities($_POST['email']),
        ':hl' => htmlentities($_POST['headline']),
        ':sm' => htmlentities($_POST['summary']),
        ':pri' => $_REQUEST['profile_id']));
    
         // Clear out the old position entries
         $stmt = $pdo->prepare('DELETE FROM Position
          WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $_REQUEST['profile_id'] ));
        // Insert the position entries
        insertPositions($pdo, $_REQUEST['profile_id']);
          
        // Clear out the old education entries
        $stmt = $pdo->prepare('DELETE FROM education
        WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $_REQUEST['profile_id'] ));

        insertEducations($pdo, $_REQUEST['profile_id']);

        $_SESSION['success'] = "Record updated";
        header("Location: index.php ");
        return;
                  

}  
$stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row['user_id']!= $_SESSION['user_id'] ) {
    $_SESSION['error'] = 'This profile does not belong to the user';
    header( 'Location: index.php' ) ;
    return;
}  
$fn = $row['first_name'];
$ln = $row['last_name'];
$em = $row['email'];
$hl = $row['headline'];
$sm = $row['summary'];
$pi = $row['profile_id'];
$positions = loadPos($pdo, $_REQUEST['profile_id']);
$schools = loadEdu($pdo, $_REQUEST['profile_id']);
$pos = count($positions);
$edu = count($schools);
?>

<html>
<?= flashMessages(); ?>
<title> Daniel Vásquez</title>
<div class="container">
<h1>Edit profile</h1>
<div class="row row-content">
<div class="row">
<form method="post">
<p> First name:</p>
</p><p><input type="text" name="first_name" value="<?= $fn?>"></p>
<p>Last name:</p><p>
<input type="text" name="last_name" value="<?= $ln?>"></p>
<p> Email:</p><p>
<input type="text" name="email" value="<?= $em?>"></p>
<p> Headline:</p>
<p><input type="text" name="headline" value="<?= $hl?>"></p>
<p> Summary:</p>
<p><textarea name="summary" rows="8" cols="80"><?= $sm?></textarea></p>
<p> Education: <input type="submit" id="addEdu" value="+"></p>
<div id="education_fields">
<?php
foreach($schools as $school){
  echo('<div id="edu'.$school['rank'].'"> 
          <p>Year: <input type="text" name="edu_year'.$school['rank'].'" value="'.$school['year'].'" /> 
          <input type="button" value="-" 
              onclick="$(\'#edu'.$school['rank'].'\').remove();return false;"></p> 
              <p>School: <input type="text" size="80" name="edu_school'.$school['rank'].'" class="school" 
              value="'.$school['name'].'"/>
              </p>  </div>');
}
?></div>
<p> Position: <input type="submit" id="addPos" value="+"></p>
<div id="position_fields">
 <?php 
foreach($positions as $position){
    echo('<div id="position'.$position['rank'].'"> 
            <p>Year: <input type="text" name="year'.$position['rank'].'" value="'.$position['year'].'" /> 
            <input type="button" value="-" 
                onclick="$(\'#position'.$position['rank'].'\').remove();return false;"></p> 
            <textarea name="desc'.$position['rank'].'" rows="8" cols="80">'.$position['description'].'</textarea>
            </div>');
}
 ?>
  </div>
  <div><p><input type="submit" value="Save"/>
  <p><input type="submit" value="Cancel"/>

<input type="hidden" name="profile_id" value="<?= $pi?>"/>
<script>
countPos = <?= $pos?>;
countEdu = <?= $edu?>;
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
        var source = $("#pos-template").html();
       $('#position_fields').append(source.replace(/@COUNT@/g, countPos));
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
    $('.school').autocomplete({
         source: "school.php"
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
<script id="pos-template" type="text">
<div id="position@COUNT@"> 
            <p>Year: <input type="text" name="year@COUNT@" value="" /> 
            <input type="button" value="-" 
                onclick="$('#position@COUNT@').remove();return false;"></p> 
            <textarea name="desc@COUNT@" rows="8" cols="80"></textarea>
</div>
</script>
</form>
</div>
</div>
</div>
