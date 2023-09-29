<?php
session_start();
$id = session_id();
$db = "sqlite:../database.sqlite";
$conn = new PDO($db) or die("cannot open the database");
$del = $conn->query("DELETE FROM sessions WHERE id = '".$id."'");
$_SESSION = array();
$params = session_get_cookie_params();
setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
session_destroy();
echo "<script>function redirect() { 
    window.location.replace('../logout/logout.html'); 
}</script>";
?>
