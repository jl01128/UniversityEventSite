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
                ?>
                <div class="col">
                    <?php
                    if ($event["EventType"] != 'rso' || orgs_check_membership($university_id, $event["RSOID"], $user_id)) : ?>
                        <div class="card" style="width: 18rem;">
                            <img src="..." class="card-img-top" alt="...">
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

<?php
include_once('../core/footer.php');
?>