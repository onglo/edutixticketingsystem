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
        include("/home/sites/edutix.com/config/config.php");
        authenticateUser();
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
                <label for="descriptionInput">Write a brief description of your event:</label>

                <!-- an input for the description -->
                <textarea style="resize:none;" name="descriptionInput" id="descriptionInput" class="form-control" name="descriptionInput" placeholder="Enter description here" rows="5"/></textarea>

                <!-- a label telling the user to choose a date -->
                <label for="dateInput">When are you hosting your event?</label>

                <!-- a date input -->
                <input name="dateInput" id="dateInput" placeholder="mm/dd/yy" type="date" class="form-control"/>

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
                <!-- a submit button -->
                <button type="submit" name="submitButton" method="post" class="btn btn-primary">Create Event!</button>

            </form>
        </div>

        <!-- a PHP script to upload this data -->
        <?php

        // check if the user has submitted data
        if (!empty($_POST["titleInput"]) or !empty($_POST["descriptionInput"]) or !empty($_POST["dateInput"]) or !empty($_POST["locationInput"]) or !empty($_POST["hostName"])) {

            // ensure that the user is authenticated
            authenticateUser();


        }

        ?>

    </body>
</html>
