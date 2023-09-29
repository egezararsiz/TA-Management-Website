<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    //email
    $email = $_POST['email'];

    //check if empty
    if($email == ""){
        echo "<div class='error'>Please enter an email address.</div>";
        $conn->close();
        die();
    }

    //if user exists
    $userquery = "SELECT COUNT(*) FROM user where email='".$email."'";
    $result = $conn->query($userquery);
    $result = $result -> fetchArray(SQLITE3_ASSOC);
    if ($result["COUNT(*)"] == 0) {
        echo "<div class='error'>The user does not exist.</div>";
        $conn->close();
        die();
    }

    //have to delete from possible tables
    $query1 = "DELETE FROM user_usertype WHERE userId='".$email."'";
    $query2 = "DELETE FROM user WHERE email ='".$email."'";
    $query3 = "DELETE FROM student_courses WHERE student ='".$email."'";
    $query4 = "DELETE FROM ta_courses WHERE ta ='".$email."'";
    $query5 = "DELETE FROM tasheet WHERE email ='".$email."'";
    $query6 = "UPDATE course SET courseInstructor = 'TBD' WHERE courseInstructor ='".$email."'";
    $query7 = "DELETE FROM professor WHERE professor ='".$email."'";
    $query8 = "DELETE FROM sessions WHERE email ='".$email."'";
    $query_array = array($query1,$query2,$query3,$query4,$query5,$query6,$query7,$query8);
    foreach ($query_array as $query){
        $conn->query($query);
    }

    $result = $conn->query($userquery);
    $result = $result -> fetchArray(SQLITE3_ASSOC);
    //check if removal was successful
    if ($result["COUNT(*)"] != 0) {
        echo "<div class='error'>Problem occured while deleting account.</div>";
        $conn->close();
        die();
    }else{
        echo "<div class='success'>Account removed successfully!</div>";
    }
}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}
?>