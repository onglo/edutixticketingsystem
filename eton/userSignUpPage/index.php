<html>

  <head>

      <!-- a title for the webpage -->
      <title>Sign up</title>

      <!-- bootstrap css-->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">

      <!-- link to the page's stylesheet (DISABLED FOR DEVELOPMENT)
      <link rel="stylesheet" href="style.css" />-->

  </head>

  <body>

    <!-- a container div -->
    <div id="container">

      <!-- a container for the sign up box -->
      <div id="signUpContainer">

        <!-- a label telling the user to sign up-->
        <p style="color:white;text-align:center">Sign Up</p>

      </div>

      <!-- a banner that will be populated if there are any errors in signing up the user -->
      <div class="alert alert-danger" style="display:none;" role="alert" id="alert">
      </div>

      <!-- a form where they can sign up -->
      <form method="post">

        <!-- a form group for the user info -->
        <div class="form-group">

            <!-- a label telling the user to input their first name -->
            <label for="firstNameInput">First Name:</label>

            <!--an input to input the users's first name -->
            <input type="text" class="form-control" name="firstName" id="firstNameInput" placeholder="Enter First Name" />

            <!-- a label telling the user to input their last name -->
            <label for="lastNameInput">Last Name:</label>

            <!--an input to input the users's second name -->
            <input type="text" class="form-control "name="lastName" id="lastNameInput" placeholder="Enter Last Name" />

        </div>

        <!-- a form group for the email input -->
        <div class="form-group">

          <!-- a label telling them to input their email -->
          <label for="emailInput">Your Eton Email:</label>

          <!-- username input -->
          <input type="email" name="emailInput" id="emailInput" class="form-control" type="text" placeholder="Enter Email" />

        </div>

        <!-- a form group for the password input -->
        <div class="form-group">

          <!-- a label telling them to input their password -->
          <label for="passwordInput">Password:</label>

          <!-- password Input-->
          <input name="passwordInput" id="passwordInput" class="form-control" type="password" placeholder="Enter Password" />

          <!-- tell the user their password has to be atleast 6 characters -->
          <small id="passwordInfo" class="form-text text-muted">Your password must be at least 6 characters long</small>

          <!-- a label telling them to confirm their password -->
          <label for="passwordInput">Confirm Password:</label>

          <!-- confirm password Input Input-->
          <input name="confirmPasswordInput" id="confirmPasswordInput" class="form-control" type="password" placeholder="Enter Password" />

        </div>

        <!-- a login button -->
        <button id="submitButton" method="post" class="btn btn-primary">Submit</button>
      </form>

      <!-- a link where the user can sign up if they don't have an account-->
      <a href="www.google.co.uk" id="signUpLink">Already have an account?</a>

    </div>

    <!-- link to jquery, tether and bootstrap-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>

    <!-- php -->
    <?php

    include("/home/sites/edutix.com/config/config.php");

    // check if the user has submitted data
    if (!empty($_POST)) {

      // an array that will hold any errors
      $errors = "";

      // a list of the name of all of the forms
      $forms = array("firstName", "lastName", "emailInput", "passwordInput", "confirmPasswordInput");

      // loop through each form
      foreach ($forms as $value) {

        // check if it is empty
        if (empty($_POST[$value])) {

          // add this as an error
          $errors .= ".One or more of the fields are empty";
          // break out of the loop
          break;
        }
      }

      // check if passwords match
      if ($_POST["passwordInput"] != $_POST["confirmPasswordInput"]) {
        $errors .= ".The passwords don't match";
      }

      // check if an eton email was not used
      preg_match("/@etoncollege.org.uk/", $_POST['emailInput'], $out);
      if (empty($out) && !empty($_POST['emailInput'])) {

        // add this as an error
        $errors .= '.This is not an Eton email address';
      }

      // check if the password is atleast 6 characters
      if (strlen($_POST['passwordInput']) < 6 && !empty($_POST['passwordInput'])) {

        // add this as an error
        $errors .= '.Your password is not long enough';

      }
      // connect to the db if there are no errors
      if (empty($errors)) {
        // connect to our database (host, database username, database password, database name)
        $link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

        // check if the connection was succesful
        if(mysqli_connect_error()) {
          // kill the script
          die("Error connecting to database");
        };

        // format the email
        $formatted = encryptEmail(mysqli_real_escape_string($link, $_POST['emailInput']));

        // check if the username is already taken
        $usernameTakenQuery = "SELECT `email` FROM `etonUsers` WHERE `email` = '".$formatted."'";

        // execute the query
        if ($result = mysqli_query($link, $usernameTakenQuery)) {

          // get the resourcebundle_count
          $data = mysqli_fetch_array($result);

          // check if the email is already taken
          if (!empty($data)) {
            $errors .= ".Email already registered";
          }

        }
        else {
          die("Error connecting to database");
        }

        // if there are still no errors sign up the user
        if (empty($errors)) {

          // generate a public and private key
          $keyPair = openssl_pkey_new();

          // get the private key
          openssl_pkey_export($keyPair, $privateKey);

          // get the public key
          $publicKey = openssl_pkey_get_details($keyPair);
          $publicKey = $publicKey["key"];

          // generate a unique salt for the user
          $userSalt = mt_rand();

          // encrypt the private key
          $encryptedPrivateKey = encryptPrivate($privateKey, mysqli_real_escape_string($link, $_POST["passwordInput"]), $userSalt);

          // encrypt the password
          $encryptedPassword = encryptPassword(mysqli_real_escape_string($link, $_POST["passwordInput"]), $userSalt);

          // encrypt their data
          openssl_public_encrypt(mysqli_real_escape_string($link, $_POST["firstName"]), $firstNameEncrypted, $publicKey);
          openssl_public_encrypt(mysqli_real_escape_string($link, $_POST["lastName"]), $lastNameEncrypted, $publicKey);
          $emailEncrypted = encryptEmail(mysqli_real_escape_string($link, $_POST["emailInput"]));

          // escape all the data we will use
          $firstNameEncrypted = mysqli_real_escape_string($link, $firstNameEncrypted);
          $lastNameEncrypted = mysqli_real_escape_string($link, $lastNameEncrypted);
          $emailEncrypted = mysqli_real_escape_string($link, $emailEncrypted);
          $encryptedPassword = mysqli_real_escape_string($link, $encryptedPassword);
          $userSalt = mysqli_real_escape_string($link, $userSalt);
          $encryptedPrivateKey = mysqli_real_escape_string($link, $encryptedPrivateKey);
          $publicKey = mysqli_real_escape_string($link, $publicKey);

          // prepare a query to insert their database
          $query = "INSERT INTO `cl11-main-rh8`.`etonUsers` (`firstName`, `lastName`, `email`, `password`, `salt`, `privateKey`, `publicKey`) VALUES ('$firstNameEncrypted', '$lastNameEncrypted', '$emailEncrypted', '$encryptedPassword', '$userSalt', '$encryptedPrivateKey', '$publicKey')";

          // execute the query
          if (mysqli_query($link, $query)) {

            // redirect the user to a page telling them they were signed up but that they need to confirm their email
            header("Location: confirmationPage");

            exit();

          }
          // if the query was not executed kill the script
          else {
            die("Error connecting to database".mysqli_connect_error());
          }
        }
      }
    }

    ?>

    <!-- javascript -->
    <script type="text/javascript">

    // check if there are any errors
    var errors = "<?php echo $errors ?>";
    if(errors) {
      // SPLIT THE ERRORS AND ADD LINE BREAKS
      errors = errors.replace(".", "<br />&#8226 ")
      errors = errors.replace(".", "<br />&#8226 ")
      errors = errors.replace(".", "<br />&#8226 ")
      errors = errors.replace(".", "<br />&#8226 ")
      errors = errors.replace(".", "<br />&#8226 ")

      // if there are add them to the div
      $('#alert').html("<strong>Your account could not be created for the following reasons: </strong>" + errors);

      // make the error visible
      $('#alert').css("display", "inherit")
    }
    </script>

  </body>

</html>
