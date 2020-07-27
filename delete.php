<?php
require_once "pdo.php";
require_once "util.php";

session_start();
flashMessages();
if ( ! isset($_SESSION['name']) ) {
    die("Not logged in");
  }
  $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ( $row['user_id']!= $_SESSION['user_id'] ) {
      $_SESSION['error'] = 'This profile does not belong to the user';
      header( 'Location: index.php' ) ;
      return;
  
}

if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM profile WHERE profile_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':id' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT first_name, profile_id FROM profile where profile_id = :id");
$stmt->execute(array(":id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>
<p>Confirm: Deleting <?= ($row['first_name']) ?></p>

<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<input type="submit" value="Delete" name="delete">
<a href="index.php">Cancel</a>
</form>
