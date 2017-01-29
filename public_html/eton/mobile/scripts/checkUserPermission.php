<?php

// start a session that will authenticate the user
if (session_id() == "") {
    session_start();
}

// check if the user can create events
if ($_SESSION["permission"] == md5($_SESSION["idNumber"].$_SESSION["userID"])) {
    // say they can
    echo "true";
}
else {
    // say then can't
    echo "false";
}

?>
