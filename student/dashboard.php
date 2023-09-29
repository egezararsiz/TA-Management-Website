<?php 
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(),[1,2,3,4,5]);
if ($email) {
    echo '
    <hr>
</nav>
<div class="tab-content" id="nav-tabContent">
  <br />
    ';

  // Create connection
  $db = "sqlite:../database.sqlite"; // Change accordingly
  $conn = new PDO($db) or die("cannot open the database");
  
  
  $query = "SELECT * FROM student_courses WHERE student='".$email."'";

  foreach($conn->query($query) as $course){

    $first = true;
    $query = "SELECT * FROM ta_courses WHERE course='".$course['course']."' and year='".$course['year']."' and term='".$course['term']."'";
    foreach ($conn->query($query) as $ta) {
      $cou = $course['course'].' - '.$course['term'].' '.$course['year'];
      //print the name of the course once
      if($first)  {      echo '
      <h2>'.$cou.'</h2>
      ';
    $first=false;}  


      $sth = $conn->query("SELECT * FROM user WHERE email='".$ta['ta']."'");
      $user = $sth->fetch(PDO::FETCH_ASSOC); 
        //print the ta's name, rating stars and comment box
      echo '<div class="form-group">
      <form id="'.$ta['ta'].$cou.'" action="javascript:sendRating(\''.$ta['ta'].'\',\''.$cou.'\')" method="post"> ';
      echo $user['firstName'] .' '. $user['lastName'].'<br>';

      //stars
      echo '<label class="starRating"><input onclick="(function() { 
        document.getElementById(\''.$ta['ta'].$cou.'\').style = \'color:black;\'; 
        for(i=2;i<6;i++){ document.getElementById(\''.$ta['ta'].$cou.'\'+i).style = \'color:grey;\'; } 
      })()" style="display:none" type="radio" name="rating" value="1"><i id="'.$ta['ta'].$cou.'1" class="fa fa-star" style="color:grey;" aria-hidden="true"></i></label>';

      echo '<label class="starRating"><input onclick="(function() { 
        for(i=1;i<3;i++){ document.getElementById(\''.$ta['ta'].$cou.'\'+i).style = \'color:black;\'; } 
        for(i=3;i<6;i++){ document.getElementById(\''.$ta['ta'].$cou.'\'+i).style = \'color:grey;\'; } 
      })()" style="display:none" type="radio" name="rating" value="2"><i id="'.$ta['ta'].$cou.'2" class="fa fa-star" style="color:grey;" aria-hidden="true"></i></label>';


      echo '<label class="starRating"><input onclick="(function() { 
        for(i=1;i<4;i++){ document.getElementById(\''.$ta['ta'].$cou.'\'+i).style = \'color:black;\'; } 
        for(i=4;i<6;i++){ document.getElementById(\''.$ta['ta'].$cou.'\'+i).style = \'color:grey;\'; } 
      })()" style="display:none" type="radio" name="rating" value="3"><i id="'.$ta['ta'].$cou.'3" class="fa fa-star" style="color:grey;" aria-hidden="true"></i></label>';


      echo '<label class="starRating"><input onclick="(function() { 
        for(i=1;i<5;i++){ document.getElementById(\''.$ta['ta'].$cou.'\'+i).style = \'color:black;\'; } 
        for(i=5;i<6;i++){ document.getElementById(\''.$ta['ta'].$cou.'\'+i).style = \'color:grey;\'; } 
      })()" style="display:none" type="radio" name="rating" value="4"><i id="'.$ta['ta'].$cou.'4" class="fa fa-star" style="color:grey;" aria-hidden="true"></i></label>';


      echo '<label class="starRating"><input onclick="(function() { 
        for(i=1;i<6;i++){ document.getElementById(\''.$ta['ta'].$cou.'\'+i).style = \'color:black;\'; } 
      })()" style="display:none" type="radio" name="rating" value="5"><i id="'.$ta['ta'].$cou.'5" class="fa fa-star" style="color:grey;" aria-hidden="true"></i></label>';


      //comment box
      echo '
      <input type="radio" name="rating" value="0" hidden="true" checked="true">
      <input
      type="text"
      name="comment"
      placeholder="comment"
      id="comment"
      class="ratingComment"
    /><input class="ratingSubmit" type="submit" value="Submit Rating" >
    <div id="'.$ta['ta'].$cou.'-err" class="ratingErr"></div></form></div><hr>';
    
  }
}

  }
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="../login/login.html">Back to Login</a></h1>.</text>
            </div>';
}
?>