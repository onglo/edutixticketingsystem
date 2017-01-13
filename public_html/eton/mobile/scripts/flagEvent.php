<?php

include("/home/sites/edutix.com/config/config.php");

// start a session that will authenticate the user
if (session_id() == "") {
    session_start();
}

// a script to flag an event for a user
// attempt to link to the db
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {
  // kill the script
  die("Error connecting to database");
};

// prepare a query that will insert the user into the user's personal list
$userQuery = "UPDATE `etonUsers` SET `flaggedList` = CONCAT(IFNULL(flaggedList,''), '.".mysqli_real_escape_string($link, $_POST['id'])."')  WHERE `id` = ".mysqli_real_escape_string($link,$_SESSION['idNumber']);

// attempt this query
if (mysqli_query($link, $userQuery)) {

    echo "success";

    // update the user's session var that keeps track of the events they are registed to
    $_SESSION["flagged"] .= ".".$_POST["id"];
}
else {
    die();
}

?>
