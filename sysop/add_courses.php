<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    // define all fields to add to the database
    $course_number = $_POST['courseNumber'];
    $course_name = $_POST['courseName'];
    $course_description = $_POST['courseDescription'];
    $course_term = $_POST['term'];
    $course_year = $_POST['year'];
    $course_instructor_email = $_POST['instrEmail'];

    //default the value to TBD
    if($course_instructor_email === "null"){
        $course_instructor_email = "TBD";
    }
    else{
        //check correct permissions
        $result = $conn->query("SELECT * FROM professor WHERE professor ='".$course_instructor_email."'");
        $prof = $result -> fetchArray(SQLITE3_ASSOC);
        if(!$prof){
            echo "<div class='error'>Please make sure email belongs to a professor in Professors </div>";
            $conn->close();
            die();
        }
    }

    //check if course exists
    $result = $conn->query("SELECT * FROM course WHERE courseNumber ='".$course_number."' and term = '".$course_term."' and year = '".$course_year."'");
    $course = $result -> fetchArray(SQLITE3_ASSOC);

    if ($course) {
        //if it exists
        echo "<div class='error'>The course with these parameters already exists.</div>";
        $conn->close();
        die();
    } else {
        //if it doesn't exist, add to courses
        $sql = "INSERT INTO course (courseName, courseDesc, term, year, courseNumber, courseInstructor) VALUES ('".$course_name."','".$course_description."','".$course_term."','".$course_year."','".$course_number."','".$course_instructor_email."')";
        $res = $conn -> query($sql);
        if ($res) {
            echo "<div class='success'>Course created successfully!</div>";
        } else {
            echo "<div class='error'>Course creation failed...</div>";
        }
    }
} 
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}
?>