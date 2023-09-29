<?php 
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(),[2,3,4,5]);
if ($email) {

    echo '<div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a
      class="nav-item nav-link active"
      data-toggle="tab"
      href="#nav-announcements"
      role="tab"
      >Announcements</a
    >
    <a
      class="nav-item nav-link"
      data-toggle="tab"
      href="#nav-chat"
      role="tab"
      >Chats</a
    >

    <a
    class="nav-item nav-link"
    data-toggle="tab"
    href="#nav-sheet"
    role="tab"
    >TA Sheets</a
  ></div>
  <select name="course" class="course-select" id="selectCourse" onchange="populate()">
  
  ';
  $db = "sqlite:../database.sqlite"; // Change accordingly
  $conn = new PDO($db) or die("cannot open the database");
  $query = "SELECT course FROM ta_courses WHERE ta='".$email."'";

  //populate dropdown with courses
  foreach($conn->query($query) as $course){
    $query = "SELECT * FROM course WHERE courseNumber='".$course['course']."'";
    $sth = $conn->query($query);
    $cou = $sth->fetch(PDO::FETCH_ASSOC);
    echo '<option value="'.$cou['courseNumber'].' - '.$cou['courseName'].' - '.$cou["term"].' '.$cou["year"].'">'.$cou['courseNumber'].' - '.$cou['courseName'].' - '.$cou["term"].' '.$cou["year"].'</option>';

  }

  $query = "SELECT * FROM course WHERE courseInstructor='".$email."'";

  foreach($conn->query($query) as $course){

    echo '<option value="'.$course['courseNumber'].' - '.$course['courseName'].' - '.$course["term"].' '.$course["year"].'">'.$course['courseNumber']." - ".$course['courseName'].' - '.$course["term"].' '.$course["year"].'</option>';

  }

  //the divs that get filled by populate()
  echo '</select>
      </nav>
      <div class="tab-content" id="nav-tabContent">

      <div class="tab-pane fade " id="nav-chat" role="tabpanel" style="margin-left:5%;margin-right:5%;margin-top:1%">

      <iframe id="chatFrame" src="../ta/chat.php?course=" frameborder="1" class="chatBox" ></iframe>
      </div>

      
      <div class="tab-pane fade " id="nav-sheet" role="tabpanel">
      <div id="sheet"  style="margin-left:5%;margin-right:5%;margin-top:1%"></div>
      </div>

      <div class="tab-pane fade show active" id="nav-announcements" role="tabpanel" style="margin-left:10%;margin-right:10%"> </div>
      

      ';
      //create the modals
      echo '
      <div id="announcementModal" class="our-modal">
      <!-- Modal content -->
      <div class="our-modal-edit-content announcement">
        <span id="announcementModal-close" class="our-modal-close">&times;</span>
        <form id="announcement-form" class="user-form" action="javascript:sendAnnouncement()" method="post" autocomplete="off">
          <h3 style="color: black;"> New Announcement <br /> </h3>
          <div>
            <hr></hr>
            <input id="announcementModal-title"  style="width:100%" name="title" placeholder="Title"><div style="height:10%"></div>
            <textarea
                  id="announcementModal-content"
                  class="form-control"
                  placeholder="Announcement"
                  name="content"
                  wrap="soft"
                  rows="17"
                ></textarea>
          </div>
          <br />
          <div id="announcementModal-err">
          </div>
          <div>
            <input style="position: absolute; right: 20px; bottom: 10px; cursor: pointer;" type="submit" value="Submit">
          </div>
        </form>
      </div>
    </div>


    <div id="responsibilities" class="our-modal">
    <!-- Modal content -->
    <div class="our-modal-edit-content sheet">
      <span id="responsibilities-close" class="our-modal-close">&times;</span>
      <form id="responsibilities-form" class="user-form" action="javascript:updateTASheet(\'responsibilities\')" method="post" autocomplete="off">
        <h3 style="color: black;"><div id=responsibilities-head> Change responsibilities </div> <br /> </h3>
        <div>
          <hr></hr>
          <input id="responsibilities-duties"  style="width:100%" name="new" placeholder="new responsibilities"><div style="height:10%"></div>
          <input type="hidden" id="responsibilities-email" name="email" value="">
          <input type="hidden" value="duties" name="toUpdate">
          </div>
        <br />
        <div id="responsibilities-err">
        </div>
        <div>
          <input style="position: absolute; right: 20px; bottom: 10px; cursor: pointer;" type="submit" value="Submit">
        </div>
      </form>
    </div>
  </div>

  <div id="OH" class="our-modal">
  <!-- Modal content -->
  <div class="our-modal-edit-content sheet">
    <span id="OH-close" class="our-modal-close">&times;</span>
    <form id="OH-form" class="user-form" action="javascript:updateTASheet(\'OH\')" method="post" autocomplete="off">
      <h3 style="color: black;"> <div id=OH-head> Change OH </div> <br /> </h3>
      <div>
        <hr></hr>
        <input id="OH-duties"  style="width:100%" name="new" placeholder="New Hours"><div style="height:10%"></div>
        <input type="hidden" id="OH-email" name="email" value="">
        <input type="hidden" value="hours" name="toUpdate">

      </div>
      <br />
      <div id="OH-err">
      </div>
      <div>
        <input style="position: absolute; right: 20px; bottom: 10px; cursor: pointer;" type="submit" value="Submit">
      </div>
    </form>
  </div>
</div>


<div id="location" class="our-modal">
<!-- Modal content -->
<div class="our-modal-edit-content sheet">
  <span id="location-close" class="our-modal-close">&times;</span>
  <form id="location-form" class="user-form" action="javascript:updateTASheet(\'location\')" method="post" autocomplete="off">
    <h3 style="color: black;"> <div id=location-head> Change location </div> <br /> </h3>
    <div>
      <hr></hr>
      <input id="location-duties"  style="width:100%" name="new" placeholder="New Location"><div style="height:10%"></div>
      <input type="hidden" id="location-email" name="email" value="">
      <input type="hidden" value="location" name="toUpdate">
    </div>
    <br />
    <div id="location-err">
    </div>
    <div>
      <input style="position: absolute; right: 20px; bottom: 10px; cursor: pointer;" type="submit" value="Submit">
    </div>
  </form>
</div>
</div>

      
    </div>
      <script>
        populate();
      </script>

    </body>
  </html>';

} 
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}
?>