<?php 

require __DIR__.'/../login/verify.php';

session_start();
$email = verify(session_id(),[2,3,4,5]);
if ($email) {

    $db = "sqlite:../database.sqlite"; // Change accordingly
    $conn = new PDO($db) or die("cannot open the database");

    $query = "SELECT courseInstructor FROM course WHERE courseNumber='".$_GET['course']."' and year='".$_GET['year']."' and term='".$_GET['term']."'";
    $sth = $conn->query($query);
    $prof = $sth->fetch(PDO::FETCH_ASSOC);
    echo '<div class="announcements">';
    if ($prof['courseInstructor'] == $email){
      //if the user is the prof, show the add button
      echo '<br>
      <h2>'.$_GET['course'].' Announcements 
                <button
                      type="button"
                      class="btn btn-light"
                      id="AnnouncementModalButton"
                      onclick="animateModalSheet(\'announcementModal\')"
                    >
                    <i class="fa fa-plus" style="font-size: 24px"></i>
                </button>
      
    </h2><br>
      <div id="announcements">' ;
    }else{
      echo '<br>
      <h2>'.$_GET['course'].' Announcements</h2><br>
      <div id="announcements">' ;
    }


    //display each announcement for the course
    foreach($conn->query("SELECT * FROM announcements WHERE course='".$_GET['course']."' and year='".$_GET['year']."' and term='".$_GET['term']."' ORDER BY date DESC") as $announce){
      $date = substr($announce['date'],0,strpos($announce['date'],' '));
      
      echo "<h4>".$announce['author']." - ".$announce['title']."</h4>
        <p>".$announce['message']."</p><p style='font-size:75%;'>
          on ".$date."
        </p> <br><hr />";
        
    }
echo '</div></div></div>';
}
?>