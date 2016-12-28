<html>

  <head>

      <!-- a title for the webpage -->
      <title>Login</title>

      <!-- bootstrap css-->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">

      <!-- link to the page's stylesheet -->
      <link rel="stylesheet" href="style.css" />

  </head>
  <body>

      <!-- a container div -->
      <div id="container">

        <!-- a container for the login box -->
        <div id="loginContainer">

          <!-- a label telling the user to login-->
          <p style="color:white;text-align:center">Log In</p>

        </div>

        <!-- a form where they can login -->
        <form>

          <!-- a form group for the username input -->
          <div class="form-group">

            <!-- a label telling them to input their username -->
            <label for="usernameInput">Email:</label>

            <!-- username input -->
            <input name="usernameInput" id="usernameInput" class="form-control" type="text" placeholder="Enter Email" />

          </div>

          <!-- a form group for the password input -->
          <div class="form-group">

            <!-- a label telling them to input their password -->
            <label for="passwordInput">Password:</label>

            <!-- password Input-->
            <input name="passwordInput" id="passwordInput" class="form-control" type="password" placeholder="Enter Password" />

          </div>

          <!-- a login button -->
          <button id="submitButton" method="post" class="btn btn-primary">Submit</button>
        </form>

        <!-- a link where the user can sign up if they don't have an account-->
        <a href="www.google.co.uk" id="signUpLink">Don't have an account yet?</a>

        <!-- a link where the user can reset their password -->
        <a href="#" id="resetPassword">Forgot your passsword?</a>

      </div>

      <!-- php -->
      <?php

      // check if the user has pressed the submit button
      echo(fajkd);



      ?>

      <!-- link to jquery, tether and bootstrap-->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>
  </body>
</html>