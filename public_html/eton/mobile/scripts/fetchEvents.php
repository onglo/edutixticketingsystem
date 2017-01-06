<?php

require_once "/home/sites/edutix.com/config/config.php";

// connect to our database
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {

    die("error");

};

// prepare a query that will get the first 20 events
$query = "SELECT `id`, `eventName`, `eventDesc`, `eventDate`, `eventLocation`, `eventHost`, `createdBy`, `salt` FROM `etonEvents` ORDER BY `eventDate` LIMIT ".$_POST['startOffset'].", 20";

// attempt to execute the query
if ($result = mysqli_query($link, $query)) {

    // initialise our array of data
    $resultData = array();

    // check if we have data
    if ($data = mysqli_fetch_array($result)) {

        // decrypt the data
        $temp = array();
        array_push($temp, $data["id"]);
        array_push($temp, decryptEventData($data["salt"], $data["eventName"]));
        array_push($temp, decryptEventData($data["salt"], $data["eventDesc"]));
        array_push($temp, $data["eventDate"]);
        array_push($temp, decryptEventData($data["salt"], $data["eventLocation"]));
        array_push($temp, decryptEventData($data["salt"], $data["eventHost"]));

        // add this data to the array
        array_push($resultData, $temp);

        // check if there is any more data
        while ($data = mysqli_fetch_array($result)) {

            // decrypt the data
            $temp = array();
            array_push($temp, $data["id"]);
            array_push($temp, decryptEventData($data["salt"], $data["eventName"]));
            array_push($temp, decryptEventData($data["salt"], $data["eventDesc"]));
            array_push($temp, $data["eventDate"]);
            array_push($temp, decryptEventData($data["salt"], $data["eventLocation"]));
            array_push($temp, decryptEventData($data["salt"], $data["eventHost"]));

            // add this data to the array
            array_push($resultData, $temp);
        }
        echo (json_encode($resultData));

    }
    else {
        echo "none";
    }

}
else {
    die("error");
}
?>
