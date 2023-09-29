<?php 

require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(),[2,3,4,5]);
if ($email) {

$db = "sqlite:../database.sqlite"; // Change accordingly
$conn = new PDO($db) or die("cannot open the database");
//display each message
foreach($conn->query("SELECT * FROM messages WHERE course='".$_GET['course']."' and year='".$_GET['year']."' and term='".$_GET['term']."' ORDER BY date") as $message){
    echo "
        <h5>".$message['author']." - ".$message['date']."</h5>
        <p>".$message['message']."</p><br>
    ";
}

}

?>