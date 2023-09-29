<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    //define fields
    $email = $_POST['email'];
    $new_fname = $_POST['new_fname'];

    //empty check
    if($new_fname == ""){
        echo "<div class='error'>Please enter a first name.</div>";
        $conn->close();
        die();
    }

    //fname doesn't have to be unique so directly update
    $date = date('Y-m-d H:i:s');
    $query = "UPDATE user SET firstName='".$new_fname."', updatedAt='".$date."' WHERE email='".$email."'";
    $successful = $conn->query($query);


    if($successful){
        echo "<div class='success'>First Name successfully changed.</div>";
    }
    else{
        echo "<div class='error'>First Name failed to change.</div>";
    }

}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}

?>