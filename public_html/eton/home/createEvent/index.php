<html>

    <head>

        <!-- a title for the webpage -->
        <title>Edutix - Create an event</title>

        <!-- bootstrap css-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
        <!-- link to jquery, tether and bootstrap-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>

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

        <!-- a title for the page -->
        <h2>Host an event</h2>

        <!-- a form to fill out to host the event -->
        <div class="container">
            <form method="post">

                <!-- a field for the name of the event -->
                <label for="titleInput">What is the name of your event?</label>

                <!-- an input for the name -->
                <input type="text" class="form-control" name="titleInput" placeholder="Enter name here" />

                <!-- a brief description for the event -->
                <label for="descriptionInput">Write a brief description for your event:</label>

                <!-- an input for the description -->
                <input type="text" class="form-control" name="descriptionInput" placeholder="Enter description here"/>

                <!-- a label telling the user to choose a date -->
                <label for="dateInput">When are you hosting your event?</label>

                <!-- a date input -->
                <input name="dateInput" id="dateInput" placeholder="mm/dd/yy" class="form-control"/>

                <!-- an input for the location of the event -->
                <label for="locationInput">Where is your event located?</label>

                <!-- location input -->
                <input type="text" class="form-control" placeholder="Enter location here" id="locationInput" />

            </form>
        </div>

    </body>
</html>
