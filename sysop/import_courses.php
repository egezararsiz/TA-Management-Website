<?php
include "convert_account.php";
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);

//possible csv types
$csv_mimetypes = array(
    'text/csv',
    'application/csv',
    'text/comma-separated-values',
    'application/excel',
    'application/vnd.ms-excel',
    'application/vnd.msexcel'
);

if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    //if file is provided
    if(isset($_FILES['file'])){

        //if there are no errors
        if($_FILES['file']['error'] == 0){

            //if file is indeed a csv
            if (in_array($_FILES['file']['type'], $csv_mimetypes)){
                $file_content = file($_FILES['file']['tmp_name']);
                foreach($file_content as $row) {
                    //parse the csv
                    $items = explode(",", trim($row));
                    $course_number = $items[4];
                    $course_name = $items[0];
                    $course_description = $items[1];
                    $course_term = $items[2];
                    $course_year = $items[3];
                    $course_instructor_email = $items[5];
                    
                    //if professor exists in Professors
                    $result = $conn->query("SELECT * FROM professor WHERE professor ='".$course_instructor_email."'");
                    $prof = $result -> fetchArray(SQLITE3_ASSOC);
                    
                    if($prof){

                        $result = $conn->query("SELECT * FROM course WHERE courseNumber ='".$course_number."' and term = '".$course_term."' and year = '".$course_year."'");
                        $course = $result -> fetchArray(SQLITE3_ASSOC);

                        //if professor isn't assigned to that course, insert
                        if(!$course){
                            $sql = $conn->prepare("INSERT INTO course (courseName, courseDesc, term, year, courseNumber, courseInstructor) VALUES (:crname, :crdesc, :crterm, :cryr, :crnum, :crprof)");
                            $sql->bindValue(':crname', $course_name, SQLITE3_TEXT);
                            $sql->bindValue(':crdesc', $course_description, SQLITE3_TEXT);
                            $sql->bindValue(':crterm', $course_term, SQLITE3_TEXT);
                            $sql->bindValue(':cryr', $course_year, SQLITE3_TEXT);
                            $sql->bindValue(':crnum', $course_number, SQLITE3_TEXT);
                            $sql->bindValue(':crprof', $course_instructor_email, SQLITE3_TEXT);
                            $result = $sql->execute();

                            //check if every row is already in db
                            if($not_added){
                                $not_added = false;
                            }
                        }   
                    }
                }

                //error/success messages
                if($not_added){
                    echo "<div class='error'>Each row was already in the database. No insertion.</div>";
                }
                else{
                    echo "<div class='success'>Successful.</div>";
                }
            }
            else{
                echo "<div class='error'>Please upload a CSV file...</div>";
            }
        }
        else{
            echo "<div class='error'>There was an error while uploading your file...</div>";
        }
    }     
    else{
        echo "<div class='error'>Please select a file...</div>";
    }    
}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}
?>