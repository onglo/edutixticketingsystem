<html>

  <head>

      <!-- a title for the webpage -->
      <title>Edutix - Login</title>

      <!-- bootstrap css-->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">

      <!-- link to the page's stylesheet DISABLED FOR DEVELOPMENT
      <link rel="stylesheet" href="style.css" />-->

      <!-- recaptcha-->
      <script src='https://www.google.com/recaptcha/api.js'></script>

  </head>
  <body>

      <!-- a container div -->
      <div id="container">

        <!-- a container for the login box -->
        <div id="loginContainer">

          <!-- a label telling the user to login-->
          <p style="color:white;text-align:center">Log In</p>

        </div>

        <!-- a banner that will be populated if there are any errors in signing up the user -->
        <div class="alert alert-danger" style="display:none;" role="alert" id="alert">
        </div>

        <!-- a form where they can login -->
        <form method="post">

          <!-- a form group for the username input -->
          <div class="form-group">

            <!-- a label telling them to input their username -->
            <label for="usernameInput">Email:</label>

            <!-- username input -->
            <input name="usernameInput" type="email" id="usernameInput" class="form-control" type="text" placeholder="Enter Email" />

          </div>

          <!-- a form group for the password input -->
          <div class="form-group">

            <!-- a label telling them to input their password -->
            <label for="passwordInput">Password:</label>

            <!-- password Input-->
            <input name="passwordInput" id="passwordInput" class="form-control" type="password" placeholder="Enter Password" />

          </div>

          <!-- recaptcha -->
          <div class="g-recaptcha" data-sitekey="6Lc38A8UAAAAAI31hcp9EcihJOx-woqf_WFAOhiT"></div>

          <!-- a login button -->
          <button id="submitButton" method="post" type="submit" class="btn btn-primary">Submit</button>
        </form>

        <!-- a link where the user can sign up if they don't have an account-->
        <a href="www.google.co.uk" id="signUpLink">Don't have an account yet?</a>

        <!-- a link where the user can reset their password -->
        <a href="#" id="resetPassword">Forgot your passsword?</a>

      </div>

      <!-- php -->
      <?php

      include("/home/sites/edutix.com/config/config.php");

      // check if the user has submitted data
      if (!empty($_POST["usernameInput"]) or !empty($_POST["passwordInput"])) {

          // an array that will hold all of the error
          $errors = array();

          // make sure that none of the fields are empty
         if (empty($_POST["usernameInput"]) or empty($_POST["passwordInput"])) {
             // add this as an error
             $errors .= ".One or more of the fields are empty";
         }

         // check if recaptcha was submitted
         if (empty($_POST["g-recaptcha-response"])) {
             // add this as an error
             $errors .= ".You need to verify that you are a human!";
         }
         else {
             // check that recaptcha is safe
             // check if the recaptcha is safe
             $captcha=$_POST['g-recaptcha-response'];
             $ip = $_SERVER['REMOTE_ADDR'];
             $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$reCAPTCHA."&response=".$captcha."&remoteip=".$ip);
             $responseKeys = json_decode($response,true);
             if(intval($responseKeys["success"]) !== 1) {
                 // add this as an error if recaptcha was failed
                 $errors .= ".reCAPTCHA failed";;
             }
         }

         // attempt login if there are no errors
         if (empty($errors)) {
            // connect to our database (host, database username, database password, database name)
            $link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

            // check if the connection was succesful
            if(mysqli_connect_error()) {
              // kill the script
              die("Error connecting to database");
            };

            // format the email
            $formattedEmail = encryptEmail($_POST["usernameInput"]);
            $formattedEmail = mysqli_real_escape_string($link, $formattedEmail);

            // check if this account exists
            $query = "SELECT `salt`,`emailConfirmation` FROM `etonUsers` WHERE `email` = '".$formattedEmail."'";

            // get the data
            if ($result = mysqli_query($link, $query)) {

                // get the user's salt
                $data = mysqli_fetch_array($result);

                // check if this account even existed
                if (empty($data)) {
                  $errors .= ".This account doesn't exist";
                }

                // check if this account's email has been confirmed
                if ($data["emailConfirmation"] != "true") {
                    $errors .= ".Your email needs to be confirmed before you can login";
                }

                // if there are no errors check pssword

                print_r($data);

            }
            else {
                die("Error connecting to database");
            }


         }
      }
      ?>

      <!-- link to jquery, tether and bootstrap-->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>

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
        $('#alert').html("<strong>We couldn't log you in because: </strong>" + errors);

        // make the error visible
        $('#alert').css("display", "inherit")
      }
      </script>

  </body>
</html>
