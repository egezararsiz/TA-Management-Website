<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [4,5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    // define all fields to add to the database
    $course_number = $_POST['courseNumber'];
    $ta_email = $_POST['ta'];
    $course_term = $_POST['term'];
    $course_year = $_POST['year'];
    $assigned_hours = $_POST['hours'];

    //check for empty inputs
    if($course_number == "" || $assigned_hours == "" || ($_POST['assign'] != "assign" && $_POST['remove'] != "remove")){
        echo "<div class='error'>Please enter all required fields.</div>";
        $conn->close();
        die();
    }

    //check if course exists in given term + year
    $result = $conn->query("SELECT * FROM course WHERE courseNumber ='".$course_number."' and term = '".$course_term."' and year = '".$course_year."'");
    $course = $result -> fetchArray(SQLITE3_ASSOC);
    
    //if not
    if (!$course) {
        echo "<div class='error'>The course with these parameters do not exist. </div>";
        $conn->close();
        die();
    } else {
        
        //check if ta is working during that term + year
        $ta_query = $conn->prepare("SELECT * FROM taadmin WHERE email = :ta_email and term = :term and year = :year");
        $ta_query->bindValue(':ta_email', $ta_email, SQLITE3_TEXT);
        $ta_query->bindValue(':year', $course_year, SQLITE3_TEXT);
        $ta_query->bindValue(':term', $course_term, SQLITE3_TEXT);
        $binary_result = $ta_query -> execute();
        $ta_info = $binary_result -> fetchArray(SQLITE3_ASSOC);
        
        if(!$ta_info){
            echo "<div class='error'>TA is not working in the year and term selected.</div>";
            $conn->close();
            die();
        }

        //if yes, start calculating hours
        $current_hours = $ta_info["assigned_hours"];
        $max_hours = $ta_info["max_hours"];
        $int_curr = intval($current_hours);
        $int_assigned = intval($assigned_hours);
        $int_max = intval($max_hours);

        //variable to distinguish if TA is already in course
        $is_in_course = 0;


        $query = $conn -> prepare("SELECT * FROM ta_courses WHERE ta = :email and course = :course and term = :term and year = :year");
        $query -> bindValue(':email', $ta_email, SQLITE3_TEXT);
        $query -> bindValue(':course', $course_number, SQLITE3_TEXT);
        $query -> bindValue(':term', $course_term, SQLITE3_TEXT);
        $query -> bindValue(':year', $course_year, SQLITE3_TEXT);
        $bin_res = $query -> execute();
        $res = $bin_res -> fetchArray(SQLITE3_ASSOC);
        if($res){
            $is_in_course = 1;
        }

        //if add is selected
        if($_POST['assign'] == "assign"){
            $future_hours = $int_curr + $int_assigned;
            //check if max is exceeded
            if($future_hours > $int_max){
                echo "<div class='error'>Workload is more than maximum hours.</div>";
                $conn->close();
                die();
            }

            //if not already ta'in for that course
            if(!$is_in_course){

                //insert to ta_course table
                $sql = $conn->prepare("INSERT INTO ta_courses (ta, course, year, term) VALUES (:ta, :course, :year, :term)");
                $sql->bindValue(':ta', $ta_email, SQLITE3_TEXT);
                $sql->bindValue(':course', $course_number, SQLITE3_TEXT);
                $sql->bindValue(':year', $course_year, SQLITE3_TEXT);
                $sql->bindValue(':term', $course_term, SQLITE3_TEXT);
                $res = $sql->execute();
                
                //insert to tasheet table
                $sql = $conn->prepare("INSERT INTO tasheet(name, hours, year, course, email, term, year) VALUES (:name, :hours, :year, :course, :email, :term, :year)");
                $sql->bindValue(':name', $ta_info["name"], SQLITE3_TEXT);
                $sql->bindValue(':hours', $assigned_hours, SQLITE3_TEXT);
                $sql->bindValue(':course', $course_number, SQLITE3_TEXT);
                $sql->bindValue(':email', $ta_email, SQLITE3_TEXT);
                $sql->bindValue(':year', $course_year, SQLITE3_TEXT);
                $sql->bindValue(':term', $course_term, SQLITE3_TEXT);
                $sql->execute();  
            }
            //if already ta'ing for that course
            else{
                //get current working hour
                $sql = $conn -> prepare("SELECT hours FROM tasheet WHERE email = :email and year = :year and term = :term and course = :course");
                $sql->bindValue(':course', $course_number, SQLITE3_TEXT);
                $sql->bindValue(':email', $ta_email, SQLITE3_TEXT);
                $sql->bindValue(':year', $course_year, SQLITE3_TEXT);
                $sql->bindValue(':term', $course_term, SQLITE3_TEXT);
                $success = $sql -> execute();
                $course_hours = $success -> fetchArray(SQLITE3_ASSOC);

                //calculate new hour
                $course_hours = intval($course_hours);
                $new_course_hours = $course_hours + $assigned_hours;

                //update the hour
                $sql = $conn -> prepare("UPDATE tasheet SET hours = :newhours WHERE email = :email and year = :year and term = :term and course = :course");
                $sql->bindValue(':course', $course_number, SQLITE3_TEXT);
                $sql->bindValue(':email', $ta_email, SQLITE3_TEXT);
                $sql->bindValue(':year', $course_year, SQLITE3_TEXT);
                $sql->bindValue(':term', $course_term, SQLITE3_TEXT);
                $sql->bindValue(':newhours', $new_course_hours, SQLITE3_TEXT); 
                $sql->execute(); 

            }

            //in any case, after successful add always update the admin (overall) hour
            $sql = $conn->prepare("UPDATE taadmin SET assigned_hours = :newhours WHERE email = :email and term = :term and year = :year");
            $sql->bindValue(':newhours', $future_hours, SQLITE3_TEXT);
            $sql->bindValue(':email', $ta_email, SQLITE3_TEXT);
            $sql->bindValue(':year', $course_year, SQLITE3_TEXT);
            $sql->bindValue(':term', $course_term, SQLITE3_TEXT);
            $sql->execute();

            echo "<div class='success'>TA assigned successfully!</div>";
        }
        //if removal was selected
        else{
            $future_hours = $int_curr - $int_assigned;
            //if not in course, impossible to remove
            if(!$is_in_course){
                echo "<div class='error'>TA is not in this course. </div>";
                $conn->close();
                die();
            }
            //ta hours cannot go below 0
            if($future_hours < 0){
                echo "<div class='error'>Workload cannot be below 0.</div>";
                $conn->close();
                die();
            }
            //get the hours working for the course
            $sql = $conn -> prepare("SELECT hours FROM tasheet WHERE email = :email and year = :year and term = :term and course = :course");
            $sql->bindValue(':course', $course_number, SQLITE3_TEXT);
            $sql->bindValue(':email', $ta_email, SQLITE3_TEXT);
            $sql->bindValue(':year', $course_year, SQLITE3_TEXT);
            $sql->bindValue(':term', $course_term, SQLITE3_TEXT);
            $success = $sql -> execute();
            $course_hours = $success -> fetchArray(SQLITE3_ASSOC);
            $course_hours = intval($course_hours["hours"]);
            $new_course_hours = $course_hours - $assigned_hours; 

            //if hours go below 0, don't allow
            if($new_course_hours < 0){
                echo "<div class='error'>Workload cannot be below 0.</div>";
                $conn->close();
                die();
            }
            //if hours become 0, remove from course and sheet
            else if($new_course_hours == 0){
                //remove from ta_course
                $sql = $conn->prepare("DELETE FROM ta_courses WHERE ta = :ta and course = :course and year = :year and term = :term");
                $sql->bindValue(':ta', $ta_email, SQLITE3_TEXT);
                $sql->bindValue(':course', $course_number, SQLITE3_TEXT);
                $sql->bindValue(':year', $course_year, SQLITE3_TEXT);
                $sql->bindValue(':term', $course_term, SQLITE3_TEXT);
                $res = $sql->execute();
                
                //remove from ta_sheet
                $sql = $conn->prepare("DELETE FROM tasheet WHERE email = :email and course = :course and year = :year and term = :term");
                $sql->bindValue(':course', $course_number, SQLITE3_TEXT);
                $sql->bindValue(':email', $ta_email, SQLITE3_TEXT);
                $sql->bindValue(':year', $course_year, SQLITE3_TEXT);
                $sql->bindValue(':term', $course_term, SQLITE3_TEXT);
                $sql->execute(); 
            }   
            else{
                //if new hours are still above 0, update hours for the cours
                $sql = $conn -> prepare("UPDATE tasheet SET hours = :newhours WHERE email = :email and year = :year and term = :term and course = :course");
                $sql->bindValue(':course', $course_number, SQLITE3_TEXT);
                $sql->bindValue(':email', $ta_email, SQLITE3_TEXT);
                $sql->bindValue(':year', $course_year, SQLITE3_TEXT);
                $sql->bindValue(':term', $course_term, SQLITE3_TEXT);
                $sql->bindValue(':newhours', $new_course_hours, SQLITE3_TEXT); 
                $sql->execute();
            }

            //in any case, update general(admin) hour count
            $sql = $conn->prepare("UPDATE taadmin SET assigned_hours = :newhours WHERE email = :email and term = :term and year = :year");
            $sql->bindValue(':newhours', $future_hours, SQLITE3_TEXT);
            $sql->bindValue(':email', $ta_email, SQLITE3_TEXT);
            $sql->bindValue(':year', $course_year, SQLITE3_TEXT);
            $sql->bindValue(':term', $course_term, SQLITE3_TEXT);
            $sql->execute(); 
            
            echo "<div class='success'>TA reassigned successfully!</div>";
        }
    }
} 
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}
?>