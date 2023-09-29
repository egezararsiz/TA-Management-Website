<?php
include 'convert_account.php';
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
  $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

  //declare fields
  $email = $_POST['email'];
  $usertype_str=$_POST['data'];
  $usertype_array = explode(",",$usertype_str);

  //delete previous usertypes
  $query = "DELETE FROM user_usertype WHERE userId='".$email."'";
  $successful = $conn->query($query);

  if($successful){
    foreach($usertype_array as $value){
      //inser new ones
      $query = "INSERT INTO user_usertype (userId,userTypeId) VALUES ('".$email."', '".$value."')";
      $sql = $conn -> query($query);
    }

    //usertypes
    $query = "SELECT usertype.userType FROM usertype INNER JOIN user_usertype 
                ON usertype.idx=user_usertype.userTypeId WHERE user_usertype.userId ='".$email."'";
    $res = $conn->query($query);

    //change update time of user
    $date = date('Y-m-d H:i:s');
    $query = "UPDATE user SET updatedAt='".$date."' WHERE email='".$email."'";
    $conn->query($query);
    $uTypes = [];

    // create comma-separated list of account types
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
      $uTypes[] = convertAccountType($row['userType']);
    }
    $userRoles = implode(', ', $uTypes);
    echo "<div class='success'>User Types are updated succesfully.</div>";
    echo "<div class='newusertype'>". $userRoles ."</div>";
  }
  else{
    echo "<div class='error'>Problem occured while User Types are being updated.</div>";
    $conn->close();
    die();
  }
}
else {
  echo '<div class="welcomeMessage">
  <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
          </div>';
}
?>