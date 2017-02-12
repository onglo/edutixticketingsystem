<html>

    <head>

        <!-- a title for the webpage -->
        <title>Edutix - Reset Your Password</title>

        <!-- bootstrap css-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">

    </head>
    <body>

        <!-- php script that will check if the url is valid -->
        <?php

        include("/home/sites/edutix.co.uk/config/config.php");

        // first check if the values are present
        if (empty($_GET["email"]) or empty($_GET["token"])) {
            // if they aren't redirect the user
            header("Location: error");
        }

        // connect to db
        $link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

        // see if there was an error
        if (mysqli_connect_error()) {
            // kill the script
            die("Error connecting to database");
        }

        // check to see if the email and token are valid
        $query = "SELECT `token`,`salt` FROM `etonUsers` WHERE `email` = '".mysqli_real_escape_string($link, rawurldecode($_GET["email"]))."' AND `resetLink` = '".mysqli_real_escape_string($link, rawurldecode($_GET["token"]))."'";

        //attempt to do this query
        if ($result = mysqli_query($link, $query)) {

            // fetch the data
            $data = mysqli_fetch_array($result);

            // check if there is data
            if (empty($data)) {
                header("Location: error");
            }

            // check if the url is still valid
            $currentValue = deToken($_GET["token"],$data["token"]);
            $currentValue = (int) $currentValue;

            if ((time() - $currentValue) > 86400) {
                header("Location: error");
            }
        }
        else {
            die("Error connecting to database");
        }

        ?>

        <!-- a label for the page -->
        <h2>Reset Your Password</h2>

        <!-- a banner that will be populated if there are any errors in signing up the user -->
        <div class="alert alert-danger" style="display:none;" role="alert" id="alert">
        </div>

        <!-- a container for our form -->
        <div class="container">

            <!-- a form where the user can submit their new password -->
            <form method="post">

                <!-- a form group for the password inputs-->
                <div class="form-group">

                    <!-- a label telling the user to enter their new pssword -->
                    <label for="passwordInput">Please enter your new password:</label>

                    <!-- an input for the password -->
                    <input id="passwordInput" class="form-control" name="passwordInput" placeholder="Enter password here" type="password" />

                    <!-- small text saying the password has to be at lead 6 characthers long -->
                    <small id="passwordInfo" class="form-text text-muted">Your password must be at least 6 characters long</small>

                    <!-- a label telling them to confirm their password -->
                    <label for="confirmPasswordInput">Confirm Password:</label>

                    <!-- confirm password Input Input-->
                    <input name="confirmPasswordInput" id="confirmPasswordInput" class="form-control" type="password" placeholder="Enter Password" />
                </div>

                <!-- a form group where the user has to re enter their first and last name -->
                <div class="form-group">

                    <!-- a label telling the user what to do -->
                    <label for="firstNameInput">As your data needs to be stored according to your new password, please re-enter your first and last name:</label>

                    <!-- an input for the first name -->
                    <input type="text" name="firstNameInput" id="firstNameInput" placeholder="Enter first name here" class="form-control" />

                    <!-- an input for the second name -->
                    <input type="text" name="lastNameInput" id="lastNameInput" placeholder="Enter last name here" class="form-control" />

                </div>

                <!-- a submit button -->
                <button type="submit" method="post" class="btn btn-primary">Submit</button>

            </form>

        </div>

        <!-- link to jquery, tether and bootstrap-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>

        <!-- main php script -->
        <?php

        // initialise an array that will keep track of any errors
        $errors = array();

        // check if data has been posted
        if (!empty($_POST)) {

            // check if any of the fieds are empty
            if (empty($_POST["passwordInput"]) or empty($_POST["confirmPasswordInput"]) or empty($_POST["firstNameInput"]) or empty($_POST["lastNameInput"])) {
                // add this as an error
                $errors .= ".One or more of the fields are empty";
            }

            if (empty($errors)) {

                // check if the passwords match
                if ($_POST["passwordInput"] != $_POST["confirmPasswordInput"]) {
                    $errors .= ".The passwords don't match";
                }
                else {
                    // check if the psswords are at least 6 chars long
                    if (strlen($_POST["passwordInput"]) < 6) {
                        $errors .= ".Your password isn't long enough";
                    }
                }

                // if everything is good, reset the user's password
                if (empty($errors)) {

                    // generate a public and private key
                    $keyPair = openssl_pkey_new();

                    // get the private key
                    openssl_pkey_export($keyPair, $privateKey);

                    // get the public key
                    $publicKey = openssl_pkey_get_details($keyPair);
                    $publicKey = $publicKey["key"];

                    // encrypt the private key
                    $encryptedPrivateKey = encryptPrivate($privateKey, mysqli_real_escape_string($link, $_POST["passwordInput"]));

                    // encrypt the password
                    $encryptedPassword = encryptPassword($_POST["passwordInput"], $data["salt"]);

                    // encrypt their data
                    openssl_public_encrypt(mysqli_real_escape_string($link, $_POST["firstNameInput"]), $firstNameEncrypted, $publicKey);
                    openssl_public_encrypt(mysqli_real_escape_string($link, $_POST["lastNameInput"]), $lastNameEncrypted, $publicKey);

                    // escape all the data we will use
                    $firstNameEncrypted = mysqli_real_escape_string($link, $firstNameEncrypted);
                    $lastNameEncrypted = mysqli_real_escape_string($link, $lastNameEncrypted);
                    $encryptedPassword = mysqli_real_escape_string($link, $encryptedPassword);
                    $encryptedPrivateKey = mysqli_real_escape_string($link, $encryptedPrivateKey);
                    $publicKey = mysqli_real_escape_string($link, $publicKey);

                    // prepare the query
                    $resetPasswordQuery = "UPDATE `etonUsers` SET `firstName` = '".$firstNameEncrypted."', `lastName` = '".$lastNameEncrypted."', `password` = '".$encryptedPassword."', `privateKey` = '".$encryptedPrivateKey."', `publicKey` = '".$publicKey."', `token` = '', `resetLink` = '' WHERE `email` = '".mysqli_real_escape_string($link, rawurldecode($_GET["email"]))."' AND `resetLink` = '".mysqli_real_escape_string($link, rawurldecode($_GET["token"]))."'";

                    // execute the query
                    if (mysqli_query($link, $resetPasswordQuery)) {

                        // redirect the user to a page telling them it was a success
                        header("Location: success");
                    }
                    else {
                        die("Error connecting to database");
                    }


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
