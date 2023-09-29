<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    //define all fields to add to the database
    $instructor_email = $_POST['professor'];
    $faculty = $_POST['faculty'];
    $department = $_POST['department'];
    $course_number = $_POST['course'];

    // define all fields to add to the database
    if($instructor_email == "" || $course_number == "" || $faculty == "null" || $department == "null"){
        echo "<div class='error'>Please enter all required fields.</div>";
        $conn->close();
        die();
    }

    //query to check for prof
    $sql = $conn->prepare("SELECT * FROM professor WHERE professor = :prof");
    $sql->bindValue(':prof', $instructor_email, SQLITE3_TEXT);
    $result = $sql->execute();
    $user = $result -> fetchArray(SQLITE3_ASSOC);

    //if prof exists already
    if ($user) {
        echo "<div class='error'>The Professor already exists in Professors.</div>";
        $conn->close();
        die();
    } else {
        //if not in Professors yet, check if email belongs to a user who is a prof
        $result = $conn->query("SELECT * FROM user_usertype WHERE userID ='".$instructor_email."' and userTypeID = 2");
        $prof = $result -> fetchArray(SQLITE3_ASSOC);
        if(!$prof){
            echo "<div class='error'>This email doesn't exist or doesn't belong to a user of type Professor.</div>";
            $conn->close();
            die();
        }

        //check for an available course
        $result = $conn->query("SELECT * FROM course WHERE courseNumber ='".$course_number."' and courseInstructor = 'TBD'");
        $course_available = $result -> fetchArray(SQLITE3_ASSOC);
        
        //course not available
        if(!$course_available){
            echo "<div class='error'>This course doesn't exist or is assigned to another professor.</div>";
            $conn->close();
            die();
        }

        //add prof to the course when available (Professors table)
        $sql = $conn->prepare("INSERT INTO professor (professor, faculty, department, course) VALUES (:prof, :fac, :dep, :crnum)");
        $sql->bindValue(':prof', $instructor_email, SQLITE3_TEXT);
        $sql->bindValue(':fac', $faculty, SQLITE3_TEXT);
        $sql->bindValue(':dep', $department, SQLITE3_TEXT);
        $sql->bindValue(':crnum', $course_number, SQLITE3_TEXT);
        $result = $sql->execute();


        if($result){
            //update course table with prof's info
            $sql = $conn->prepare("UPDATE course SET courseInstructor = :prof WHERE courseNumber = :crnum AND courseInstructor = 'TBD'");
            $sql->bindValue(':prof', $instructor_email, SQLITE3_TEXT);
            $sql->bindValue(':crnum', $course_number, SQLITE3_TEXT);
            $result = $sql->execute();


            if($result){
                echo "<div class='success'>Professor successfully added to course!</div>";
                $conn->close();
                die();
            }
            else{
                echo "<div class='error'>An error occured while updating assigned course for this professor.</div>";
                $conn->close();
                die();
            }
        }
        else{
            //error
            echo "<div class='error'>An error occured while adding professor.</div>";
            $conn->close();
            die();
        }
    }
}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}


?>