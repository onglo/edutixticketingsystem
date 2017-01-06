<?php

require_once "/home/sites/edutix.com/config/config.php";

// attempt to link to the database
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {
  // kill the script
  die("Error connecting to database");
};

// format the email
$formattedEmail = encryptEmail($_POST["username"]);
$formattedEmail = mysqli_real_escape_string($link, $formattedEmail);

// check if this account exists
$query = "SELECT `salt`,`emailConfirmation`,`password`,`privateKey`,`publicKey` FROM `etonUsers` WHERE `email` = '".$formattedEmail."'";

// get the data
if ($result = mysqli_query($link, $query)) {

    // get the user's salt
    $data = mysqli_fetch_array($result);

    // check if this account even existed
    if (empty($data)) {
      die("Email or password incorrect");
    }

    // if the account exists check pssword
    if (empty($errors)) {

        // check if this account's email has been confirmed
        if ($data["emailConfirmation"] != "true") {
            die("Your email needs to be confirmed before you can login");
        }

        // check if the pssword is correct
        $userKey = encryptPassword($_POST["password"], $data["salt"]);

        // password is false
        if ($userKey != $data["password"]) {

            // throw this as an error
            die("Email or password incorrect");

        }
        //password is good
        else {

            // start a session that will authenticate the user
            session_start();
            session_destroy();
            session_start();

            // next store the public key for identification
            $_SESSION["userID"] = $data["publicKey"];

            // store the private key for authentication
            $_SESSION["privateKey"] = decryptPrivate($data["privateKey"], $_POST["passwordInput"]);

            echo("success");

        }
    }
}

?>
