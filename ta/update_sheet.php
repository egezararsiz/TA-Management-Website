<?php 

require __DIR__.'/../login/verify.php';

session_start();
$email = verify(session_id(),[2,3,4,5]);
if ($email) {

$db = "sqlite:../database.sqlite"; // Change accordingly
$conn = new PDO($db) or die("cannot open the database");


$sth = $conn->prepare("UPDATE tasheet SET ".$_POST['toChange']."='".$_POST['new']."' WHERE course='".$_POST['course']."' and email='".$_POST['email']."' and term='".$_POST['term']."' and year='".$_POST['year']."'");
$sth->execute();

}
?>