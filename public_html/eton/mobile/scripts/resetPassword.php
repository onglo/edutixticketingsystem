<?php

require_once "/home/sites/edutix.com/config/config.php";

$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// see if there was an error
if (mysqli_connect_error()) {
    // kill the script
    die("Error connecting to database");
}

// format the email so that we can search for it in the db
$formattedSearch = encryptEmail($_POST["emailInput"]);
$formattedSearch = mysqli_real_escape_string($link, $formattedSearch);

// prepare a query to search for it
$query = "SELECT `token`,`resetLink`,`email` FROM `etonUsers` WHERE `email` = '".$formattedSearch."'";

// get the data
if ($result = mysqli_query($link, $query)) {

    $data = mysqli_fetch_array($result);

    // check if this is empty
    if (empty($data)) {
        // add this as an error (email doesn't exist)
        die("notvalid");
    }
    // check if a token has already been generated
    elseif (!empty($data["token"])) {
        // check if this token is still valid
        $value = deToken($data["resetLink"],$data["token"]);

        // make this value an int
        $value = (int) $value;

        // get the current time
        $currentTime = time();

        // figure out if the token is still valid
        if (($currentTime - $value) < 86400) {
            // the token is still valid and so return this as an error
            die("alreadyrequested");
        }
    }
    // if there are no errors reset the user's pssword
    if (empty($errors)) {

        // get the token for the user
        $userToken = generateToken();
        $userToken[0] = mysqli_real_escape_string($link, $userToken[0]);
        $userToken[1] = mysqli_real_escape_string($link, $userToken[1]);

        // prepare a query that will store the token in the db
        $query = "UPDATE `etonUsers` SET `token` = '".$userToken[0]."',`resetLink` = '".$userToken[1]."' WHERE `email` = '".$formattedSearch."'";

        // execute the query
        if ($result = mysqli_query($link, $query)) {

            // a message that will be sent to the user
            $message = "Hello,\r\n\r\nA request has been made to reset the password to your account. To do so, please visit the link below:\r\nhttp://79.170.40.38/edutix.com/eton/resetPassword/reset?email=".rawurlencode($data["email"])."&token=".rawurlencode($userToken[1])."\r\n\r\nIf you didn't request a password change, please ignore this email.\r\n\r\nMany Thanks,\r\nThe Edutix Team";

            // send the email
            mail(decryptEmail($data["email"]), 'Reset Edutix Password', $message, "'From:hello@Edutix.com' . '\r\n'");

            die("success");
        }
        else {
            die("Error connecting to database");
        }
    }
}
else {
    // kill the script (error)
    die("Error connecting to database");
}

?>
