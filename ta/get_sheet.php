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


    echo '<table>';
    echo'<tr>
        <th class="red-label">TA</th>
        <th class="red-label">Office Hours</th>
        <th class="red-label">OH Location</th>
        <th class="red-label">Responsibilities</th>
        <th class="red-label">Email</th>
        </tr>';
    
    foreach($conn->query("SELECT * FROM tasheet WHERE course='".$_GET['course']."' and year='".$_GET['year']."' and term='".$_GET['term']."'") as $ta){
        if ($ta['email']==$email){
            //if the user's row, add the buttons on oh and location row
        echo 
        '<tr>
            <td>'. $ta['name'] .'</td>
            <td>'. $ta['hours'] .' 
            <button type="button" onclick="showModal(\'OH\',\''. $ta['name'] .'\',\''.$ta['email'].'\')" class="btn btn-light" id="'.$ta['email'].'-oh-button" style="display: inline;margin: 0;text-align:right;">
            <i class="fa fa-pencil" style="color:red"></i>
            </button></i></td>
            <td>'. $ta['location'] .' <button onclick="showModal(\'location\',\''. $ta['name'] .'\',\''.$ta['email'].'\')" type="button" class="btn btn-light" id="'.$ta['email'].'-loc-button" style="display: inline;margin: 0;text-align:right;">
            <i class="fa fa-pencil" style="color:red"></i>
            </button></td>
            <td>'. $ta['duties'] .'</td>
            <td>'.$ta['email'].'</td>
        </tr>';            
        }
        else if ($prof['courseInstructor'] == $email){
            //if the user is the prof, add button to duties row
            echo 
            '<tr>
                <td>'. $ta['name'] .'</td>
                <td>'. $ta['hours'] .'</td>
                <td>'. $ta['location'] .'</td>
                <td>'. $ta['duties'] .' <button onclick="showModal(\'responsibilities\',\''. $ta['name'] .'\',\''.$ta['email'].'\')" type="button" class="btn btn-light" id="'.$ta['email'].'-duties-button" style="display: inline;margin: 0px;text-align:right;">
                <i class="fa fa-pencil" style="color:red"></i>
                </button></i></td>
                <td>'.$ta['email'].'</td>
            </tr>';  
        }else{
            //print row
            echo 
            '<tr>
                <td>'. $ta['name'] .'</td>
                <td>'. $ta['hours'] .'</td>
                <td>'. $ta['location'] .'</td>
                <td>'. $ta['duties'] .'</td>
                <td>'.$ta['email'].'</td>
            </tr>';  
        }
        
        
    }
    echo '</table>';
}
?>