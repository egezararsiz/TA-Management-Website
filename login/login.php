<?php 
$db = "sqlite:../database.sqlite"; // Change accordingly

// Create connection
$conn = new PDO($db) or die("cannot open the database");
$email = $_POST['email'];
$query = "SELECT * FROM user WHERE email='".$email."'";
$sth = $conn->query($query);
$user = $sth->fetch(PDO::FETCH_ASSOC);    
if($user){    
    // retrieve password from database
	$hashed_pass = $user['password'];
    // check against the user input password
    $login_success = password_verify($_POST['password'], $hashed_pass);
	if ($login_success) {
		//create the session and add to the db
	session_start();
	$id = session_id();
	$expire = time()+3600;
	$send = "INSERT INTO sessions (id, email, expire) VALUES (?, ?, ?)";
	$conn->prepare($send)->execute([$id,$email,$expire]);
	echo "<script>function redirect() { 
                window.location.replace('../dashboard/dashboard.php'); 
	    }</script>";
	exit();
    } else {
	    echo '<text>Password is incorrect. Try again.</text>';
	    exit();
    }
}else{
    echo '<text>Username does not exist.</text>';
}

?>
