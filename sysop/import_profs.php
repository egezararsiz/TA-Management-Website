<?php
include "convert_account.php";
require __DIR__.'/../login/verify.php';
session_start();

//csv types
$csv_mimetypes = array(
    'text/csv',
    'application/csv',
    'text/comma-separated-values',
    'application/excel',
    'application/vnd.ms-excel',
    'application/vnd.msexcel'
);
$email = verify(session_id(), [5]);
if ($email) {
    $not_added = true;

    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    //if file is provided
    if(isset($_FILES['file'])){

        //if there are no errors
        if($_FILES['file']['error'] == 0){
            $file_content = file($_FILES['file']['tmp_name']);
            
            //if file's a csv
            if (in_array($_FILES['file']['type'], $csv_mimetypes)){
                foreach($file_content as $row) {
                    //parse csv
                    $items = explode(",", trim($row));
                    $instructor_email = $items[0];
                    $faculty = $items[1];
                    $department = $items[2];
                    $course_number = $items[3];
                    
                    //if prof doesn't exist, continue
                    $sql = $conn->prepare("SELECT * FROM professor WHERE professor = :prof");
                    $sql->bindValue(':prof', $instructor_email, SQLITE3_TEXT);
                    $result = $sql->execute();
                    $exists = $result -> fetchArray(SQLITE3_ASSOC);
                    
                    if(!$exists){
                        //if imported prof is indeed a user of type prof
                        $result = $conn->query("SELECT * FROM user_usertype WHERE userID ='".$instructor_email."' and userTypeID = 2");
                        $prof = $result -> fetchArray(SQLITE3_ASSOC);

                        //check if course is available for him
                        $result = $conn->query("SELECT * FROM course WHERE courseNumber ='".$course_number."' and courseInstructor = 'TBD'");
                        $course_available = $result -> fetchArray(SQLITE3_ASSOC);
                        
                        //if everything's okay, insert the prof
                        if($course_available && $prof){
                            $sql = $conn->prepare("INSERT INTO professor (professor, faculty, department, course) VALUES (:prof, :fac, :dep, :crnum)");
                            $sql->bindValue(':prof', $instructor_email, SQLITE3_TEXT);
                            $sql->bindValue(':fac', $faculty, SQLITE3_TEXT);
                            $sql->bindValue(':dep', $department, SQLITE3_TEXT);
                            $sql->bindValue(':crnum', $course_number, SQLITE3_TEXT);
                            $result = $sql->execute();

                            //also update the courses table
                            if($result){
                                $sql = $conn->prepare("UPDATE course SET courseInstructor = :prof WHERE courseNumber = :crnum AND courseInstructor = 'TBD'");
                                $sql->bindValue(':prof', $instructor_email, SQLITE3_TEXT);
                                $sql->bindValue(':crnum', $course_number, SQLITE3_TEXT);
                                $sql->execute();
                            }

                            //check for no insertion
                            if($not_added){
                                $not_added = false;
                            }
                        }
                    }
                }
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