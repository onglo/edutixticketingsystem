<html>

  <head>

      <!-- a title for the webpage -->
      <title>Edutix - Confirm Your Email</title>

      <!-- bootstrap css-->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">


  </head>
  <body>

      <!-- php -->
      <?php

      include("/home/sites/edutix.co.uk/config/config.php");

      // link to the db
      $link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbUsername);

      // check if the connection was succesful
      if(mysqli_connect_error()) {
        // kill the script
        die("Error connecting to database");
      };

      // figure out the email of the confirmation request
      $emailToConfirm = $_GET["id"];

      // prepare a query that will check if the id needs to be checked
      $query = "SELECT `emailConfirmation` FROM `etonUsers` WHERE `emailConfirmation` = '".$emailToConfirm."'";

      // execute this query
      if ($result = mysqli_query($link, $query)) {

          // get the data from this query
          $data = mysqli_fetch_array($result);

          // check if this email needs to be verified
          if (!empty($data)) {

              // prepare a query to update the email
              $query = "UPDATE `etonUsers` SET `emailConfirmation` = 'true' WHERE `emailConfirmation` = '$emailToConfirm'";

              // execute the query
              if ($secondResult = mysqli_query($link, $query)) {
                  // tell the user their email was verified
                  echo "<h2>Email Successfully Verified!</h2>";
              }
              else {
                  die("Error connecting to database");
              }

          }
          else {
              echo "<h2>Error validating email</h2>";
          }

      }
      else {
          die("Error connecting to database");
      }

      ?>

      <!-- link to jquery, tether and bootstrap-->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>
  </body>
</html>
