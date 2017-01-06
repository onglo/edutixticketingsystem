<?php

// logout the user by destryoing their session
session_unset();
if (session_id() != ""){
    session_destroy();
}

echo "success";

?>
