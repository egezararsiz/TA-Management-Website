<?php 

require __DIR__.'/../login/verify.php';

session_start();
$email = verify(session_id(),[2,3,4,5]);
if ($email) {

$db = "sqlite:../database.sqlite"; // Change accordingly
$conn = new PDO($db) or die("cannot open the database");

$time=date("Y-m-d H:i:s");

$query = "SELECT * FROM user WHERE email ='".$email."'";
$response = $conn->query($query);
$user = $response->fetch(PDO::FETCH_ASSOC);
$author = $user['firstName']." ".$user['lastName'];


$sth = $conn->prepare("INSERT INTO announcements ('author', 'date','title', 'message', 'course','term','year') VALUES (?,?,?,?,?,?,?)");
$sth->execute([$author, $time,$_POST['title'], $_POST['message'], $_POST['course'],$_POST['term'],$_POST['year']]);

}
?>