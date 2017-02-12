<?php

include("/home/sites/edutix.co.uk/config/config.php");

// attempt to link to the db
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {
  // kill the script
  die();
};

// prepare a query to fetch the salt for that event
$query = "SELECT `salt`,`idOfPeopleGoing` FROM `".mysqli_real_escape_string($link, $_POST["db"])."` WHERE `id` = ".mysqli_real_escape_string($link, $_POST["eventID"])." LIMIT 1";

// attempt the query
if ($result = mysqli_query($link, $query)) {

    // save this as the salt
    $data = mysqli_fetch_array($result);
    $salt = $data["salt"];

    $peopleGoing = $data["idOfPeopleGoing"];
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

    // check if the user wants to email all of the people going to the event
    if ($_POST["email"] == "true") {

        // get the list of people going to the event
        $listOfPeople = str_replace(".", ",", $peopleGoing);

        // remove the first comma
        $listOfPeople = substr($listOfPeople, 1);

        // prepare a query that will fetch all of these user's emails
        $emailQuery = "SELECT `email` FROM `etonUsers` WHERE `id` IN (".mysqli_real_escape_string($link, $listOfPeople).")";

        // format the message we will give to the users
        $arrayOfChanges = explode("$$$", $_POST["changed"]);

        // initialise the message var
        $messageChange = "";

        // loop through each of these and add them to a big string
        foreach ($arrayOfChanges as $change) {
            if ($change != "") {
                $messageChange .= $change.", ";
            }
        }

        // remove the final comma
        $messageChange = substr($messageChange, 0 , -2);

        // add a full stop
        $messageChange .= ".";

        // get the data from this query
        if ($emails = mysqli_query($link, $emailQuery)) {

            // loop through each email
            while ($userEmail = mysqli_fetch_array($emails)) {

                // prepare an email that we will send to the user
                $message = "Hello,\r\n\r\nJust a quick email to let you know that an event your going to, ".$_POST["eventTitle"].", has changed some things about the event: ".$messageChange."\r\n\r\nMany Thanks,\r\nThe Edutix Team";

                // mail the message
                mail(decryptEmail($userEmail["email"]), $_POST["eventTitle"]." has been updated", $message, "'From:hello@Edutix.co.uk' . '\r\n'");

            }
        }
    }

    echo "success";
}
else {
    echo $userQuery;
    die();
}

?>
