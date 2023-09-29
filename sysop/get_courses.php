<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    //all courses
    $result = $conn->query("SELECT * FROM course");

    //display headers
    echo '<table>';
    echo'<tr>
        <th class="red-label">Course Number</th>
        <th class="red-label">Course Name</th>
        <th class="red-label">Course Description</th>
        <th class="red-label">Course Semester</th>
        <th class="red-label">Course Year</th>
        <th class="red-label">Course Instructor</th>
        </tr>';

    //for each course, create a row and fill
    while ($course = $result->fetchArray(SQLITE3_ASSOC)) {
        //if instructor not assignes / removed
        if($course['courseInstructor'] === "TBD"){
            echo 
            '<tr>
                <td>'. $course['courseNumber'] .'</td>
                <td>'. $course['courseName'] .'</td>
                <td>'. $course['courseDesc'] .'</td>
                <td>'. $course['term'] .'</td>
                <td>'. $course['year'] .'</td>
                <td> TBD </td>
            </tr>';
        }
        //if there is an active instructor
        else{
            $query = "SELECT * FROM user WHERE email='".$course['courseInstructor']."'";
            $res = $conn->query($query);
            $user = $res->fetchArray(SQLITE3_ASSOC);
            echo 
            '<tr>
                <td>'. $course['courseNumber'] .'</td>
                <td>'. $course['courseName'] .'</td>
                <td>'. $course['courseDesc'] .'</td>
                <td>'. $course['term'] .'</td>
                <td>'. $course['year'] .'</td>
                <td>'. $user['firstName'] . ' ' . $user['lastName'] . '</td>
            </tr>';

        }  
    }
    echo '</table>';
}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
  }

?>