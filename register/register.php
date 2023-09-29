<?php 
//CURRENT SEMESTER
$semester = "Fall 2022";
$year = "2022";
$term = "Fall";


// Create connection
$db = "sqlite:../database.sqlite"; // Change accordingly
$conn = new PDO($db) or die("cannot open the database");

$email = $_POST['email'];
$query = "SELECT * FROM user WHERE email='".$email."'";
$sth = $conn->query($query);
$user = $sth->fetch(PDO::FETCH_ASSOC);  

if ($user) {
    echo 'A user already exist with this email.';
    return;
}elseif($email=="" or $_POST['password']==""){
    echo 'Please enter an email and a password.';
}elseif(substr_count($email,'@')!=1 or $email[0]=="@" or $email[strlen($email)-1] =="@"){
    echo 'Please enter a valid email adress.';
}
else {
    //create the user
    $name = $_POST['firstname'];
    $lastName = $_POST['lastname'];
    $student = $_POST['student'] == 'true';
    $ta = $_POST['ta'] == 'true';
    $prof = $_POST['professor'] == 'true';
    $sysop = $_POST['sysop'] == 'true';
    $admin = $_POST['admin'] == 'true';
    $date=date("Y-m-d H:i:s");
    $hash = password_hash($_POST['password'],PASSWORD_DEFAULT);
    $sql = $conn->prepare("INSERT INTO user (`firstName`, `lastName`, `email`, `password`, `createdAt`, `updatedAt`,'semester') VALUES (?,?,?,?,?,?,?)");
    $sql->execute([$name,$lastName,$email,$hash,$date,$date,$semester]);
    if ($student){
        //add the user's courses and insert usertype
        $sql = $conn->prepare("INSERT INTO user_usertype ( `userId`, `userTypeId`) VALUES (?,1)");
        $sql->execute([$email]);
        $courses = $_POST['studentCourses'];
        $courses = "";
        foreach(explode(",",$_POST['studentCourses']) as $course){
            $sql = $conn->prepare("INSERT INTO student_courses ( `student`, `course`,'year','term') VALUES (?,?,?,?)");
            $sql->execute([$email,$course,$year,$term]);
            if ($courses=="")
            $courses = $course;
            else
            $courses = $courses.",".$course;
        }
        $sql = $conn->prepare("UPDATE user SET courses_registered_in =? WHERE email=?");
        $sql->execute([$courses,$email]);
        $sql = $conn->prepare("UPDATE user SET id =? WHERE email=?");
        $sql->execute([$_POST['student_id'],$email]);
    }        
    //insert usertypes
    if ($prof){
        $sql = $conn->prepare("INSERT INTO user_usertype ( `userId`, `userTypeId`) VALUES (?,2)");
        $sql->execute([$email]);    
  
    }
    if ($ta){
        
        $sql = $conn->prepare("INSERT INTO user_usertype ( `userId`, `userTypeId`) VALUES (?,3)");
        $sql->execute([$email]);    
        $sql = $conn->prepare("UPDATE user SET id =? WHERE email=?");
        $sql->execute([$_POST['student_id'],$email]);
    }
    if ($admin){
        $sql = $conn->prepare("INSERT INTO user_usertype ( `userId`, `userTypeId`) VALUES (?,4)");
        $sql->execute([$email]);    
 
    }
    if ($sysop){
        $sql = $conn->prepare("INSERT INTO user_usertype ( `userId`, `userTypeId`) VALUES (?,5)");
        $sql->execute([$email]);    

    }

    echo "<script>function redirect() { 
        window.location.replace('../login/login.html'); 
    }</script>";
    return;
}
?>