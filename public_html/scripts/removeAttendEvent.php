<?php

include("/home/sites/edutix.co.uk/config/config.php");

// start a session that will authenticate the user
if (session_id() == "") {
    session_start();
}

// attempt to link to the db
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {
  // kill the script
  die("Error connecting to database");
};

// prepare a query that will get the people going to the event so that we can update it
$query = "SELECT `idOfPeopleGoing` FROM `etonEvents` WHERE `id` = ".mysqli_real_escape_string($link, $_POST['id']);

// attempt the query
if ($result = mysqli_query($link, $query)) {

    // get the data
    $data = mysqli_fetch_array($result);

    // store the values we need
    $listOfPeople = $data["idOfPeopleGoing"];

}
else {
    die();
}

// put the ids of people going in an array
$listOfPeopleArray = explode(".", $listOfPeople);

// put the user's list of events that they are going to in an Array
$userListOfEvents = explode(".", $_SESSION["events"]);

// initialise the new strings that we are gonig to creates
$newListOfPeopleString = "";
$newUserListOfEvents = "";

// loop through the list of people array
for ($counter = 0; $counter < sizeof($listOfPeopleArray); $counter++) {

    // check if this is the id of the person that needs to be deleted
    if ($listOfPeopleArray[$counter] == $_SESSION["idNumber"]) {

        // delte this value from the array
        unset($listOfPeopleArray[$counter]);

    }
    else if ($listOfPeopleArray[$counter] != "") {

        // add this to the string
        $newListOfPeopleString .= ".".$listOfPeopleArray[$counter];
    }
}

// loop through the user's list of events that they are attending
for ($counter = 0; $counter < sizeof($userListOfEvents); $counter++) {

    // check if this event is the one that needs to be deleted from the list
    if ($userListOfEvents[$counter] == $_POST["id"]) {

        // delete this value from the list
        unset($userListOfEvents[$counter]);
    }
    else if ($userListOfEvents[$counter] != "") {

        // add this to the new string
        $newUserListOfEvents .= ".".$userListOfEvents[$counter];
    }
}

// prepare a query that will update the event details with the new ids of people gonig
$updateEventQuery = "UPDATE `etonEvents` SET `idOfPeopleGoing` = '".mysqli_real_escape_string($link, $newListOfPeopleString)."', `numberOfPeopleGoing` = `numberOfPeopleGoing` - 1 WHERE `id` = ".mysqli_real_escape_string($link, $_POST["id"]);

// prepare a query that will update the user's database
$updateUserQuery = "UPDATE `etonUsers` SET `eventList` = '".mysqli_real_escape_string($link, $newUserListOfEvents)."' WHERE `id` = ".mysqli_real_escape_string($link, $_SESSION["idNumber"]);

// attempt to update the user's db
if (mysqli_query($link, $updateUserQuery)) {

    // update the session
    $_SESSION["events"] = $newUserListOfEvents;

    // attempt to update the event database
    if (mysqli_query($link, $updateEventQuery)) {
        echo "success";
    }
    else {
        die();
    }
}
else {
    die();
}



?>
