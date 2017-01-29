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

// a query to get the events that the user has hosted that are active
$query = "SELECT `id`, `eventName`, `eventDesc`, `eventDate`, `eventLocation`, `eventHost`, `createdBy`, `salt`,`numberOfPeopleGoing` FROM `etonEvents` WHERE `createdBy` = ".mysqli_real_escape_string($link,$_SESSION["idNumber"])." ORDER BY `eventDate`";

// a query to get the archived events that the user has hosted
$archivedQuery = "SELECT `id`, `eventName`, `eventDesc`, `eventDate`, `eventLocation`, `eventHost`, `createdBy`, `salt`,`numberOfPeopleGoing` FROM `etonEventsArchive` WHERE `createdBy` = ".mysqli_real_escape_string($link, $_SESSION["idNumber"])." ORDER BY `eventDate`";

// attempt to exeucte the first query
if ($result = mysqli_query($link, $query)) {

    // initialise the array that will hold all of the data
    $activeEvents = array();

    // check if there is any data
    if (mysqli_num_rows($result) <= 0) {
        echo "empty";
    }
    else {
        // continue looping while there are still events to load
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

            // put the user's flagged events into array
            $userFlagged = explode(".", $_SESSION["flagged"]);

            // initialise the field in the array
            array_push($temp, "false");
            array_push($temp, "false");

            // loop through each of the events
            foreach ($userEvents as $event) {
                // check if the user is giong to this event
                if ($data["id"] == $event) {
                    $temp[6] = "true";
                }
            }

            // loop through each of the flagged events
            foreach ($userFlagged as $flagged) {

                // check if the user has flageed this event
                if ($data["id"] == $flagged) {
                    $temp[7] = "true";
                }
            }

            // get the number of people going to the event
            array_push($temp, $data["numberOfPeopleGoing"]);

            // add this data to the array
            array_push($activeEvents, $temp);
        }

        // echo this data in a json format
        echo json_encode($activeEvents);
    }
}
else {
    die();
}

// echo a marker that will split the two sets of data appart
echo "`@marker@`";

// next attempt to get the archived events
if ($result = mysqli_query($link, $archivedQuery)) {

    // initialise the array that will hold all of the data
    $archivedEvents = array();

    // check if there is any data
    if (mysqli_num_rows($result) <= 0) {
        echo "empty";
    }
    else {
        // continue looping while there are still events to load
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

            // put the user's flagged events into array
            $userFlagged = explode(".", $_SESSION["flagged"]);

            // initialise the field in the array
            array_push($temp, "false");
            array_push($temp, "false");

            // loop through each of the events
            foreach ($userEvents as $event) {
                // check if the user is giong to this event
                if ($data["id"] == $event) {
                    $temp[6] = "true";
                }
            }

            // loop through each of the flagged events
            foreach ($userFlagged as $flagged) {

                // check if the user has flageed this event
                if ($data["id"] == $flagged) {
                    $temp[7] = "true";
                }
            }

            // get the number of people going to the event
            array_push($temp, $data["numberOfPeopleGoing"]);

            // add this data to the array
            array_push($archivedEvents, $temp);
        }

        // echo this data in a json format
        echo json_encode($archivedEvents);
    }
}
else {
    die();
}

?>
