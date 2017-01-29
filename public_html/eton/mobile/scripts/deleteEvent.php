<?php

require_once "/home/sites/edutix.com/config/config.php";

// connect to our database
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {

    die();

};

// prepare a query that will delete the event
$userQuery = "DELETE FROM `".mysqli_real_escape_string($link, $_POST["db"])."` WHERE `id` = '".mysqli_real_escape_string($link, $_POST["eventID"])."' LIMIT 1";

// check if the query was successful
if (mysqli_query($link, $userQuery))  {
    echo "success";
}

?>
