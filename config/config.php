<?php

$dbUsername = "cl11-main-rh8";
$dbPassword = "tWdUeUMNB";
$dbServer = "localhost";

// the secret key for reCAPTCHA
$reCAPTCHA = "6Lc38A8UAAAAAGtxlsslgQ6suFCT-u0xZECx00LJ";

// a function to encrypt a user's private key
function encryptPrivate($privateKeyInput, $passwordInput, $userSaltInput) {

  // hash the private key
  $encryptedPrivate = md5($privateKeyInput.$passwordInput.$userSaltInput);

  // return this value
  return $encryptedPrivate;
}

// a function to encrypt passwords
function encryptPassword($passwordToEncrypt, $userSaltInput) {

  $encrypted = crypt($passwordToEncrypt.$userSaltInput);

  // return this value
  return $encrypted;

}

// a function to encrypt emails
function encryptEmail($targetEmail) {
    // encrypt the email and return it
    $targetEmail = md5($targetEmail."W6zl5BGXAylZpOCA25El");
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

?>
