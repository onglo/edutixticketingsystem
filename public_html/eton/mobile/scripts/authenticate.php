<?php
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
