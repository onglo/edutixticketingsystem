<html>

    <head>

        <!-- a title for the webpage -->
        <title>Edutix - Home</title>

        <!-- bootstrap css-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">

    </head>
    <body>

        <!-- a script to see if the user is logged in -->
        <?php

        // start a session
        session_start();

        // redirect function
        function deniedRedirect() {
            // unset the session
            session_unset();

            // redirect the user and kill the script
            header("Location: /edutix.com/eton/acessDenied");
            exit();
        }

        // first check if all of the user's info is present
        if (empty($_SESSION["ip"]) or empty($_SESSION["userID"]) or empty($_SESSION["privateKey"]) ) {

            // redirect the user
            deniedRedirect();
        }

        // check if the user's ip is good
        if ($_SERVER["REMOTE_ADDR"] != $_SESSION["ip"]) {
            // redirect the user
            deniedRedirect();
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
            deniedRedirect();
        }

        ?>

        

        <!-- link to jquery, tether and bootstrap-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>
    </body>
</html>
