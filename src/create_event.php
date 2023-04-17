<?php
include_once('core/header.php');
require_once('core/db.php');

$error = null;

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $eventName = trim($_POST['eventName']);
    $category = ($_POST['category']);
    $description = ($_POST['description']);
    $time = ($_POST['time']);
    $date = ($_POST['date']);
    $locationId = ($_POST['locationId']);
    $contactPhone = ($_POST['contactPhone']);
    $contactEmail = ($_POST['contactEmail']);


    // Get the current user's ID as the admin
    $adminID = $_SESSION["user_id"];

    echo 'admin: ';
    echo $adminID;


    try {
        // Insert the RSO
        $stmt = $dbConn->prepare("INSERT INTO EVENTS (Name, Category, Description, Time, Date, LocationID, ConatactPhone, ContactEmail) VALUES (:eventName, :category, :description, :time, :date, :locationId, :,contactPhone, :contactEmail)");
        $stmt->bindParam(':eventName', $eventName);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':locationId', $locationId);
        $stmt->bindParam(':contactPhone', $contactPhone);
        $stmt->bindParam(':contactEmail', $contactEmail);
        $stmt->execute();
      

    } catch (PDOException $e) {
        $error = "Error creating RSO: " . $e->getMessage();
    }

}

?>

<?php if ($error != null) : ?>
    <div class="alert alert-danger text-center" role="alert">
        Error: <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="h-100 d-flex align-items-center justify-content-center">

    <!--Create RSO card.-->
    <div class="card text-center w-25">
        <div class="container">
            <h1>Host Event</h1>
            <form class="text-start" action="/organizations/create_rso.php" method="post">
                <div class="mb-3">
                    <label for="rsoName" class="form-label">Event Name</label>
                    <input type="text" class="form-control" id="eventName" name="eventName" required>
                </div>
                <div class="mb-3">
                    <label for="universityID" class="form-label">Event Category</label>
                    <select class="form-control" id="category" name="category" required="required">
                        <option>Public</option>
                        <option>Private</option>
                        <option>RSO</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label for="inputEventDescription" required="required">Description</label>
                    <input style="height:180px; padding-bottom:150px" type="text" name="description" class="form-control" id="description" aria-describedby="emailHelp" placeholder="Enter Description..." maxlength="500">
                </div>
                <div class="container px-1 px-sm-5 mx-auto mt-2">
                    <form autocomplete="off">
                        <div class="flex-row d-flex justify-content-center">
                            <div class="col-lg-6 col-11 px-1 r">
                                <div class="input-group input-daterange"> 
                                    <label class="ml-3 form-control-placeholder mr-3" id="date" for="start">Date:&nbsp;</label>                                     
                                    <input type="date" id="date" class="form-control text-left mr-2"> 
                                    <span class="fa fa-calendar" id="fa-1"></span> 
                                    <span class="fa fa-calendar" id="fa-2"></span> 
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="container px-1 px-sm-5 mx-auto mt-2">
                    <div class="d-flex justify-content-center">
                    <label for="appt">Start Time:&nbsp;</label>
                    <input type="time" id="time" name="time"
                           min="06:00" max="18:00" required>
                    </div>
                </div>
                <div class="mt-2 d-flex justify-content-center">
                    <label>LocationId:&nbsp;</label>
                    <input class="col-2" type="numver" name="locationId" id="locationId" maxlength="50">
                </div>
                <div class="mb-3">
                    <label for="universityID" class="form-label">Phone</label>
                    <input type="number" class="form-control" id="contactPhone" name="contactPhone" required>
                </div>
                <div class="mb-3">
                    <label for="rsoName" class="form-label">Email Address</label>
                    <input type="text" class="form-control" id="contactEmail" name="contactEmail" required>
                </div>
                <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Create Event</button>
            </form>
        </div>

        <script>
            $(document).ready(function() {
                $("#addMoreMembers").click(function() {
                    $('<input type="email" class="form-control mb-2 memberEmails" id="memberEmails[]" name="memberEmails[]" placeholder="Email address">').appendTo("#memberEmailsContainer");
                });
            });
        </script>
    </div>
</div>

<?php
include_once('core/footer.php');
?>







<!DOCTYPE html> 
<html lang="en"> 
    <head> 
        <title>Event Planning</title>         
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css"> 
        <link rel="stylesheet" href="homepage/styles/bootstrap.min.css">
        <!-- <link href="src/css/style.css" rel="stylesheet">  -->
        <link rel="shortcut icon" href="img/UCFlogo.png"/> 
        <script src="homepage/scripts/bootstrap.min.js"></script>
        <script src="src/js/login.js"></script>
        <script src="src/js/navbar.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.js"></script>
        <style type="text/css">h1 { font-size: 20px; margin-top: 24px; margin-bottom: 24px; } img { height: 40px; }</style>         
    </head>     
    <body> 
      <script type="text/javascript">
          document.addEventListener('DOMContentLoaded', function() 
          {
              readEventCookie();
          }, false);
      </script>

    <script type="text/javascript">
      document.addEventListener('DOMContentLoaded', function() 
      {
          readNavCookie();
          showNavBar();
      }, false);
    </script>

        <nav class="navbar navbar-expand navbar-light bg-light border" style="background-color: #EEE2DC;" id="navbarList">

        </nav>          
        <div class="col-md-8 offset-md-2 mt-5 bg-light">
            <h2 class="bg-dark text-light">Add Your Events Here!</h2>
            <form accept-charset="UTF-8" action="https://getform.io/f/{your-form-endpoint-goes-here}" method="POST" target="_blank">
                <div class="form-group mb-2">
                    <label for="inputEventName">Event Name</label>
                    <input type="text" name="eventName" class="form-control" id="inputEventName" placeholder="Enter Event Name..." required="required" maxlength="200">
                </div>
                <div class="form-group mb-2">
                    <label for="inputEventEmail" required="required">Email Address</label>
                    <input type="email" name="email" class="form-control" id="inputEventEmail" aria-describedby="emailHelp" placeholder="Enter Contact Email..." maxlength="100">
                </div>
                <div class="form-group mb-2">
                    <label for="inputEventPhoneNumber" required="required">Phone Number</label>
                    <input type="text" name="phone" class="form-control" id="inputEventPhoneNumber" aria-describedby="emailHelp" placeholder="Enter Contact Number..." maxlength="15">
                </div>
                <div class="form-group mb-2">
                    <label for="inputEventDescription" required="required">Description</label>
                    <input style="height:180px; padding-bottom:150px" type="text" name="description" class="form-control" id="inputEventDescription" aria-describedby="emailHelp" placeholder="Enter Description..." maxlength="500">
                </div>

                <div class="container px-1 px-sm-5 mx-auto mt-2">
                    <form autocomplete="off">
                        <div class="flex-row d-flex justify-content-center">
                            <div class="col-lg-6 col-11 px-1 r">
                                <div class="input-group input-daterange"> 
                                    <label class="ml-3 form-control-placeholder mr-3" id="inputStartDate" for="start">Start Date:&nbsp;</label>                                     
                                    <input type="text" id="start" class="form-control text-left mr-2"> 
                                    <span class="fa fa-calendar" id="fa-1"></span> 
                                    <label class="ml-3 form-control-placeholder" id="inputEndDate" for="end">&nbsp;End Date:&nbsp;</label>                                     
                                    <input type="text" id="end" class="form-control text-left ml-3"> 
                                    <span class="fa fa-calendar" id="fa-2"></span> 
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="container px-1 px-sm-5 mx-auto mt-2">
                    <div class="d-flex justify-content-center">
                    <label for="appt">Start Time:&nbsp;</label>
                    <input type="time" id="inputStartTime" name="appt"
                           min="06:00" max="18:00" required>
                    <label for="appt">&nbsp;End Time:&nbsp;</label>
                    <input type="time" id="inputEndTime" name="appt"
                           min="06:00" max="18:00" required>
                    </div>
                    <small class="d-flex justify-content-center">Event hours range from 6am to 10pm.</small>

                </div>

                <div class="mt-2 d-flex justify-content-center">
                    <label>Longitude:&nbsp;</label>
                    <input class="col-2" type="text" name="longitude" id="inputEventLongitude" maxlength="50">
                    <label>&nbsp;Latitude:&nbsp;</label>
                    <input class="col-2" type="text" name="latitude" id="inputEventLatitude" maxlength="50">
                </div>

                <div class="form-group mb-2">
                    <label for="inputCategory">Event Type</label>
                    <select class="form-control" id="inputCategory" name="platform" required="required">
                        <option>Public</option>
                        <option>Private</option>
                        <option>RSO</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                  <label for="inputRSO">RSO</label>
                  <select class="form-control" id="inputRSO" name="platform" required="required">
                    <script>
                      document.addEventListener('DOMContentLoaded', function() 
                      {
                          getRSOList();
                      }, false);
                    </script>
                  </select>
              </div>
                <hr>
                <div class="form-group mt-3">
                    <label class="mr-2">Upload your images:</label>
                    <input type="file" name="file" id="file">
                </div>
                <hr>
                <button type="submit" class="btn btn-primary" onclick="readEventInput();">Submit</button>
                <p id="logstatus" style="font-size: 1.0em;"></p>
            </form>
        </div>                 
        <style>#row_style { margin-top: 30px; } #submit { display: block; margin: auto; }</style>         
        <!-- you need to include the shieldui css and js assets in order for the charts to work -->         
        <script type="text/javascript">
            $(document).ready(function(){

            $('.input-daterange').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            calendarWeeks : true,
            clearBtn: true,
            disableTouchKeyboard: true
            });

            });
        </script>
    </body>
