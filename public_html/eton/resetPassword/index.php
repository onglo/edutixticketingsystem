<html>

    <head>

        <!-- a title for the webpage -->
        <title>Edutix - Reset Your Password</title>

        <!-- bootstrap css-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">

        <!-- link to reCaptcha -->
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    <body>

        <!-- a label for the page -->
        <h2>Reset Your Password</h2>

        <!-- a banner that will be populated if there are any errors in signing up the user -->
        <div class="alert alert-danger" style="display:none;" role="alert" id="alert">
        </div>

        <!-- a form where the user can put in their details -->
        <div class="container">
            <form method="post">

                <!-- a form group for the email input -->
                <div class="form-group">

                    <!-- a label telling the user to put in their email -->
                    <label for="emailInput">Please enter your email so that we can find your account:</label>

                    <!-- an input for the email -->
                    <input type="email" class="form-control" name="emailInput" placeholder="Enter Email Here"/>

                    <!-- a small label telling the user that we will send instructions to their email -->
                    <small class="fomr-text text-muted">We will send recovery instructions to your email</small>

                </div>

                <!-- recaptcha -->
                <div class="g-recaptcha" data-sitekey="6Lc38A8UAAAAAI31hcp9EcihJOx-woqf_WFAOhiT"></div>

                <!-- a submit button -->
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
        </div>

        <!-- link to jquery, tether and bootstrap-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>

        <!-- php -->
        <?php

        include("/home/sites/edutix.com/config/config.php");

        // check if the user has submitted data
        if (!empty($_POST["emailInput"])) {

            // initialise an array that will hold any errors that arise
            $errors = array();

            // first verify that the user has submitted recaptcha
            if (empty($_POST["g-recaptcha-response"])) {

                // add this as an error
                $errors .= ".You need to verify that you are a human!";
            }
            else {
                // check if the recaptcha submitted was good
                $captcha = $_POST['g-recaptcha-response'];
                $ip = $_SERVER['REMOTE_ADDR'];
                $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$reCAPTCHA."&response=".$captcha."&remoteip=".$ip);
                $responseKeys = json_decode($response,true);
                if(intval($responseKeys["success"]) !== 1) {
                    // add this as an error if recaptcha was failed
                    $errors .= ".reCAPTCHA failed";;
                }
            }

            // if there are no errors proceed to link to the database
            if (empty($errors)) {

                $link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

                // see if there was an error
                if (mysqli_connect_error()) {
                    // kill the script
                    die("Error connecting to database");
                }

                // format the email so that we can search for it in the db
                $formattedSearch = encryptEmail($_POST["emailInput"]);
                $formattedSearch = mysqli_real_escape_string($link, $formattedSearch);

                // prepare a query to search for it
                $query = "SELECT `token`,`resetLink`,`email` FROM `etonUsers` WHERE `email` = '".$formattedSearch."'";

                // get the data
                if ($result = mysqli_query($link, $query)) {

                    $data = mysqli_fetch_array($result);

                    // check if this is empty
                    if (empty($data)) {
                        // add this as an error (email doesn't exist)
                        $errors .= ".This email is not registered to an account";
                    }
                    // check if a token has already been generated
                    elseif (!empty($data["token"])) {
                        // check if this token is still valid
                        $value = deToken($data["resetLink"],$data["token"]);

                        // make this value an int
                        $value = (int) $value;

                        // get the current time
                        $currentTime = time();

                        // figure out if the token is still valid
                        if (($currentTime - $value) < 86400) {
                            // the token is still valid and so return this as an error
                            $errors .= ".A password reset has already been requested for this account";
                        }
                    }
                    // if there are no errors reset the user's pssword
                    if (empty($errors)) {

                        // get the token for the user
                        $userToken = generateToken();
                        $userToken[0] = mysqli_real_escape_string($link, $userToken[0]);
                        $userToken[1] = mysqli_real_escape_string($link, $userToken[1]);

                        // prepare a query that will store the token in the db
                        $query = "UPDATE `etonUsers` SET `token` = '".$userToken[0]."',`resetLink` = '".$userToken[1]."' WHERE `email` = '".$formattedSearch."'";

                        // execute the query
                        if ($result = mysqli_query($link, $query)) {

                            // a message that will be sent to the user
                            $message = "Hello,\r\n\r\nA request has been made to reset the password to your account. To do so, please visit the link below:\r\nhttp://edutix.co.uk/eton/resetPassword/reset?email=".rawurlencode($data["email"])."&token=".rawurlencode($userToken[1])."\r\n\r\nIf you didn't request a password change, please ignore this email.\r\n\r\nMany Thanks,\r\nThe Edutix Team";

                            // send the email
                            mail(decryptEmail($data["email"]), 'Reset Edutix Password', $message, "'From:hello@edutix.co.uk' . '\r\n'");
                        }
                        else {
                            die("Error connecting to database");
                        }




                    }

                }
                else {
                    // kill the script (error)
                    die("Error connecting to database");
                }
            }
        }

        ?>

        <!-- javascript -->
        <script type="text/javascript">

        // check if there are any errors
        var errors = "<?php echo $errors ?>";

        // remove the 'array put in by php'
        errors = errors.replace("Array", "")

        if(errors.length > 2) {

          // SPLIT THE ERRORS AND ADD LINE BREAKS
          errors = errors.replace(".", "<br />&#8226 ")
          errors = errors.replace(".", "<br />&#8226 ")
          errors = errors.replace(".", "<br />&#8226 ")
          errors = errors.replace(".", "<br />&#8226 ")
          errors = errors.replace(".", "<br />&#8226 ")


          // if there are add them to the div
          $('#alert').html("<strong>Error: </strong>" + errors);

          // make the error visible
          $('#alert').css("display", "inherit")
        }
        </script>
    </body>
</html>
