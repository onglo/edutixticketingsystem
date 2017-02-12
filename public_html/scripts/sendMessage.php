<?php

require_once "/home/sites/edutix.co.uk/config/config.php";

// connect to our database
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {

    die();

};

// prepare a query that will fetch events in the next hour that need emails sent.
$query = "SELECT `idOfPeopleGoing` FROM `".$_POST["db"]."` WHERE `id` = '".mysqli_real_escape_string($link, $_POST["eventID"])."' LIMIT 1";

// try to execute this query
if ($result = mysqli_query($link, $query)) {

    $data = mysqli_fetch_array($result);

    // also get a list of the people going so that we can query their emails
    $listOfPeople = str_replace(".", ",", $data["idOfPeopleGoing"]);

    // remove the first comma
    $listOfPeople = substr($listOfPeople, 1);

    // prepare a query that will fetch all of these user's emails
    $emailQuery = "SELECT `email` FROM `etonUsers` WHERE `id` IN (".mysqli_real_escape_string($link, $listOfPeople).")";

    // get the data from this query
    if ($emails = mysqli_query($link, $emailQuery)) {

        // loop through each email
        while ($userEmail = mysqli_fetch_array($emails)) {

            // prepare an email that we will send to the user
            $message = "Hello,\r\n\r\nAn event your going to: ".$_POST["eventName"]." has sent you a message:\r\n\r\n".$_POST["messageToSend"]."\r\n\r\nMany Thanks,\r\nThe Edutix Team";

            // mail the message
            mail(decryptEmail($userEmail["email"]), $_POST["eventName"]." has sent you a message", $message, "'From:hello@Edutix.co.uk' . '\r\n'");
        }
    }
    echo "emailSent";
}
else {
    die();
}

?>
