<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    // define all fields to add to the database
    $course_number = $_POST['courseNumber'];
    $course_instructor_email = $_POST['profEmail'];
    $course_term = $_POST['term'];
    $course_year = $_POST['year'];

    //check for empty inputs
    if($instructor_email == "" || $course_number == ""){
        echo "<div class='error'>Please enter all required fields.</div>";
        $conn->close();
        die();
    }
    else{
        //check if Professor
        $result = $conn->query("SELECT * FROM professor WHERE professor ='".$course_instructor_email."'");
        $prof = $result -> fetchArray(SQLITE3_ASSOC);
        if(!$prof){
            echo "<div class='error'>Please make sure email belongs to a professor in Professors </div>";
            $conn->close();
            die();
        }
    }

    //check couse from term and semester
    $result = $conn->query("SELECT * FROM course WHERE courseNumber ='".$course_number."' and term = '".$course_term."' and year = '".$course_year."'");
    $course = $result -> fetchArray(SQLITE3_ASSOC);

    //if doesn't exist
    if (!$course) {
        echo "<div class='error'>The course with these parameters do not exist. </div>";
        $conn->close();
        die();
    } else {
        if($_POST['assign'] === "assign"){
            //if exists, it shouldn't be taught by another prof if we are assigning prof
            if($course['courseInstructor'] === 'TBD'){
                $sql = $conn->prepare("UPDATE course SET courseInstructor = :prof WHERE courseNumber = :crnum and term = :crterm and year = :cryr and courseInstructor = 'TBD'");
                $sql->bindValue(':crnum', $course_number, SQLITE3_TEXT);
                $sql->bindValue(':crterm', $course_term, SQLITE3_TEXT);
                $sql->bindValue(':cryr', $course_year, SQLITE3_TEXT);
                $sql->bindValue(':prof', $course_instructor_email, SQLITE3_TEXT);
                $res = $sql->execute(); 
            }
            else{
                echo "<div class='error'>The course is already assigned to a professor. </div>";
                $conn->close();
                die();
            }
        }
        else{
            //if exists, it shoul be taught by a prof if we are removing prof
            if($course['courseInstructor'] === 'TBD'){
                echo "<div class='error'>The course is not assigned to a professor. </div>";
                $conn->close();
                die();
            }
            else{
                $sql = $conn->prepare("UPDATE course SET courseInstructor = 'TBD' WHERE courseNumber = :crnum and term = :crterm and year = :cryr and courseInstructor = :prof");
                $sql->bindValue(':crnum', $course_number, SQLITE3_TEXT);
                $sql->bindValue(':crterm', $course_term, SQLITE3_TEXT);
                $sql->bindValue(':cryr', $course_year, SQLITE3_TEXT);
                $sql->bindValue(':prof', $course_instructor_email, SQLITE3_TEXT);
                $res = $sql->execute();
            }
        }
        if ($res) {
            echo "<div class='success'>Course edited successfully!</div>";
        } else {
            echo "<div class='error'>Course edit failed...</div>";
        }
    }
} 
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}
?>