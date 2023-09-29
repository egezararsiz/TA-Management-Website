<?php 
require __DIR__.'/../login/verify.php';

session_start();
$email = verify(session_id(),[1,2,3,4,5]);
if ($email) {

    $db = "sqlite:../database.sqlite"; // Change accordingly
    $conn = new PDO($db) or die("cannot open the database");


    $response = $conn->query("SELECT * FROM taratings WHERE reviewerID = '".$email."' and taID = '".$_POST["taID"]."' and  course = '".$_POST["course"]."'");
    $rating = $response->fetch(PDO::FETCH_ASSOC);
    if ($rating) {
        $sql = $conn->prepare("UPDATE taratings SET `rating`=?,`comment`=?,`year`=?,`term`=? WHERE reviewerID = ? and taID = ? and  course = ?");
        $sql->execute([$_POST['rating'],$_POST["comment"],$_POST["year"], $_POST["term"], $email, $_POST["taID"],$_POST["course"]]);
    }else{
        $sql = $conn->prepare("INSERT INTO taratings (`taID`, `reviewerID`, `rating`, `comment`, `course`, `year`, `term`) VALUES (?,?,?,?,?,?,?)");
        $sql->execute([$_POST["taID"] ,$email ,$_POST['rating'],$_POST["comment"],$_POST["course"],$_POST["year"], $_POST["term"]]);
    }

}
?>