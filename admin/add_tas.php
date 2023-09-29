<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [4,5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    //necessary fields
    $ta_email = $_POST['ta'];
    $term = $_POST['term'];
    $year = $_POST['year'];
    $hours = $_POST['hours'];

    //if ta manual add field empty
    if($ta_email == ""){
        echo "<div class='error'>Please enter all required fields.</div>";
        $conn->close();
        die();
    }

    //check if there is a TA for the selected term and year with the given mail addr
    $sql = $conn->prepare("SELECT * FROM taadmin WHERE email = :ta and term = :term and year = :year");
    $sql->bindValue(':ta', $ta_email, SQLITE3_TEXT);
    $sql->bindValue(':term', $term, SQLITE3_TEXT);
    $sql->bindValue(':year', $year, SQLITE3_TEXT);
    $result = $sql->execute();
    $ta = $result -> fetchArray(SQLITE3_ASSOC);

    //if there exists one
    if ($ta) {
        echo "<div class='error'>The TA already exists in TAs for the given Term and Year.</div>";
        $conn->close();
        die();
    } else {
        //if not, check user priveleges
        $result = $conn->query("SELECT * FROM user_usertype WHERE userID ='".$ta_email."' and userTypeID = 3");
        $ta = $result -> fetchArray(SQLITE3_ASSOC);
        if(!$ta){
            echo "<div class='error'>This email doesn't exist or doesn't belong to a user of type TA.</div>";
            $conn->close();
            die();
        }

        //if user's actually a TA
        $ta_info = $conn->prepare("SELECT * FROM user WHERE email = :ta_email");
        $ta_info -> bindValue(':ta_email', $ta_email, SQLITE3_TEXT);
        $result = $ta_info->execute();
        $ta = $result -> fetchArray(SQLITE3_ASSOC);
        $stud_id = $ta['id'];
        $name = $ta["firstName"]." ".$ta["lastName"];

        //insert into admin's view
        $sql = $conn->prepare("INSERT INTO taadmin (email, studentID, name, term, year, assigned_hours, max_hours) VALUES (:email, :sid, :name, :term, :year, :ass_hrs, :max_hrs)");
        $sql -> bindValue(':email', $ta_email, SQLITE3_TEXT);
        $sql -> bindValue(':sid', $stud_id, SQLITE3_TEXT);
        $sql -> bindValue(':name', $name, SQLITE3_TEXT);
        $sql -> bindValue(':term', $term, SQLITE3_TEXT);
        $sql -> bindValue(':year', $year, SQLITE3_TEXT);
        $sql -> bindValue(':ass_hrs', "0", SQLITE3_TEXT);
        $sql -> bindValue(':max_hrs', $hours, SQLITE3_TEXT);
        $result = $sql->execute();

        if($result){
            echo "<div class='success'>TA successfully added!</div>";
            $conn->close();
            die();
        }
        else{
            echo "<div class='error'>An error occured adding this TA.</div>";
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