<?php
include_once('../core/header.php');
require_once('../core/db.php');

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
        $stmt = $dbConn->prepare("INSERT INTO EVENTS (Name, Category, Description, Time, Date, LocationID, ContactPhone, ContactEmail) VALUES (:eventName, :category, :description, :time, :date, :locationId, :contactPhone, :contactEmail)");
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
            <form class="text-start" action="/events/create_event.php" method="post">
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
                    <input style="height:180px; padding-bottom:150px" type="text" name="description"
                           class="form-control" id="description" aria-describedby="emailHelp"
                           placeholder="Enter Description..." maxlength="500">
                </div>


                <div class="form-group mb-2">
                    <label class="ml-3 form-control-placeholder mr-3" id="date" for="start">Date:&nbsp;</label>
                    <input type="date" id="date" name ="date" class="form-control text-left mr-2">
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
                <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Create Event
                </button>
            </form>
        </div>

        <script>
            $(document).ready(function () {
                $("#addMoreMembers").click(function () {
                    $('<input type="email" class="form-control mb-2 memberEmails" id="memberEmails[]" name="memberEmails[]" placeholder="Email address">').appendTo("#memberEmailsContainer");
                });
            });
        </script>
    </div>
</div>

<?php
include_once('core/footer.php');
?>

