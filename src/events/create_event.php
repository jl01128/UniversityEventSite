<?php
include_once('../core/header.php');
include_once('../core/core.php');
include_once('../auth/login_required.php');

$error = null;

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $eventName = trim($_POST['eventName']);
    $category = ($_POST['category']);
    $description = ($_POST['description']);
    $time = ($_POST['time']);
    $date = ($_POST['date']);
    $address = ($_POST['address']);
    $latitude = ($_POST['latitude']);
    $longitude = ($_POST['longitude']);
    $contactPhone = ($_POST['contactPhone']);
    $contactEmail = ($_POST['contactEmail']);
    $eventType = ($_POST['eventType']);
    $rsoID = ($_POST['rsoID']);
    $universityID = ($_SESSION["user_universityid"]);

    events_create_event($universityID, $eventName, $category, $description, $time, $date, $latitude, $longitude, $address, $contactPhone, $contactEmail, $eventType, $rsoID);

}

$userId = $_SESSION["user_id"];
$universityId = $_SESSION["user_universityid"];


//Get the orgs the user is part of
$userRsos = orgs_get_user_orgs_admin($universityId, $userId);

?>

<?php if ($error != null) : ?>
    <div class="alert alert-danger text-center" role="alert">
        Error: <?php echo $error; ?>
    </div>
<?php endif; ?>

<script>
    $(document).ready(function () {
        $("#addMoreMembers").click(function () {
            $('<input type="email" class="form-control mb-2 memberEmails" id="memberEmails[]" name="memberEmails[]" placeholder="Email address">').appendTo("#memberEmailsContainer");
        });
    });

    $(document).ready(function() {
        $('#eventType').on('change', function() {
            if ($(this).val() === 'rso') {
                $('#rsoIdField').show();
            } else {
                $('#rsoIdField').hide();
            }
        });
    });
</script>
<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 0, lng: 0},
            zoom: 3
        });

        // Create a marker object
        var marker = new google.maps.Marker({
            map: map,
            draggable: true
        });

        // Hide the marker initially
        marker.setVisible(false);

        // Add click event listener to the map
        map.addListener('click', function (event) {
            document.getElementById('latitude').value = event.latLng.lat();
            document.getElementById('longitude').value = event.latLng.lng();


            getAddress(event.latLng.lat(), event.latLng.lng());

            // Update the marker position and show it
            marker.setPosition(event.latLng);
            marker.setVisible(true);


        });

        // Update latitude and longitude fields when the marker is dragged
        marker.addListener('dragend', function (event) {
            document.getElementById('latitude').value = event.latLng.lat();
            document.getElementById('longitude').value = event.latLng.lng();
        });
    }

    function getAddress(lat, lon) {

        const latlng = {
            lat: lat,
            lng: lon,
        };

        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({location: latlng})
            .then((response) => {
                document.getElementById('address').value = response.results[0].formatted_address;
                document.getElementById('address-fake').value = response.results[0].formatted_address;
            })
            .catch((e) => window.alert("Geocoder failed due to: " + e));
    }
</script>

<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCBCmCpSOa0IWY9r2vSabM7nC5mbUSe9zU&callback=initMap"></script>

<div class="h-60 d-flex align-items-center justify-content-center">

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
                    <label for="rsoName" class="form-label">Category</label>
                    <input type="text" class="form-control" id="category" name="category" required>
                </div>

                <div class="form-group mb-2">
                    <label for="inputEventDescription" required="required">Description</label>
                    <input style="height:180px; padding-bottom:150px" type="text" name="description"
                           class="form-control" id="description" aria-describedby="emailHelp"
                           placeholder="Enter Description..." maxlength="500">
                </div>


                <div class="form-group mb-2">
                    <label class="ml-3 form-control-placeholder mr-3" id="date" for="start">Date:&nbsp;</label>
                    <input type="date" id="date" name="date" class="form-control text-left mr-2">
                </div>

                <div class="container px-1 px-sm-5 mx-auto mt-2">
                    <div class="d-flex justify-content-center">
                        <label for="appt">Start Time:&nbsp;</label>
                        <input type="time" id="time" name="time" required>
                    </div>
                </div>
                <div class="mt-2 d-flex justify-content-center">
                    <div id="map" style="width: 100%; height: 300px;"></div>
                </div>

                <input type="num" class="col-2" name="latitude" id="latitude" maxlength="50" hidden required>
                <input type="num" class="col-2" name="longitude" id="longitude" maxlength="50" hidden required>

                <input type="text" class="form-control" id="address" name="address" required hidden>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address-fake" disabled>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="contactPhone" name="contactPhone" maxlength="10"
                           required>
                </div>
                <div class="mb-3">
                    <label for="rsoName" class="form-label">Email Address</label>
                    <input type="text" class="form-control" id="contactEmail" name="contactEmail" required>
                </div>
                <div class="mb-3">
                    <label for="eventType" class="form-label">Event type</label>
                    <select class="form-control" id="eventType" name="eventType" required="required">
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                        <option value="rso">Student Organization</option>
                    </select>
                </div>
                <div class="mb-3" id="rsoIdField" style="display: none;">
                    <label for="rsoID" class="form-label">Organization</label>
                    <select class="form-control" id="rsoID" name="rsoID">
                        <option disabled selected value> Select an organization.... </option>
                        <?php foreach ($userRsos as $RSO) : ?>
                            <option value="<?php echo $RSO['RSOID']; ?>"><?php echo $RSO['Name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Create Event
                </button>
            </form>
        </div>
    </div>
</div>

<?php
include_once('../core/footer.php');
?>

