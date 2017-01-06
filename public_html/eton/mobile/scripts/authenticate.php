<?php

require_once "/home/sites/edutix.com/config/config.php";

// start a session that will authenticate the user
if (session_id() == "") {
    session_start();
}

// first check if all of the user's info is present
if (empty($_SESSION["userID"]) or empty($_SESSION["privateKey"]) ) {

    // deny authentication
    session_unset();
    session_destroy();
    die("false");
}

// check if the user is registered
// connect to our database
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {
    // deny authentication
    session_unset();
    session_destroy();
    die("false");
};

// format the email
$formatted = mysqli_real_escape_string($link, $_SESSION['userID']);

// check if the username is already taken
$usernameTakenQuery = "SELECT `email` FROM `etonUsers` WHERE `publicKey` = '".$formatted."'";

// execute the query
if ($result = mysqli_query($link, $usernameTakenQuery)) {

  // get the result
  $data = mysqli_fetch_array($result);

  // check if the email is already taken
  if (empty($data)) {
      // deny authentication
      session_unset();
      session_destroy();
      die("false");
  }

}
else {
    // deny authentication
    session_unset();
    session_destroy();
    die("false");
}

// check if the private key matches the public key
// generate some data
$data = mt_rand();

// encrypt it using the public key
openssl_public_encrypt($data, $encryptedData, $_SESSION["userID"]);

// decrypt
openssl_private_decrypt($encryptedData, $decryptedData, $_SESSION["privateKey"]);

// check if the values correspond
if ($data != $decryptedData) {

    // deny authentication
    session_unset();
    session_destroy();
    die("false");
}

die("true");

?>
