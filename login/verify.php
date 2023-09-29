<?php 


function verify(string $id, $types){
    $db = "sqlite:../database.sqlite"; // Change accordingly
    if ($id == "" )
        return NULL;
    $conn = new PDO($db) or die("cannot open the database");
    //verify if a session is open
    foreach($conn->query("SELECT * FROM sessions WHERE id='".$id."'") as $session){
    $date = time();
    if ($date < $session["expire"]){
        //if an appropriate session is open, update the expiry
        $expire = $date+3600;
        $upd = $conn->query("UPDATE sessions SET expire=".$expire." WHERE id = '".$id."'");

        $query ="SELECT userTypeId FROM user_usertype WHERE userId = '".$session['email']."'";
        $userTypes = array();
        //verifies usertypes
        foreach($conn->query($query) as $type){
            if(in_array($type["userTypeId"],$types)) return $session['email'];
            }
        return null;
    }else{
        $del = $conn->query("DELETE FROM sessions WHERE id = ".$id);
    return NULL;
    }}
    return NULL;
    }
?>
