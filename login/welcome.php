<?php 
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(),[1,2,3,4,5]);
if ($email) {
// Create connection
$db = "sqlite:../database.sqlite"; // Change accordingly
$conn = new PDO($db) or die("cannot open the database");

$query = "SELECT * FROM user WHERE email ='".$email."'";
$response = $conn->query($query);
$user = $response->fetch(PDO::FETCH_ASSOC);


$query ="SELECT userTypeId FROM user_usertype WHERE userId = '".$email."'";
$userTypes = array();
foreach($conn->query($query) as $type){
    array_push($userTypes,$type["userTypeId"]);
}


    echo '
    <hr></nav>
    <div class="welcomeMessage">
    <text><h1>Welcome, ';
    echo $user['firstName'];
    echo' <br /><br></h1></text></div>
    ';

    #---------LINK BOXES---------------
    if (in_array(5,$userTypes)) {
        echo ' 
        <div class="dashboardElement">
        <h3>Sysop Tasks</h3><br />
        <a href="javascript:openTab(\'Sysop Tasks\',\'Professors\')">Professors</a> 
        <a href="javascript:openTab(\'Sysop Tasks\',\'Courses\')">Courses</a>
        <a href="javascript:openTab(\'Sysop Tasks\',\'Users\')">Users</a>
        </div>

        <div class="dashboardElement">
        <h3>TA Admin</h3><br />
        <a href="javascript:openTab(\'TA Admin\',\'TAs\')">TAs</a> 
        <a href="javascript:openTab(\'TA Admin\',\'Courses\')">Courses</a>
        </div>
        
        
        <div class="dashboardElement">
        <h3>TA Management</h3><br />
        <a href="javascript:openTab(\'TA Management\',\'Announcements\')">Announcements</a> 
        <a href="javascript:openTab(\'TA Management\',\'Chats\')">Chat</a>
        <a href="javascript:openTab(\'TA Management\',\'TA Sheets\')">TA Sheets</a>
        </div>

        <div class="dashboardElement">
        <h3>TA Ratings</h3><br />
        <a href="javascript:openTab(\'Rate a TA\',\'rating\')">Rate a Ta</a> 
        </div>


        
        
        ';

    }else if (in_array(4,$userTypes)) {
        echo '       
        <div class="dashboardElement">
        <h3>TA Admin</h3><br />x

        </div>
        
        
        <div class="dashboardElement">
        <h3>TA Management</h3><br />
        <a href="javascript:openTab(\'TA Management\',\'Announcements\')">Announcements</a> 
        <a href="javascript:openTab(\'TA Management\',\'Chats\')">Chat</a>
        <a href="javascript:openTab(\'TA Management\',\'TA Sheets\')">TA Sheets</a>
        </div>

        <div class="dashboardElement">
        <h3>TA Ratings</h3><br />
        <a href="javascript:openTab(\'Rate a TA\',\'rating\')">Rate a Ta</a> 
        </div>';
    }
    else if (in_array(2,$userTypes) or in_array(3,$userTypes)) {
        echo '
        <div class="dashboardElement">
        <h3>TA Management</h3><br />
        <a href="javascript:openTab(\'TA Management\',\'Announcements\')">Announcements</a> 
        <a href="javascript:openTab(\'TA Management\',\'Chats\')">Chat</a>
        <a href="javascript:openTab(\'TA Management\',\'TA Sheets\')">TA Sheets</a>
        </div>

        <div class="dashboardElement">
        <h3>TA Ratings</h3><br />
        <a href="javascript:openTab(\'Rate a TA\',\'rating\')">Rate a Ta</a> 
        </div>
        ';
    }        
     else if (in_array(1,$userTypes)) {
        echo '        
        <div class="dashboardElement">
        <h3>TA Ratings</h3><br />
        <a href="javascript:openTab(\'Rate a TA\',\'rating\')">Rate a Ta</a> 
        </div>';
    } 

}else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}
?>

