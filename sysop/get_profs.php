<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    $db = "sqlite:../database.sqlite";
    $conn = new PDO($db) or die("cannot open the database");
    
    //display headers
    echo '<table>';
    echo'<tr>
        <th class="red-label">Email</th>
        <th class="red-label">First name</th>
        <th class="red-label">Last name</th>
        <th class="red-label">Faculty</th>
        <th class="red-label">Department</th>
        <th class="red-label">Courses</th>
        </tr>';

    //for each professor entry
    foreach ($conn->query("SELECT * FROM professor") as $prof) {
        $courseList = "";
        
        //for firstname, lastname etc. we need user entry of professor
        $query = "SELECT * FROM user WHERE email='".$prof['professor']."'";
        $res = $conn->query($query);
        $user = $res->fetch(PDO::FETCH_ASSOC);

        //to display courses
        $query = "SELECT * FROM course WHERE courseInstructor='".$prof['professor']."'";
        foreach ($conn->query($query) as $course){
            $courseList = $courseList.$course["courseNumber"].", ";
        }
        $courseList = rtrim($courseList, ', ');

        //display
        echo
        '<tr>
            <td>'. $prof['professor'] .'</td>
            <td>'. $user['firstName'] .'</td>
            <td>'. $user['lastName'] .'</td>
            <td>'. $prof['faculty'] .'</td>
            <td>'. $prof['department'] .'</td>
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