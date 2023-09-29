<?php
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);

//possible csv types
$csv_mimetypes = array(
    'text/csv',
    'application/csv',
    'text/comma-separated-values',
    'application/excel',
    'application/vnd.ms-excel',
    'application/vnd.msexcel'
);
if($email){

    //function for role conversion
    function user_role_id_map($role){
        $user_roles = array(1 => "student", 2 => "professor", 3 => "ta", 4 => "admin", 5 => "sysop");
        $key = array_search($role, $user_roles);
        return($key);
    }
    $not_added = true;

    if ($email) {
        $conn = new SQLite3("../database.sqlite") or die("Connection Failed.");
        //if file is provided
        if(isset($_FILES['file'])){
            //if there is no error with upload
            if($_FILES['file']['error'] == 0){
                $file_content = file($_FILES['file']['tmp_name']);
                $date = date('Y-m-d H:i:s');
                //if file is csv
                if (in_array($_FILES['file']['type'], $csv_mimetypes)){
                    foreach($file_content as $row) {
                        //parse csv
                        $items = explode(",", trim($row));
                        $first_name = $items[0];
                        $last_name = $items[1];
                        $email = $items[2];
                        $password = $items[3];
                        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
                        $account_types = explode('/', $items[4]);
                        $account_types = array_map("user_role_id_map", $account_types);

                        //insert if not already a user
                        $result = $conn->query("SELECT * FROM user WHERE email ='".$email."'");
                        $user = $result -> fetchArray(SQLITE3_ASSOC);

                        if (!$user){
                            $query = "INSERT INTO user (firstName, lastName, email, password, createdAt, updatedAt) VALUES ('".$first_name."', '".$last_name."', '".$email."', '".$hashed_pass."', '".$date."', '".$date."')";
                            $sql = $conn -> query($query);
                            //insert usertypes if user insertion was successful
                            if ($sql) {
                                foreach ($account_types as $account_type) {
                                    $query = "INSERT INTO user_usertype (userId, userTypeId) VALUES ('".$email."', ".$account_type.")";
                                    $sql = $conn -> query($query);
                                }
                            //check if at least one insertion was performed   
                            if($not_added){
                                $not_added = false;
                            }
                            }
                        }
                    }
    
                    if($not_added){
                        echo "<div class='error'>Each row was already in the database. No insertion.</div>";
                    }
                    else{
                        echo "<div class='success'>Successful.</div>";
                    }          
                }
                else{
                    echo "<div class='error'>Please upload a CSV file...</div>";
                }
            }
            else{
                echo "<div class='error'>There was an error while uploading your file...</div>";
            }
        }
        else{
            echo "<div class='error'>Please select a file...</div>";
        }
    }
}
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}

?>