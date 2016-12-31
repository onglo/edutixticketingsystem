<?php

$dbUsername = "cl11-main-rh8";
$dbPassword = "tWdUeUMNB";
$dbServer = "localhost";

// the secret key for reCAPTCHA
$reCAPTCHA = "6Lc38A8UAAAAAGtxlsslgQ6suFCT-u0xZECx00LJ";

// a function to encrypt a user's private key
function encryptPrivate($privateKeyInput, $passwordInput) {

  // hash the private key
  $encryptedPrivate = openssl_encrypt($privateKeyInput, "AES-128-ECB", $passwordInput);

  // return this value
  return $encryptedPrivate;
}

// a function to decrypt the user's private key
function decryptPrivate($encryptedPrivateInput, $saltInput) {

    $privateKey = openssl_decrypt($encryptedPrivateInput, "AES-128-ECB", $saltInput);
    return $privateKey;

}

// a function to encrypt passwords
function encryptPassword($passwordToEncrypt, $userSaltInput) {

  $encrypted = crypt($passwordToEncrypt,$userSaltInput);

  // return this value
  return $encrypted;

}

// a function to encrypt emails
function encryptEmail($targetEmail) {
    // encrypt the email and return it
    $targetEmail = openssl_encrypt($targetEmail, "AES-128-ECB", "W6zl5BGXAylZpOCA25El");
    return $targetEmail;
}

// a function to decrypt emails
function decryptEmail($targetEmail) {
    $targetEmail = openssl_decrypt($targetEmail, "AES-128-ECB", "W6zl5BGXAylZpOCA25El");
    return $targetEmail;
}

// a function to generate a random string for email confirmation
function emailConfirmationURL() {
    // generate a random value
    $value = mt_rand();

    // md5 it
    $value = md5($value);

    // return this
    return $value;
}

// a function to generate a token to reset passwords
function generateToken() {

    // first get the current time
    $time = time();

    // next generate a random value
    $length = 32;
    $secure = true;
    $random = openssl_random_pseudo_bytes($length, $secure);

    // generate the str to encrypt
    $strToEncrypt = $random.$time;

    // generate the token that will be sent to the user
    $token = openssl_encrypt($strToEncrypt, "AES-128-ECB", $random);

    // return the values
    return array($random, $token);

}

// a function to decrypt the token
function deToken($link, $token) {

    // decrypt the token
    $tokenValue = openssl_decrypt($link , "AES-128-ECB", $token);

    // get the time value
    $timeValue = str_replace($token, "", $tokenValue);

    return $timeValue;
}
?>
