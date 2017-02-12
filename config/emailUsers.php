<?php

require_once "/home/sites/edutix.co.uk/config/config.php";

// connect to our database
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {

    die();

};


// prepare a query that will fetch events in the next hour that need emails sent
$query = "SELECT * FROM `etonEvents` WHERE `eventDate` BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 HOUR) AND `emailSent` = 'false'";

// try to execute this query
if ($result = mysqli_query($link, $query)) {
    // loop through each event that we need to send an email to
    while ($data = mysqli_fetch_array($result)) {

        // decrypt an the event name and title
        $eventName = decryptEventData($data["salt"], $data["eventName"]);
        $eventLocation = decryptEventData($data["salt"], $data["eventLocation"]);

        // prepare a query that will update the email
        $updateQuery = "UPDATE `etonEvents` SET `emailSent` = 'true' WHERE `id` = ".$data["id"];

        // execute this query
        if (mysqli_query($link, $updateQuery)) {

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
                    $message = "Hello,\r\n\r\nJust a quick email to let you know that an event your going to, ".$eventName.", is starting in less than an hour. The event location is: ".$eventLocation.".\r\n\r\nMany Thanks,\r\nThe Edutix Team";

                    // mail the message
                    mail(decryptEmail($userEmail["email"]), $eventName." reminder", $message, "'From:hello@Edutix.co.uk' . '\r\n'");

                    echo "emailSent";
                }
            }
        }
    }
}
else {
    die();
}

?>
