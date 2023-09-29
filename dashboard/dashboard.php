<?php 
require __DIR__."/../login/verify.php";
session_start();
$email = verify(session_id(),[1,2,3,4,5]);
if ($email) {
  $db = "sqlite:../database.sqlite"; // Change accordingly
  $conn = new PDO($db) or die("cannot open the database");
    $query ="SELECT userTypeId FROM user_usertype WHERE userId = '".$email."'";
  $userTypes = array();
  foreach($conn->query($query) as $type){
	  array_push($userTypes,$type["userTypeId"]);
  }
    #  -----HTML HEAD--------
    echo '<!DOCTYPE html>
    <html>
      <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>XAMPP-Starter</title>
        <link href="../dashboard/dashboard.css" rel="stylesheet" />
        <link rel="icon" href="../media/favicon.ico" type="image/ico">
        <link
          rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
          crossorigin="anonymous"
        />
        <link
          rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
        />
        <script src="../logout/logout.js"></script>
        <script src="dashboard.js"></script>
        <script src="../student/send_rating.js"></script>
        <script src="../ta/populate.js"></script>
        <script src="../admin/manage_courses.js"></script>
        <script src="../admin/manage_tas.js"></script>
        <script src="../sysop/manage_users.js"></script>
        <script src="../sysop/manage_courses.js"></script>
         <script src="../sysop/manage_profs.js"></script>
        <script
        src="https://code.jquery.com/jquery-3.3.1.js"
        integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
        crossorigin="anonymous"
      ></script>
      <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"
      ></script>

      </head>';

    #------------PAGE HEADER--------
    echo '<div class="container">
    <nav class="navbar">
      <!-- Header -->
      <div class="container-fluid">
        <!-- Logo and User Role  -->
        <div class="d-flex align-items-center">
          <img
            src="../media/mcgill_logo.png"
            style="width: 14rem; height: auto;cursor: pointer"
            alt="mcgill-logo"
            onclick="dashboardPage()"
          />
          <select class="custom-select" id="selecttype" onchange="showContent()">';


        #-----------------USER TYPE RELATED------------
          if (in_array(5,$userTypes)) {
            echo '<option value="Sysop Tasks" >Sysop Tasks</option>
            <option value="Rate a TA" >Rate a TA</option>
            <option value="TA Management" >TA Management</option>
            <option value="TA Admin" >TA Admin</option>';

        }else if (in_array(4,$userTypes)) {
            echo '<option value="sysop" >TA Admin</option>
            <option value="sysop" >Rate a TA</option>
            <option value="sysop" >TA Management</option>';
        }
        else if (in_array(2,$userTypes) or in_array(3,$userTypes)) {
            echo '<option value="sysop" >TA Management</option>
            <option value="sysop" >Rate a TA</option>';
        }        
         else if (in_array(1,$userTypes)) {
            echo '<option value="sysop" >Rate a TA</option>';
        } 



        echo '<option hidden value="welcome" selected="selected">welcome</option>';
# ------LOGOUT-----------
    echo '</select>
        </div>
        <!-- Logout -->
        <div>
          <button
            type="button"
            class="btn btn-link"
            onclick="javascript:sendLogoutRequest()"
          >
            <i class="fa fa-sign-out" style="font-size: 24px "></i>
          </button>
        </div>
        <a href="javascript:void(0);" class="hamburger" onclick="showHamMenu()">
        <i class="fa fa-bars" style="color:red; font-size:30px"></i>
      </a>
      </div>
        <div class="topnav" id="myTopnav">
        
        ';
        #-------------HAMBURGER MENU---------
        if (in_array(5,$userTypes)) {
          echo '<div class="hamOption" onclick="selectPage(\'Sysop Tasks\')" >Sysop Tasks</div>
          <div class="hamOption" onclick="selectPage(\'Rate a TA\')" >Rate a TA</div>
          <div class="hamOption"  onclick="selectPage(\'TA Management\')">TA Management</div>
          <div class="hamOption" onclick="selectPage(\'TA Admin\')" >TA Admin</div>';

      }else if (in_array(4,$userTypes)) {
          echo '          <div class="hamOption"  >Rate a TA</div>
          <div class="hamOption"  >TA Management</div>
          <div class="hamOption"  >TA Admin</div>';
      }
      else if (in_array(2,$userTypes) or in_array(3,$userTypes)) {
          echo '<div class="hamOption"  >Rate a TA</div>
          <div class="hamOption"  >TA Management</div>';
      }        
       else if (in_array(1,$userTypes)) {
          echo '<div class="hamOption"  >Rate a TA</div>';
      } 
      

      #--------------CONTENT--------------
    echo '
       <div class="hamOption" onclick="javascript:sendLogoutRequest()" >Logout</div>
        </div>


      

    </nav>
    <nav></div>
    <div id="personalizedDashboard"></div>
    '
    
    ;

    

    echo '
    </html>
    ';
}else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="../login/login.html">Back to Login</a></h1>.</text>
            </div>';
}
?>
