<?php
function convertAccountType($type) {
    switch ($type) {
        case "student":
            return "Student";
        case "professor":
            return "Professor";
        case "admin":
            return "TA Administrator";
        case "ta":
            return "Teaching Assistant";
        case "sysop":
            return "System Operator";
    }
}
?>