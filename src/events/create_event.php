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
    $rsoID = null;
    if ($eventType == 'rso')
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

    $(document).ready(function () {
        $('#eventType').on('change', function () {
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


<div class="p-5 pb-md-4 mx-auto text-center">
    <h1 class="display-4 fw-normal">Create Event</h1>
</div>
<div class="container">
    <div class="row">
        <div class="col col-md-8 align-self-center h-100">
            <div class="card h-100">
                <div id="map" style="width: auto; height: 61rem;"></div>
            </div>
        </div>
        <div class="col col-md-4 align-self-center">
            <form class="text-start" action="/events/create_event.php" method="post">
                <div class="row my-2">
                    <div class="col-md-12">
                        <div class="card">
                            <h5 class="card-header">
                                Event Details
                            </h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Event Name: </strong> <input type="text" class="form-control" id="eventName" name="eventName" required>
                                </li>
                                <li class="list-group-item">
                                    <strong>Category: </strong> <input type="text" class="form-control" id="category" name="category" required>
                                </li>
                                <li class="list-group-item">
                                    <strong>Description: </strong> <input style="height:180px; padding-bottom:150px" type="text" name="description"
                                                                          class="form-control" id="description" aria-describedby="emailHelp"
                                                                          placeholder="Enter Description..." maxlength="500">
                                </li>
                                <li class="list-group-item">
                                    <strong>Event Type: </strong> <select class="form-control" id="eventType" name="eventType" required="required">
                                        <option value="public">Public</option>
                                        <option value="private">Private</option>
                                        <option value="rso">Student Organization</option>
                                    </select>
                                </li>
                                <li class="list-group-item" id="rsoIdField" style="display: none;">
                                    <strong>Organization: </strong> <select class="form-control" id="rsoID" name="rsoID">
                                        <option disabled selected value> Select an organization.... </option>
                                        <?php foreach ($userRsos as $RSO) : ?>
                                            <option value="<?php echo $RSO['RSOID']; ?>"><?php echo $RSO['Name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </li>
                                <li class="list-group-item">
                                    <strong>Contact Email:</strong>
                                    <input type="text" class="form-control" id="contactEmail" name="contactEmail" required>
                                </li>
                                <li class="list-group-item">
                                    <strong>Contact Phone:</strong>
                                    <input type="tel" class="form-control" id="contactPhone" name="contactPhone" maxlength="10"
                                           required>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-md-12 align-self-center">
                        <div class="card">
                            <h5 class="card-header">
                                Event Location & Time
                            </h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Address: </strong> <input type="text" class="form-control" id="address-fake" disabled>
                                    <input type="num" class="col-2" name="latitude" id="latitude" maxlength="50" hidden required>
                                    <input type="num" class="col-2" name="longitude" id="longitude" maxlength="50" hidden required>
                                    <input type="text" class="form-control" id="address" name="address" required hidden>
                                </li>
                                <li class="list-group-item">
                                    <strong>Date:</strong> <input type="date" id="date" name="date" class="form-control text-left mr-2">
                                </li>
                                <li class="list-group-item">
                                    <strong>Time:</strong> <input type="time" id="time" name="time" required>
                                </li>
                            </ul>
                            <div class="card-body">
                                <button type="submit" id="submit" name="submit" class="btn btn-primary text-center w-100">Create Event
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include_once('../core/footer.php');
?>

