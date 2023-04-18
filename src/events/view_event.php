<?php
include_once('../auth/login_required.php');
include_once('../core/header.php');

$error = null;
?>


<?php

//Check if its a form call!
if (isset($_POST['submit'])) {
    //header('Location: index.php');

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);


    //If theres no errors, set the session!
    if ($error == null) {

        //Create the statement.
        $statement = 'SELECT UserID, Password, FullName, UniversityID FROM Users WHERE Email = :email';

        $stmt = $dbConn->prepare($statement);
        $stmt->bindParam(':email', $email);

        //Execute the statement
        $stmt->execute();

        //Get the result
        $queryResult = $stmt->fetch();

        echo $queryResult["Password"];

        //Check validity!
        if ($queryResult["UserID"] == null || $queryResult["Password"] == null) {
            $error = "Invalid username/password.";

        } else if (!password_verify($password, $queryResult["Password"])) {
            $error = "Incorrect username/password.";
        } else {
            $_SESSION["user_id"] = $queryResult["UserID"];
            $_SESSION["user_universityid"] = $queryResult["UniversityID"];
            $_SESSION["user_fullname"] = $queryResult["FullName"];
        }

    }


    //If theres no errors then go back to the index!
    if ($error == null) {
        header('Location: /index.php');
    }
}

?>

<?php

$universityId = $_SESSION["user_universityid"];

//Get the event
$event = events_get_event($_SESSION["user_universityid"], $_GET["id"]);
$location = location_get_location($event["LocationID"]);

$rso = null;

if ($event["EventType"] == 'rso') {
    $rso = orgs_get_rso($event["UniversityID"], $event["RSOID"]);
}

//Get the result
$eventRating = events_get_event_rating($_GET["id"]);

//If its null, just say no ratings
if ($eventRating == null) {
    $eventRating = "No Ratings.";
}

//Get the comments
$comments = comments_get_comments($_GET["id"]);

//Make format variables
$date = new DateTime($event["Date"]);
$formatted_date = $date->format('F j, Y');
$time = new DateTime($event["Time"]);
$formatted_time = $time->format('g:i A');
$google_maps_url = "https://maps.google.com/?q={$location['Latitude']},{$location['Longitude']}";


?>



<?php if ($error != null) : ?>
    <div class="alert alert-danger text-center" role="alert">
        Error: <?php echo $error; ?>
    </div>
<?php endif; ?>

<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCBCmCpSOa0IWY9r2vSabM7nC5mbUSe9zU&callback=initMap"></script>

<script>

    function initMap() {
        <?php
        $location = location_get_location($event["LocationID"]);
        ?>
        var map<?= $event['EventID'] ?> = new google.maps.Map(document.getElementById('map'), {
            center: {lat: <?= $location['Latitude'] ?>, lng: <?= $location['Longitude'] ?>},
            zoom: 15
        });

        // Add a marker for each event
        var marker<?= $event['EventID'] ?> = new google.maps.Marker({
            position: {lat: <?= $location['Latitude'] ?>, lng: <?= $location['Longitude'] ?>},
            map: map<?= $event['EventID'] ?>
        });
    }

    window.initMap = initMap;

    //Init the maps
    initMap();
</script>


<div class="p-5 pb-md-4 mx-auto text-center">
    <h1 class="display-4 fw-normal"><?= $event["Name"] ?></h1>
</div>
<div class="container">
    <div class="row">
        <div class="col col-md-8 align-self-center h-100">
            <div class="card h-100">
                <div id="map" style="width: auto; height: 35rem;"></div>
            </div>
        </div>
        <div class="col col-md-4 align-self-center">
            <div class="row my-2">
                <div class="col-md-12">
                    <div class="card">
                        <h5 class="card-header">
                            Event Details
                        </h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Description: </strong> <?= $event["Description"]; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Category: </strong> <?= $event["Category"]; ?>
                            </li>
                            <?php if ($event["EventType"] == 'rso'): ?>
                                <li class="list-group-item">
                                    <strong>Organzation: </strong> <?= $rso["Name"]; ?>
                                </li>
                            <?php endif; ?>
                            <li class="list-group-item">
                                <strong>Contact Email:</strong>
                                <a href="mailto:<?= $event["ContactEmail"]; ?>"><?= $event["ContactEmail"]; ?></a>
                            </li>
                            <li class="list-group-item">
                                <strong>Contact Phone:</strong>
                                <a href="tel:<?= $event["ContactPhone"]; ?>"><?= $event["ContactPhone"]; ?></a>
                            </li>
                            <li class="list-group-item">
                                <strong>Rating:</strong>
                                <?php for ($i = 0; $i < $eventRating; $i++): ?> <i
                                    class="fa-solid fa-star"></i> <?php endfor; ?>
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
                                <strong>Address: </strong> <?= $location["Name"]; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Date:</strong> <?= $formatted_date; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Time:</strong> <?= $formatted_time ?>
                            </li>
                        </ul>
                        <div class="card-body">
                            <a href="<?= $google_maps_url ?>" target="_blank" class="btn btn-primary w-100">Get
                                Directions</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="p-5 pb-md-4 mx-auto text-center">
        <h1 class="display-6 fw-normal">Comments</h1>
    </div>

    <div class="row">
        <div class="col col-md-6 align-self-top h-100">
            <div class="comment-section">
                <?php foreach ($comments as $eventComment) : ?>
                    <?php

                    $commentUser = users_get_user($universityId, $eventComment["UserID"]);

                    ?>
                    <div class="card">
                        <h5 class="card-header">
                            <strong><?= $commentUser["FullName"] ?></strong>
                        </h5>
                        <div class="card-body">
                            <p class="card-text"><?= $eventComment["Content"]; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col col-md-6 align-self-top">
            <div class="row my-2">
                <div class="col-md-12">
                    <div class="card">
                        <h5 class="card-header">
                            Leave a Rating
                        </h5>
                        <div class="card-body">

                            <form class="text-center w-100" action="/events/update_rating.php" method="post">
                                <input type="hidden" class="form-control" id="event_id" name="event_id"
                                       value="<?= $_GET["id"] ?>" required>
                                <div class="mb-3">
                                    <div class="btn-group" role="group" aria-label="Rating">
                                        <input type="radio" class="btn-check" name="rating" id="rating1"
                                               value="1"<?= $eventRating == 1 ? ' checked' : '' ?> required>
                                        <label class="btn btn-secondary" for="rating1">
                                            <i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i
                                                class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i
                                                class="fa-regular fa-star"></i>
                                        </label>

                                        <input type="radio" class="btn-check" name="rating" id="rating2"
                                               value="2"<?= $eventRating == 2 ? ' checked' : '' ?>>
                                        <label class="btn btn-secondary" for="rating2">
                                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                                class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i
                                                class="fa-regular fa-star"></i>
                                        </label>

                                        <input type="radio" class="btn-check" name="rating" id="rating3"
                                               value="3"<?= $eventRating == 3 ? ' checked' : '' ?>>
                                        <label class="btn btn-secondary" for="rating3">
                                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                                class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i
                                                class="fa-regular fa-star"></i>
                                        </label>

                                        <input type="radio" class="btn-check" name="rating" id="rating4"
                                               value="4"<?= $eventRating == 4 ? ' checked' : '' ?>>
                                        <label class="btn btn-secondary" for="rating4">
                                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                                class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                                class="fa-regular fa-star"></i>
                                        </label>

                                        <input type="radio" class="btn-check" name="rating" id="rating5"
                                               value="5"<?= $eventRating == 5 ? ' checked' : '' ?>>
                                        <label class="btn btn-secondary" for="rating5">
                                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                                class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                                class="fa-solid fa-star"></i>
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" id="submit" name="submit"
                                        class="btn btn-primary text-center w-100">Save Rating
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="card">
                        <h5 class="card-header">
                            Leave a Comment
                        </h5>
                        <div class="card-body">
                            <form class="text-start" action="/events/add_comments.php" method="post">
                                <input type="hidden" class="form-control" id="event_id" name="event_id"
                                       value="<?= $_GET["id"] ?>" required>
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="Leave a comment here"
                                                  id="comment" name="comment"
                                                  style="height: 200px; resize: none;"></textarea>
                                        <label for="comment">Type your comment here.</label>
                                    </div>
                                </div>
                                <button type="submit" id="submit" name="submit"
                                        class="btn btn-primary text-center w-100">Add comment
                                </button>
                            </form>
                            <a href="/events/edit_comments.php?id=<?= $event["EventID"] ?>" class="nav-link">Edit Your
                                Comment</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--
<div class="container">
    <div class="event-container">
        <h1>Host Event</h1>
        <h2><?= $event["Name"] ?></h2>
        <p><?= $event["Description"] ?></p>
        <p>Category: <?= $event["Category"] ?></p>
        <p>Time: <?= $event["Time"] ?></p>
        <p>Date: <?= $event["Date"] ?></p>
        <p>Location: <?= $event["LocationID"] ?></p>
        <p>Contact Phone: <?= $event["ContactPhone"] ?></p>
        <p>Contact Email: <?= $event["ContactEmail"] ?></p>
        <p>RSOID: <?= $event["RSOID"] ?></p>
        <p>Rating: <?= $eventRating ?> / 5 <i class="fas fa-star"></i></p>

        <div id="map" style="width: 100%; height: 300px;"></div>
        <button type="button"
                onclick="window.open('https://maps.google.com/?q=<?= $location['Latitude'] ?>,<?= $location['Longitude'] ?>', '_blank')">
            Get Directions
        </button>

        <form class="text-start" action="/events/update_rating.php" method="post">
            <input type="hidden" class="form-control" id="event_id" name="event_id" value="<?= $_GET["id"] ?>" required>
            <div class="mb-3">
                <label for="rating" class="form-label">Rating</label>
                <input type="range" min="1" max="5" class="form-control" id="rating" name="rating"
                       value="<?= $eventRating ?>" oninput="ratingValue.value=rating.value" required>
                <output name="ratingValue" id="ratingValue"><?= $eventRating ?></output>
                / 5
            </div>
            <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Save Rating</button>
        </form>

        <h3>Comments</h3>
        <div class="comment-section">
            <?php foreach ($comments as $eventComment) : ?>
                <div class="comment">
                    <strong><?= $eventComment["Username"] ?>:</strong>
                    <p><?= $eventComment["Content"]; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <form class="text-start" action="/events/add_comments.php" method="post">
            <input type="hidden" class="form-control" id="event_id" name="event_id" value="<?= $_GET["id"] ?>" required>
            <div class="mb-3">
                <label for="comment" class="form-label">Add a Comment</label>
                <input type="text" class="form-control" id="comment" name="comment" aria-describedby="event_id"
                       required>
            </div>
            <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Add a Comment</button>
        </form>
        <a href="/events/edit_comments.php?id=<?= $event["EventID"] ?>" class="nav-link">Edit Your Comment</a>
    </div>
</div>

<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: <?= $location['Latitude'] ?>, lng: <?= $location['Longitude'] ?>},
            zoom: 15
        });

        var marker = new google.maps.Marker({
            position: {lat: <?= $location['Latitude'] ?>, lng: <?= $location['Longitude'] ?>},
            map: map
        });
    }

    initMap();
</script>

-->

    <?php
    include_once('../core/footer.php');
    ?>
