<?php
include_once('../auth/login_required.php');
include_once('../core/header.php');

// Get the user ID and university ID from the session
$user_id = $_SESSION["user_id"];
$university_id = $_SESSION["user_universityid"];

// Fetch RSOs for the user's university
$events = events_get_all_events($university_id);
$university = university_get_university($university_id);
?>

    <script>

        function initMap() {
            <?php foreach ($events as $event) { ?>
            <?php
            if ($event["EventType"] == 'rso' && !orgs_check_membership($university_id, $event["RSOID"], $user_id)) {
                continue;
            }
            $location = location_get_location($event["LocationID"]);
            ?>
            var map<?= $event['EventID'] ?> = new google.maps.Map(document.getElementById('map<?= $event['EventID'] ?>'), {
                center: {lat: <?= $location['Latitude'] ?>, lng: <?= $location['Longitude'] ?>},
                zoom: 15
            });

            // Add a marker for each event
            var marker<?= $event['EventID'] ?> = new google.maps.Marker({
                position: {lat: <?= $location['Latitude'] ?>, lng: <?= $location['Longitude'] ?>},
                map: map<?= $event['EventID'] ?>
            });
            <?php } ?>
        }

        //Init the maps
        initMap();
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCBCmCpSOa0IWY9r2vSabM7nC5mbUSe9zU&callback=initMap"></script>

    <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
        <h1 class="display-4 fw-normal"><?= $university["Name"]; ?> Events</h1>
        <p class="fs-5 text-muted"><?= $university["Description"]; ?></p>
    </div>
    <div class="container">
        <div class="row">
            <?php foreach ($events as $event) { ?>
                <?php
                if ($event["EventType"] == 'rso' && !orgs_check_membership($university_id, $event["RSOID"], $user_id)) {
                    continue;
                }
                if (($event["EventType"] === 'private' && $event["UniversityID"] !== $university_id) || ($event["EventType"] !== 'rso' && !$event["Approved"])) {
                    continue;
                }

                //Make format variables
                $date = new DateTime($event["Date"]);
                $formatted_date = $date->format('F j, Y');
                $time = new DateTime($event["Time"]);
                $formatted_time = $time->format('g:i A');
                $google_maps_url = "https://maps.google.com/?q={$location['Latitude']},{$location['Longitude']}";
                ?>

                <div class="col-md-4 event-card">
                    <?php
                    if ($event["EventType"] == 'rso' || orgs_check_membership($university_id, $event["RSOID"], $user_id) || $event["EventType"] === 'public' || $event["EventType"] === 'private') : ?>
                        <div class="card h-100">
                            <div id="map<?= $event['EventID'] ?>" style="width: 100%; height: 300px;"></div>
                            <div class="card-body">
                                <h5 class="card-title"><?= $event["Name"]; ?></h5>
                                <p class="card-text"><?= $event["Description"]; ?></p>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Category:</strong> <?= $event["Category"]; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Date:</strong> <?= $formatted_date; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Time:</strong> <?= $formatted_time ?>
                                </li>
                            </ul>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Contact Email:</strong>
                                    <a href="mailto:<?= $event["ContactEmail"];?>"><?= $event["ContactEmail"];?></a>
                                </li>
                                <li class="list-group-item">
                                    <strong>Contact Phone:</strong>
                                    <a href="tel:<?= $event["ContactPhone"];?>"><?= $event["ContactPhone"];?></a>
                                </li>
                            </ul>
                            <div class="card-body">
                                <a href="/events/view_event.php?id=<?= $event["EventID"] ?>" class="btn btn-primary w-100 mb-2">View Event</a>
                                <a href="<?= $google_maps_url ?>" target="_blank" class="btn btn-secondary w-100">Get Directions</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php } ?>
        </div>
    </div>


<?php
include_once('../core/footer.php');
?>