<?php
include "convert_account.php";
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");

    //get all users
    $result = $conn->query("SELECT * FROM user");

    //display headers
    echo '<table>';
    echo'<tr>
        <th class="red-label">Email</th>
        <th class="red-label">First Name</th>
        <th class="red-label">Last Name</th>
        <th class="red-label">User Type</th>
        </tr>';


    while ($user = $result->fetchArray(SQLITE3_ASSOC)) {
        $query = "SELECT usertype.userType FROM usertype INNER JOIN user_usertype 
                ON usertype.idx=user_usertype.userTypeId WHERE user_usertype.userId ='".$user['email']."'";
        $res = $conn->query($query);

        // create comma-separated list of account types
        $uTypes = [];

        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $uTypes[] = convertAccountType($row['userType']);
        }
        $userRoles = implode(', ', $uTypes);

        //display
        echo 
        '<tr>
            <td>'. $user['email'] .'</td>
            <td>'. $user['firstName'] .'</td>
            <td>'. $user['lastName'] .'</td>
            <td>'. $userRoles .'</td>
        </tr>';
    }
    echo '</table>';
}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}
?>