<?php

require_once "/home/sites/edutix.com/config/config.php";

// connect to our database
$link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

// check if the connection was succesful
if(mysqli_connect_error()) {
    // kill the script
    die("Error connecting to database");
};

// format the email
$formatted = encryptEmail(mysqli_real_escape_string($link, $_POST['emailInput']));

// check if the username is already taken
$usernameTakenQuery = "SELECT `email` FROM `etonUsers` WHERE `email` = '".$formatted."'";

// execute the query
if ($result = mysqli_query($link, $usernameTakenQuery)) {

    // get the result
    $data = mysqli_fetch_array($result);

    // check if the email is already taken
    if (!empty($data)) {
        die("emailtaken");
    }
}
else {
    die("Error connecting to database");
}

// if everything is good sign up the user

// generate a public and private key
$keyPair = openssl_pkey_new();

// get the private key
openssl_pkey_export($keyPair, $privateKey);

// get the public key
$publicKey = openssl_pkey_get_details($keyPair);
$publicKey = $publicKey["key"];

// generate a unique salt for the user
$length = 32;
$secure = true;
$userSalt = openssl_random_pseudo_bytes($length, $secure);

// encrypt the private key
$encryptedPrivateKey = encryptPrivate($privateKey, mysqli_real_escape_string($link, $_POST["passwordInput"]));

// encrypt the password
$encryptedPassword = encryptPassword($_POST["passwordInput"], $userSalt);

// encrypt their data
openssl_public_encrypt(mysqli_real_escape_string($link, $_POST["firstName"]), $firstNameEncrypted, $publicKey);
openssl_public_encrypt(mysqli_real_escape_string($link, $_POST["lastName"]), $lastNameEncrypted, $publicKey);
$emailEncrypted = encryptEmail(mysqli_real_escape_string($link, $_POST["emailInput"]));

// generate the email confirmation link for the user
$emailConfirmationLink = emailConfirmationURL();

// escape all the data we will use
$firstNameEncrypted = mysqli_real_escape_string($link, $firstNameEncrypted);
$lastNameEncrypted = mysqli_real_escape_string($link, $lastNameEncrypted);
$emailEncrypted = mysqli_real_escape_string($link, $emailEncrypted);
$encryptedPassword = mysqli_real_escape_string($link, $encryptedPassword);
$userSalt = mysqli_real_escape_string($link, $userSalt);
$encryptedPrivateKey = mysqli_real_escape_string($link, $encryptedPrivateKey);
$publicKey = mysqli_real_escape_string($link, $publicKey);
$emailConfirmationLink = mysqli_real_escape_string($link, $emailConfirmationLink);

// store the first name for the email
$firstName = $_POST["firstName"];

// prepare a query to insert their database
$query = "INSERT INTO `cl11-main-rh8`.`etonUsers` (`firstName`, `lastName`, `email`, `password`, `salt`, `privateKey`, `publicKey`, `emailConfirmation`) VALUES ('$firstNameEncrypted', '$lastNameEncrypted', '$emailEncrypted', '$encryptedPassword', '$userSalt', '$encryptedPrivateKey', '$publicKey', '$emailConfirmationLink')";

// execute the query
if (mysqli_query($link, $query)) {

    // prepare a msg to send to the user
    $message = "Dear ".$firstName.",\r\n\r\nThank you for signing up to Edutix!\r\n\r\nPlease confirm your email address by visiting the link below:\r\nhttp://79.170.40.38/edutix.com/eton/userSignUpPage/confirmEmail?id=".rawurlencode($emailConfirmationLink)."\r\n\r\nMany Thanks,\r\nThe Edutix Team";

    // send the email
    mail($_POST["emailInput"], 'Welcome to Edutix!', $message, "'From:hello@Edutix.com' . '\r\n'");

    // redirect the user to a page telling them they were signed up but that they need to confirm their email
    die("success");

    exit();

}
// if the query was not executed kill the script
else {
    die("Error connecting to database");
}

?>
