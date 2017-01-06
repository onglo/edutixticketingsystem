<?php

// logout the user by destryoing their session
session_unset();
session_destroy();

echo "success";

?>
