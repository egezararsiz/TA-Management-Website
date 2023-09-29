<?php
include 'convert_account.php';
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    //email for db
    $email = $_POST['email'];

    //empty var
    if($email == ""){
        echo "<div class='error'>Please enter an email address.</div>";
        $conn->close();
        die();
    }

    echo '<table id= "edit-user-table">';
    echo'<tr>
        <th class="red-label">Email</th>
        <th class="red-label">First Name</th>
        <th class="red-label">Last Name</th>
        <th class="red-label">User Type</th>
        </tr>';

    //check if email is valid
    $query = "SELECT firstName, lastName FROM user WHERE email ='".$email."'";
    $res = $conn->query($query);
    $row = $res->fetchArray(SQLITE3_ASSOC);

    $firstname = $row['firstName'];
    $lastname = $row['lastName'];

    if(empty($firstname)  && empty($lastname)){
        echo "<div class='error'>Email doesn't exist.</div>";
        $conn->close();
        die();
    }

    //if valid, also get the usertypes as usual
    $query = "SELECT usertype.userType FROM usertype INNER JOIN user_usertype 
                ON usertype.idx=user_usertype.userTypeId WHERE user_usertype.userId ='".$email."'";
    $res = $conn->query($query);

    $uTypes = [];

    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $uTypes[] = convertAccountType($row['userType']);
    }
    $userRoles = implode(', ', $uTypes);

    //display
    echo 
    '<tr>
        <td id="old_email">'. $email .'</td>
        <td id="old_fname">'. $firstname .'</td>
        <td id="old_lname">'. $lastname .'</td>
        <td id="old_usertype">'. $userRoles .'</td>
    </tr>';


    //edit buttons and all the animations are dependant on this echo
    echo
    '
    <tr>
        <td id="email-edit-td"> 
        <button type="submit" form="emailForm" class="btn btn-light" id="email-edit-button" onclick="editText(&quot;email-edit-button&quot;)" style="display: block;margin: auto;" >
        <i class="fa fa-pencil" style="color:red"></i>
        </button>
        </td>
        <td id="fname-edit-td"> 
        <button type="submit" form="fnameForm" class="btn btn-light" id="fname-edit-button" onclick="editText(&quot;fname-edit-button&quot;)" style="display: block;margin: auto;">
        <i class="fa fa-pencil" style="color:red"></i>
        </button> </td>
        <td id="lname-edit-td"> 
        <button type="submit" form="lnameForm" class="btn btn-light" id="lname-edit-button" onclick="editText(&quot;lname-edit-button&quot;)" style="display: block;margin: auto;">
        <i class="fa fa-pencil" style="color:red"></i>
        </button> </td>
        <td usertype-edit-td> 
        <button type="button" class="btn btn-light" id="usertype-edit-button" onclick="animateModal(&quot;usertypeModal&quot;)" style="display: block;margin: auto;">
        <i class="fa fa-pencil" style="color:red"></i>
        </button> </td>
    </tr>';

    echo '</table>';
}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
  }
?>