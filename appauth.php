<?php

ini_set('session.gc_maxlifetime', 400);
ini_set('session.cookie_lifetime', 0); // erase session cookie to zero when browser is closed

session_start();

if (!isset($_SESSION['userid'])) {

    include './db.php';

    if (isset($_POST)) {

        $email = $_POST['email'];
        $passcode = $_POST['passcode'];
        $userstatus = 1;

        $apiresponse = ValidateAuth($email, $passcode, $userstatus, $dbconnect);

        echo json_encode($apiresponse);
    }

    else {

        echo json_encode(array("status" => 400, "message" => "invalid request"));
    }
}


function ValidateAuth($email, $passcode, $userstatus, $dbconnect)
{

    $role = "";

    if (!isset($dbconnect)) {

        return array('status' => 400, 'message' => 'dbconnection errupted');
    }

    else {
        $sql = "SELECT u.id,u.urole FROM appusers u WHERE u.email = '" . $email . "' AND passcode = '" . $passcode . "' AND ustatus = ".$userstatus."";

        $sqlresponse = mysqli_query($dbconnect, $sql);

        if (mysqli_num_rows($sqlresponse) > 0) {

            while ($row = mysqli_fetch_assoc($sqlresponse)) {

                $_SESSION['userid'] = $row['id'];
                $_SESSION['userrole'] = $row['urole'];

                $role = $row['urole']; // send to front end for page navigation

            }
        }

        return array('status' => 200, 'role' => $role);
    }

}



?>
