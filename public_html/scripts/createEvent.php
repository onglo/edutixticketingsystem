<?php

include("/home/sites/edutix.co.uk/config/config.php");

// a script to create an event from the user's input
// attempt to link to the db
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {
  // kill the script
  die("Error connecting to database");
};

// generate a secret salt for the event
$length = 32;
$secure = true;
$salt = openssl_random_pseudo_bytes($length, $secure);

// format the date by replacing the T with a space
$date = str_replace("T", " ", $_POST["dateInput"]);

// if a session isn't started, start a session
if (session_id() == "") {
    session_start();
};

// encrypt all of the data for this event
$eventName = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["titleInput"]));
$eventDescription = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["descInput"]));
$eventDate = mysqli_real_escape_string($link, $date);
$eventLocation = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["locationInput"]));
$eventHost = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["hostName"]));
$isTicketed = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["isTicketed"]));
$createdBy = mysqli_real_escape_string($link, $_SESSION["idNumber"]);
$salt = mysqli_real_escape_string($link, $salt);

// prepare a query that will insert it into the database
$query = "INSERT INTO `etonEvents` (`eventName`,`eventDesc`,`eventDate`,`eventLocation`,`eventHost`,`isTicketed`,`createdBy`,`salt`, `emailSent`) VALUES ('$eventName', '$eventDescription', '$eventDate', '$eventLocation', '$eventHost', '$isTicketed', '$createdBy', '$salt', 'false')";

// attempt to execute the query
if (mysqli_query($link, $query)) {
    echo "success";
}
else {
    die("Error connecting to database");
}


?>
