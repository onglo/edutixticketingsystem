<?php

require_once "/home/sites/edutix.com/config/config.php";

// start a session if there isn't one already
if (session_id() == "") {
    session_start();
}

// connect to our database
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {

    die();

};

// get the events that the user has currently flagged
$flagedEvents = $_SESSION["flagged"];

// check if their list is empty
if ($flagedEvents != "") {

    // replace all of the full stops with commas
    $flagedEvents = str_replace(".", ",",$flagedEvents);

    // remove the first comma
    $flagedEvents = substr($flagedEvents, 1);
    
    // preapre a query that will fetch all of the user's bookmarked events
    $fetchBookmarked = "SELECT `id`, `eventName`, `eventDesc`, `eventDate`, `eventLocation`, `eventHost`, `createdBy`, `salt` FROM `etonEvents` WHERE `id` IN (".mysqli_real_escape_string($link, $flagedEvents).") ORDER BY `eventDate`";

    // attempt to do the query
    if ($result = mysqli_query($link, $fetchBookmarked)) {

        // init an array that will keep all of the data
        $flagedEventData = array();

        // keep on fetching events until there are none left
        while ($data = mysqli_fetch_array($result)) {

            // decrypt the data
            $temp = array();
            array_push($temp, $data["id"]);
            array_push($temp, decryptEventData($data["salt"], $data["eventName"]));
            array_push($temp, decryptEventData($data["salt"], $data["eventDesc"]));
            array_push($temp, $data["eventDate"]);
            array_push($temp, decryptEventData($data["salt"], $data["eventLocation"]));
            array_push($temp, decryptEventData($data["salt"], $data["eventHost"]));

            // put the user's events into an array
            $userEvents = explode(".", $_SESSION["events"]);

            // initialise the field in the array
            array_push($temp, "false");

            // loop through each of the events
            foreach ($userEvents as $event) {
                // check if the user is giong to this event
                if ($data["id"] == $event) {
                    $temp[6] = "true";
                }
            }

            // push a value into the array saing that this event is bookmarked
            array_push($temp, "true");

            // push this data into the main flaged event data array
            array_push($flagedEventData, $temp);
        }

        // return this data
        echo (json_encode($flagedEventData));
    }
    else {
        die();
    }
}
else {
    echo "empty";
}

// echo a marker that will split the two sets of data appart
echo "`@marker@`";

// get all of the events that the user has said that they are going to
$userEvents = $_SESSION["events"];

// check if there are any events
if ($userEvents != "") {

    // replace all of the full stops with commas
    $userEvents = str_replace(".", ",", $userEvents);

    // remove the first comma as this will mess up the query
    $userEvents = substr($userEvents, 1);

    // prepare a query that will select the events
    $fetchUserEvents = "SELECT `id`, `eventName`, `eventDesc`, `eventDate`, `eventLocation`, `eventHost`, `createdBy`, `salt` FROM `etonEvents` WHERE `id` IN (".mysqli_real_escape_string($link, $userEvents).") ORDER BY `eventDate`";

    // attempt to do the query
    if ($result = mysqli_query($link, $fetchUserEvents)) {

        // init the array that will keep track of all of the event data
        $userEventData = array();

        // continue looping while there is data
        while ($data = mysqli_fetch_array($result)) {

            // decrypt the data
            $temp = array();
            array_push($temp, $data["id"]);
            array_push($temp, decryptEventData($data["salt"], $data["eventName"]));
            array_push($temp, decryptEventData($data["salt"], $data["eventDesc"]));
            array_push($temp, $data["eventDate"]);
            array_push($temp, decryptEventData($data["salt"], $data["eventLocation"]));
            array_push($temp, decryptEventData($data["salt"], $data["eventHost"]));
            array_push($temp, "true");

            // put the user's flagged events into array
            $userFlagged = explode(".", $_SESSION["flagged"]);

            // initialise the field in the array
            array_push($temp, "false");

            // loop through each of the flagged events
            foreach ($userFlagged as $flagged) {

                // check if the user has flageed this event
                if ($data["id"] == $flagged) {
                    $temp[7] = "true";
                }
            }

            // push this data into the main array
            array_push($userEventData, $temp);
        }

        // echo this data
        echo (json_encode($userEventData));
    }
    else {
        die();
    }

}
else {
    echo "empty";
}

?>
