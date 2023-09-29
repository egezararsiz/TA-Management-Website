<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");
    
    //define fields
    $email = $_POST['email'];
    $new_lname = $_POST['new_lname'];

    //empty check
    if($new_lname == ""){
        echo "<div class='error'>Please enter a last name.</div>";
        $conn->close();
        die();
    }

    //update 
    $date = date('Y-m-d H:i:s');
    $query = "UPDATE user SET lastName='".$new_lname."', updatedAt='".$date."' WHERE email='".$email."'";
    $successful = $conn->query($query);

    if($successful){
        echo "<div class='success'>Last Name successfully changed.</div>";
    }
    else{
        echo "<div class='error'>Last Name failed to change.</div>";
    }
}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}
?>