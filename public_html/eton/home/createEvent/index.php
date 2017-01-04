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

        <!-- link to reCaptcha -->
        <script src='https://www.google.com/recaptcha/api.js'></script>

    </head>
    <body>

        <!-- a script to see if the user is logged in -->
        <?php
        include("/home/sites/edutix.com/config/config.php");
        authenticateUser();
        ?>

        <!-- a title for the page -->
        <h2>Host an event</h2>

        <!-- a banner that will be populated if there are any errors in signing up the user -->
        <div class="alert alert-danger" style="display:none;" role="alert" id="alert">
        </div>

        <!-- a form to fill out to host the event -->
        <div class="container">
            <form method="post">

                <!-- a field for the name of the event -->
                <label for="titleInput">What is the name of your event?</label>

                <!-- an input for the name -->
                <input type="text" class="form-control" name="titleInput" placeholder="Enter name here" />

                <!-- a brief description for the event -->
                <label for="descriptionInput">Write a brief description of your event:</label>

                <!-- an input for the description -->
                <textarea style="resize:none;" name="descriptionInput" id="descriptionInput" class="form-control" name="descriptionInput" placeholder="Enter description here" rows="5"/></textarea>

                <!-- a label telling the user to choose a date -->
                <label for="dateInput">When are you hosting your event?</label>

                <!-- a date input -->
                <input name="dateInput" id="dateInput" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d');?>" max="2020-01-17" type="date" class="form-control"/>

                <!-- an input for the location of the event -->
                <label for="locationInput">Where is your event located?</label>

                <!-- location input -->
                <input type="text" class="form-control" placeholder="Enter location here" id="locationInput" name="locationInput"/>

                <!-- a label asking the user what the organiser's name is -->
                <label for="hostName">Who will be hosting this event?</label>

                <!-- an input for the host name -->
                <input id="hostName" name="hostName" placeholder="Enter Name Here" type="text" class="form-control" />

                <!-- a label asking the user if they need tickets for the event -->
                <label for="isTicketed">Is this event ticketed?</label>

                <!-- Inputs for this -->
                <select name="isTicketed" id="isTicketed" class="form-control">
                    <option>No</option>
                    <option>Yes</option>
                </select>
                <br />

                <!-- recaptcha -->
                <div class="g-recaptcha" data-sitekey="6Lc38A8UAAAAAI31hcp9EcihJOx-woqf_WFAOhiT"></div>

                <!-- a submit button -->
                <button type="submit" name="submitButton" method="post" class="btn btn-primary">Create Event!</button>

            </form>
        </div>

        <!-- a PHP script to upload this data -->
        <?php

        // check if the user has submitted data
        if (!empty($_POST["titleInput"]) or !empty($_POST["descriptionInput"]) or !empty($_POST["locationInput"]) or !empty($_POST["hostName"])) {

            // ensure that the user is authenticated
            authenticateUser();

            // initialise an array that will keep track of any errors
            $errors = "";

            // check if any of the fields were empty
            if (empty($_POST["titleInput"]) or empty($_POST["descriptionInput"]) or empty($_POST["dateInput"]) or empty($_POST["locationInput"]) or empty($_POST["hostName"])) {

                // if so add this as an errors
                $errors .= ".One or more of the fields are empty";
            }

            // check if recaptcha was submitted
            if (empty($_POST["g-recaptcha-response"])) {
                $errors .= ".You need to verify that you are human";
            }
            else {
                // check recaptcha is good
                $captcha=$_POST['g-recaptcha-response'];
                $ip = $_SERVER['REMOTE_ADDR'];
                $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$reCAPTCHA."&response=".$captcha."&remoteip=".$ip);
                $responseKeys = json_decode($response,true);
                if(intval($responseKeys["success"]) !== 1) {
                    // add this as an error if recaptcha was failed
                    $errors .= ".reCAPTCHA failed";;
                }
            }

            // check if the date is valid
            if (!empty($_POST["dateInput"])) {

                // get the current date
                $currentDate = new DateTime;

                // check if the date is in the future
                if ($_POST["dateInput"] < date('Y-m-d')) {
                    $errors .= ".Invalid date";
                }
                // check if date is valid
                elseif (!validateDate($_POST["dateInput"])) {
                    $errors .= ".Invalid date";
                    echo validateDate($_POST["dateInput"]);
                }

            }

            // if there are no errors, create ze event
            if (empty($errors)) {

                // attempt to link to the db
                $link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

                // check if the connection was succesful
                if(mysqli_connect_error()) {
                  // kill the script
                  die("Error connecting to database");
                };

                // generate a secret salt for the event
                $length = 32;
                $secure = true;
                $salt = openssl_random_pseudo_bytes($length, $secure);

                // encrypt all of the data for this event
                $eventName = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["titleInput"]));
                $eventDescription = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["descriptionInput"]));
                $eventDate = mysqli_real_escape_string($link, $_POST["dateInput"]);
                $eventLocation = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["locationInput"]));
                $eventHost = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["hostName"]));
                $isTicketed = mysqli_real_escape_string($link, encryptEventData($salt, $_POST["isTicketed"]));
                $createdBy = mysqli_real_escape_string($link, encryptEventData($salt, $_SESSION["userID"]));

                // prepare a query that will insert it into the database
                $query = "INSERT INTO `etonEvents` (`eventName`,`eventDesc`,`eventDate`,`eventLocation`,`eventHost`,`isTicketed`,`createdBy`) VALUES ('$eventName', '$eventDescription', CAST('$eventDate' AS DATE), '$eventLocation', '$eventHost', '$isTicketed', '$createdBy')";
                echo $query;

                // attempt to execute the query
                if (mysqli_query($link, $query)) {
                    header("Location: success");
                }
                else {
                    die("Error connecting to database");
                }

            }

        }

        ?>

        <!-- javascript -->
        <script type="text/javascript">

        // check if there are any errors
        var errors = "<?php echo $errors ?>";

        if(errors.length > 2) {

          // SPLIT THE ERRORS AND ADD LINE BREAKS
          errors = errors.replace(".", "<br />&#8226 ")
          errors = errors.replace(".", "<br />&#8226 ")
          errors = errors.replace(".", "<br />&#8226 ")
          errors = errors.replace(".", "<br />&#8226 ")
          errors = errors.replace(".", "<br />&#8226 ")


          // if there are add them to the div
          $('#alert').html("<strong>We couldn't create your event because: </strong>" + errors);

          // make the error visible
          $('#alert').css("display", "inherit")
        }
        </script>

    </body>
</html>
