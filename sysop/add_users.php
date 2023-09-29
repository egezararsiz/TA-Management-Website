<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");
    
    // define all fields to add to the database
    $password = $_POST['password'];
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $first_name = $_POST['firstname'];
    $last_name = $_POST['lastname'];
    $student_id = $_POST['studentID'];
    $account_types = $_POST['accounttypes'];
    $account_types = json_decode($account_types, true); // convert JSON to array of account types

    //empty field checks
    if($email == ""){
        echo "<div class='error'>Please enter an email address.</div>";
        $conn->close();
        die();
    }
    if(empty($account_types)){
        echo "<div class='error'>Please select a User Type.</div>";
        $conn->close();
        die();
    }
    //if user is student or ta, check for student id
    if(in_array(1,$account_types) || in_array(3,$account_types)){
        if($student_id === ""){
            echo "<div class='error'>Please type in Student ID.</div>";
            $conn->close();
            die();
        }
        //if student id is not unique
        $result = $conn->query("SELECT * FROM user WHERE id ='".$student_id."'");
        $student = $result -> fetchArray(SQLITE3_ASSOC);
        if($student){
            echo "<div class='error'>This Student ID already exists.</div>";
            $conn->close();
            die();
        }
    }

    //check for existing email
    $result = $conn->query("SELECT * FROM user WHERE email ='".$email."'");
    $user = $result -> fetchArray(SQLITE3_ASSOC);

    if ($user) {
        echo "<div class='error'>The username already exists.</div>";
        $conn->close();
        die();
    } else {
        //if every check is passed, add user to user and usertype tables
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $date = date('Y-m-d H:i:s');

        //students also have id
        if($student_id == ""){
            $query = "INSERT INTO user (firstName, lastName, email, password, createdAt, updatedAt) VALUES ('".$first_name."', '".$last_name."', '".$email."', '".$hashed_pass."', '".$date."', '".$date."')";
        }
        else{
            $query = "INSERT INTO user (firstName, lastName, email, password, createdAt, updatedAt, id) VALUES ('".$first_name."', '".$last_name."', '".$email."', '".$hashed_pass."', '".$date."', '".$date."', '".$student_id."')";
        }
        $sql = $conn -> query($query);
        if ($sql) {
            foreach ($account_types as $account_type) {
                $query = "INSERT INTO user_usertype (userId, userTypeId) VALUES ('".$email."', ".$account_type.")";
                $sql = $conn -> query($query);
            }
        }
    }
    echo "<div class='success'>Account created successfully!</div>";
}
    else {
        echo '<div class="welcomeMessage">
        <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
                </div>';
} 
?>