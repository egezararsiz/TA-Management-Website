<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [4,5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    //get all rows from courses
    $result = $conn->query("SELECT * FROM course");

    //display headers
    echo '<table>';
    echo'<tr>
        <th class="red-label">Course Number</th>
        <th class="red-label">Course Semester</th>
        <th class="red-label">Course Year</th>
        <th class="red-label">Course Instructor</th>
        <th class="red-label">Course TAs</th>
        </tr>';

    //queries to get ta name and last name
    $ta_query = "SELECT ta FROM ta_courses WHERE course = :crn and term = :term and year = :year";
    $ta_name_query = "SELECT firstName,lastName FROM user WHERE email = :email";
    
    while ($course = $result->fetchArray(SQLITE3_ASSOC)) {
        $taList = "";

        //if no professor for the course
        if($course['courseInstructor'] == "TBD"){
            echo 
            '<tr>
                <td>'. $course['courseNumber'] .'</td>
                <td>'. $course['term'] .'</td>
                <td>'. $course['year'] .'</td>
                <td> TBD </td>';
        }

        //if there is professor for the course
        else{
            $query = "SELECT * FROM user WHERE email='".$course['courseInstructor']."'";
            $res = $conn->query($query);
            $user = $res->fetchArray(SQLITE3_ASSOC);
            echo 
            '<tr>
                <td>'. $course['courseNumber'] .'</td>
                <td>'. $course['term'] .'</td>
                <td>'. $course['year'] .'</td>
                <td>'. $user['firstName'] . ' ' . $user['lastName'] . '</td>';
        }

        //get the ta first, last name based on each course
        $get_ta = $conn -> prepare($ta_query);
        $get_ta->bindValue(':crn', $course['courseNumber'], SQLITE3_TEXT);
        $get_ta->bindValue(':term', $course['term'], SQLITE3_TEXT);
        $get_ta->bindValue(':year', $course['year'], SQLITE3_TEXT);
        $res = $get_ta -> execute();
        
        while ($ta = $res->fetchArray(SQLITE3_ASSOC)){
            $get_ta_name = $conn -> prepare($ta_name_query);
            $get_ta_name -> bindValue(':email', $ta["ta"], SQLITE3_TEXT);
            $ta_res = $get_ta_name -> execute();
            
            $ta_name = $ta_res->fetchArray(SQLITE3_ASSOC);
            $taList = $taList.$ta_name["firstName"]." ".$ta_name["lastName"].", ";
        }
        $taList = rtrim($taList, ', '); 
        echo "<td>" . $taList . "</td>";
        echo "</tr>";
    }
    echo '</table>';
}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
  }

?>