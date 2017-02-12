<?php

include("/home/sites/edutix.co.uk/config/config.php");

// attempt to link to the db
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {
  // kill the script
  die();
};

// prepare a query that will move the old events from one table to the archive TokyoTyrantTable
$moveQuery = "INSERT INTO `etonEventsArchive` SELECT * FROM `etonEvents` WHERE `eventDate` < NOW() - INTERVAL 1 HOUR";

// prepare a query that will delete the events we just moved
$deleteQuery = "DELETE FROM `etonEvents` WHERE `eventDate` < NOW() - INTERVAL 1 HOUR";

// attept to execute the query
if (mysqli_query($link, $moveQuery)) {

    // attempt to delete the events from the previous table
    if (mysqli_query($link, $deleteQuery)) {
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
