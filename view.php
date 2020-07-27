
<?php
session_start();
require_once "pdo.php";
require_once "util.php";
require_once "head.php";

 
 flashMessages();

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :pri");
$stmt->execute(array(":pri" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

$fn = ($row['first_name']);
$ln = ($row['last_name']);
$em = ($row['email']);
$hl = ($row['headline']);
$sm = ($row['summary']);
$pi = $row['profile_id'];
?>
<html>
<title>Daniel Vásquez</title>
<body>
<div class="container">
<h1>Profile information</h1>
<p>First Name: <?= $fn ?></p>
<p>Last Name: <?= $ln ?></p>
<p>Email: <?= $em ?></p>
<p>Headline: <?= $hl ?></p>
<p>Summary: <?= $sm ?></p>
<p>Positions:
<ul>
<?php 
   $stmt = $pdo->prepare ('SELECT * FROM Position
   WHERE profile_id= :prof ORDER BY rank');
   $stmt->execute(array(
       ':prof' => $_REQUEST['profile_id']
   ));
   $positions = array();
   while($row = $stmt->fetch(PDO::FETCH_ASSOC) ){
       $positions[] = $row;
   }
foreach($positions as $position){
    echo('<li>'.$position['year'].": ".$position['description'].'</li>');
}
?>
</ul></p>
<p>Education:
<ul>
<?php 
 
   $educations= loadEdu($pdo, $_REQUEST['profile_id']);
foreach($educations as $education){
    echo('<li>'.$education['year'].": ".$education['name'].'</li>');
}
?>
</ul></p>


<a href="index.php">Done</a>
</div>
</html>