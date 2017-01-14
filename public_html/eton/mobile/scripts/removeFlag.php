<?php

include("/home/sites/edutix.com/config/config.php");

// start a session that will authenticate the user
if (session_id() == "") {
    session_start();
}

// put these flagged events in an array
$flagList = explode(".", $_SESSION["flagged"]);

// a str that will be the new flagged list
$newFlagList = "";

// loop through each event
for ($counter = 0; $counter < sizeof($flagList); $counter++) {

    // check if this event is the flag that needs to be delted
    if ($flagList[$counter] == $_POST["id"]) {
        // remove this value from the array
        unset($flagList[$counter]);
    }
    else if ($flagList[$counter] != "") {
        // add this event to the user's new flag list
        $newFlagList .= ".".$flagList[$counter];
    }
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
$userQuery = "UPDATE `etonUsers` SET `flaggedList` = '".mysqli_real_escape_string($link, $newFlagList)."'  WHERE `id` = ".mysqli_real_escape_string($link,$_SESSION['idNumber']);

// attempt this query
if (mysqli_query($link, $userQuery)) {

    echo "success";

    // update the user's session var that keeps track of the events they are registed to
    $_SESSION["flagged"] = $newFlagList;
}
else {
    echo $userQuery;
    die();
}

?>
