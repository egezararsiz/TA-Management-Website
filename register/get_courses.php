<?php 
//CURRENT SEMESTER
$semester = "Fall 2022";
$year = "2022";
$term = "Fall";

$db = "sqlite:../database.sqlite"; // Change accordingly
$conn = new PDO($db) or die("cannot open the database");

foreach($conn->query("SELECT * FROM course WHERE year='".$year."' AND term='".$term."'") as $course){
    echo '
    <label class="container">'.$course['courseNumber'].' - '.$course['courseName'].'
    <input type="checkbox" name="ID_TO_REPLACE" value="'.$course['courseNumber'].'">
    <span class="checkmark"></span>
    </label>
    <hr></hr>
    ';
}


?>