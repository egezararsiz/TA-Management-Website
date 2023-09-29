<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [4,5]);
if ($email) {
    $db = "sqlite:../database.sqlite";
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");
    
    //necessary fields
    $ta_email = $_POST["ta"];
    $ta_term = $_POST["term"];
    $ta_year = $_POST["year"];

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

    //check for empty email
    if($ta_email == ""){
        echo "<div class='error'>Please enter an email address. </div>";
        $conn->close();
        die();
    }

    //check for the email in TAs
    $ta = $conn->prepare("SELECT * FROM taadmin WHERE email = :ta_email and year = :year and term = :term");
    $ta -> bindValue(':ta_email', $ta_email, SQLITE3_TEXT);
    $ta -> bindValue(':term', $ta_term, SQLITE3_TEXT);
    $ta -> bindValue(':year', $ta_year, SQLITE3_TEXT);
    $res = $ta -> execute();
    $ta_info = $res -> fetchArray(SQLITE3_ASSOC);

    if(!$ta_info){
        echo "<div class='error'>Email doesn't exists in TAs.</div>";
        $conn->close();
        die();
    }

    //if exists, get the courses the TA is teaching
    $courseList = "";
    $query = $conn -> prepare("SELECT course FROM ta_courses WHERE ta = :ta_email and term = :term and year = :year");
    $query -> bindValue(':ta_email', $ta_email, SQLITE3_TEXT);
    $query -> bindValue(':term', $ta_term, SQLITE3_TEXT);
    $query -> bindValue(':year', $ta_year, SQLITE3_TEXT);
    $res = $query -> execute();
    while ($course = $res -> fetchArray(SQLITE3_ASSOC)){
        $courseList = $courseList.$course["course"].", ";
    }
    $courseList = rtrim($courseList, ', ');

    //display
    echo
    '<tr>
        <td id="ta-table-email">'. $ta_email .'</td>
        <td id="ta-table-name">'. $ta_info['name'] .'</td>
        <td>'. $ta_info['term'] .'</td>
        <td>'. $ta_info['year'] .'</td>
        <td id="ta-table-currhours">'. $ta_info['assigned_hours'] .'</td>
        <td id="ta-table-maxhours">'. $ta_info['max_hours'] .'</td>
        <td>'. $courseList .'</td>
    </tr>';
    echo '</table>';
}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}

?>