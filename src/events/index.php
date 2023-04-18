<?php
include_once('../auth/login_required.php');
include_once('../core/header.php');

// Get the user ID and university ID from the session
$user_id = $_SESSION["user_id"];
$university_id = $_SESSION["user_universityid"];

// Fetch RSOs for the user's university
$events = events_get_all_events($university_id);
?>
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
                ?>
                <div class="col">
                    <?php
                    if ($event["EventType"] == 'rso' || orgs_check_membership($university_id, $event["RSOID"], $user_id) || $event["EventType"] === 'public' || $event["EventType"] === 'private') : ?>
                        <div class="card" style="width: 18rem;">
                            <div class="mt-2 d-flex justify-content-center">
                                <div id="map<?= $event['EventID'] ?>" style="width: 100%; height: 300px;"></div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= $event["Name"]; ?></h5>
                                <p class="card-text"><?= $event["Description"]; ?></p>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><?= $event["Category"]; ?></li>
                                <li class="list-group-item"><?= $event["Date"]; ?></li>
                                <li class="list-group-item"><?= $event["Time"]; ?></li>
                            </ul>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><?= $event["ContactEmail"]; ?></li>
                                <li class="list-group-item"><?= $event["ContactPhone"]; ?></li>
                            </ul>
                            <div class="card-body">
                                <a href="/events/view_event.php?id=<?= $event["EventID"] ?>" class="card-link">View
                                    Event</a>
                                <a href="/events/edit_event.php?id=<?= $event["EventID"] ?>" class="card-link">Edit
                                    event</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <script>
        // Initialize the maps
        function initMap() {
            <?php foreach ($events as $event) { ?>
            <?php
            if ($event["EventType"] == 'rso' && !orgs_check_membership($university_id, $event["RSOID"], $user_id)) {
                continue;
            }
            $location = location_get_location($event["LocationID"]);
            ?>
            // Create a map for each event
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


<?php
include_once('../core/footer.php');
?>