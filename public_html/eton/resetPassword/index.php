<html>

    <head>

        <!-- a title for the webpage -->
        <title>Edutix- Reset Your Password</title>

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
