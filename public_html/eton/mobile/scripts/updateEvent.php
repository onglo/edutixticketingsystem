<?php

include("/home/sites/edutix.com/config/config.php");

// attempt to link to the db
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {
  // kill the script
  die();
};

// prepare a query to fetch the salt for that event
$query = "SELECT `salt` FROM `".mysqli_real_escape_string($link, $_POST["db"])."` WHERE `id` = ".mysqli_real_escape_string($link, $_POST["eventID"])." LIMIT 1";

// attempt the query
if ($result = mysqli_query($link, $query)) {
    // save this as the salt
    $salt = mysqli_fetch_array($result);
    $salt = $salt["salt"];
}
else {
    die();
}

// format the date by replacing the T with a space
$date = str_replace("T", " ", $_POST["eventDate"]);

// prepare all the data that we are going to use
$eventName = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["eventTitle"]));
$eventDescription = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["eventDesc"]));
$eventDate = mysqli_real_escape_string($link, $date);
$eventLocation = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["eventLocation"]));
$eventHost = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["eventHost"]));
$isTicketed = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["isTicketed"]));

$userQuery = "UPDATE `".mysqli_real_escape_string($link, $_POST["db"])."` SET `eventName` = '".$eventName."', `eventDesc` = '".$eventDescription."', `eventLocation` = '".$eventLocation."', `eventDate` = '".$eventDate."', `eventHost` = '".$eventHost."', `isTicketed` = '".$isTicketed."', `emailSent` = 'false' WHERE `id` = '".mysqli_real_escape_string($link, $_POST["eventID"])."' LIMIT 1";

// attempt the query
if (mysqli_query($link, $userQuery)) {
    echo "success";
}
else {
    echo $userQuery;
    die();
}

?>
