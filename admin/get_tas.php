<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [4,5]);
if ($email) {
    $db = "sqlite:../database.sqlite";
    $conn = new PDO($db) or die("cannot open the database");
    
    //display headers
    echo '<table>';
    echo'<tr>
        <th class="red-label">Email</th>
        <th class="red-label">Name</th>
        <th class="red-label">Term</th>
        <th class="red-label">Year</th>
        <th class="red-label">Assigned Hours</th>
        <th class="red-label">Max Hours</th>
        <th class="red-label">Courses</th>
        </tr>';

    //loop through each row
    foreach ($conn->query("SELECT * FROM taadmin") as $ta) {
        $courseList = "";

        //get courses they teach specific to the term they are registered as TA
        $query = "SELECT course FROM ta_courses WHERE ta='".$ta['email']."' and term='".$ta["term"]."' and year='".$ta['year']."'";
        foreach ($conn->query($query) as $course){
            $courseList = $courseList.$course["course"].", ";
        }
        //display
        $courseList = rtrim($courseList, ', ');
        echo
        '<tr>
            <td>'. $ta['email'] .'</td>
            <td>'. $ta['name'] .'</td>
            <td>'. $ta['term'] .'</td>
            <td>'. $ta['year'] .'</td>
            <td>'. $ta['assigned_hours'] .'</td>
            <td>'. $ta['max_hours'] .'</td>
            <td>'. $courseList .'</td>
        </tr>';
    }

    echo '</table>';
}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}

?>