<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    //define fields
    $old_email = $_POST['old_email'];
    $new_email = $_POST['new_email'];

    //empty email check
    if($new_email == ""){
        echo "<div class='error'>Please enter an email address.</div>";
        $conn->close();
        die();
    }

    //check if new email is in db
    $result = $conn->query("SELECT COUNT(*) FROM user WHERE email='".$new_email."'");
    $exists = $result -> fetchArray(SQLITE3_ASSOC);

    if ($exists["COUNT(*)"] != 0){
        echo "<div class='error'>This email already exists. Please try another one.</div>";
        $conn->close();
        die();
    }

    //if email's valid, update possible tables
    $date = date('Y-m-d H:i:s');
    $query1 = "UPDATE user SET email='".$new_email."', updatedAt='".$date."' WHERE email='".$old_email."'";
    $query2 = "UPDATE user_usertype SET userId='".$new_email."' WHERE userId='".$old_email."'";
    $query3 = "UPDATE student_courses SET student='".$new_email."' WHERE student ='".$old_email."'";
    $query4 = "UPDATE ta_courses SET ta='".$new_email."' WHERE ta ='".$old_email."'";
    $query5 = "UPDATE tasheet SET email='".$new_email."' WHERE email ='".$old_email."'";
    $query6 = "UPDATE course SET courseInstructor ='".$new_email."' WHERE courseInstructor ='".$old_email."'";
    $query7 = "UPDATE professor SET professor='".$new_email."' WHERE professor ='".$old_email."'";
    $query8 = "UPDATE sessions SET email='".$new_email."' WHERE email ='".$old_email."'";
    $query_array = array($query1,$query2,$query3,$query4,$query5,$query6,$query7,$query8);
    foreach ($query_array as $query){
        $succ = $conn->query($query);
    }

    
    if($succ){
        echo "<div class='success'>Email successfully changed.</div>";
    }
    else{
        echo "<div class='error'>Email failed to change.</div>";
    }
}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}

?>