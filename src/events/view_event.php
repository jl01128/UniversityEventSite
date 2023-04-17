<?php
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

//Get the event
$statement = 'SELECT * FROM Events WHERE EventID = :eventId';

$stmt = $dbConn->prepare($statement);
$stmt->bindParam(':eventId', $_GET["id"]);

//Execute the statement
$stmt->execute();

//Get the result
$eventQuery = $stmt->fetch();

//Get the rating of the event
$statement = 'SELECT AVG(Stars) FROM Ratings WHERE EventID = :eventId';

$stmt = $dbConn->prepare($statement);
$stmt->bindParam(':eventId', $_GET["id"]);

//Execute the statement
$stmt->execute();

//Get the result
$eventRating = $stmt->fetchColumn();

//If its null, just say no ratings
if ($eventRating == null) {
    $eventRating = "No Ratings.";
}


//Get the comments of the event
//$statement = 'SELECT Content FROM Comments WHERE EventID = :eventId';

//$stmt = $dbConn->prepare($statement);
$stmt->bindParam(':eventId', $_GET["id"]);

//Execute the statement
$stmt->execute();

//Get the result
$eventComments = $stmt->fetchColumn();

$stmt = $dbConn->prepare('SELECT Content FROM Comments WHERE EventID = :eventId');
$stmt->execute(['event_id' => $university_id]);
$eventComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

//If its null, just say no ratings
if ($eventComments == null) {
    $eventComments = "No Comments";
}

?>



<?php if ($error != null) : ?>
    <div class="alert alert-danger text-center" role="alert">
        Error: <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="h-100 d-flex align-items-center justify-content-center">

    <!--Create login card.-->
    <div class="card text-center w-50">
        <div class="card-body">

            <h1>Host Event</h1>
            <div class="mb-3">
                <h1 class=""><?= $eventQuery["Name"] ?></h1>
            </div>
            <div class="mb-3">
                <h2 class=""><?= $eventQuery["Description"] ?></h2>
            </div>
            <div class="mb-3">
                <h2 class=""><?= $eventQuery["Category"] ?></h2>
            </div>
            <div class="mb-3">
                <h2 class=""><?= $eventQuery["Time"] ?></h2>
            </div>
            <div class="mb-3">
                <h2 class=""><?= $eventQuery["Date"] ?></h2>
            </div>
            <div class="mb-3">
                <h2 class=""><?= $eventQuery["LocationID"] ?></h2>
            </div>
            <div class="mb-3">
                <h2 class=""><?= $eventQuery["ContactPhone"] ?></h2>
            </div>
            <div class="mb-3">
                <h2 class=""><?= $eventQuery["ContactEmail"] ?></h2>
            </div>
            <div class="mb-3">
                <h2 class=""><?= $eventQuery["RSOID"] ?></h2>
            </div>
            <div class="mb-3">
                <h2 class="">Rating: <?= $eventRating ?></h2>
            </div>

       

            <div class="mb-3">
                <?php foreach ($eventComments as $eventComment) : ?>
                    <div class="col">
                        <div class="card" style="width: 18rem;">  
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><?=$event["Content"];?></li>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <form class="text-start" action="/events/update_rating.php" method="post">
                <input type="hidden" class="form-control" id="event_id" name="event_id" aria-describedby="event_id" value="<?=$_GET["id"]?>" required>
                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <input type="number" class="form-control" id="rating" name="rating" aria-describedby="rating" required>
                </div>
                <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Save Rating</button>
            </form>



            <form class="text-start" action="/events/add_comments.php" method="post">
                <input type="hidden" class="form-control" id="event_id" name="event_id" aria-describedby="event_id" value="<?=$_GET["id"]?>" required>
                <div class="mb-3">
                    <label for="comments" class="form-label">Comments</label>
                    <input type="text" class="form-control" id="comment" name="comment" aria-describedby="event_id" required>
                </div>
                <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Add a Comment</button>
            </form>
        </div>
    </div>
</div>


<?php
include_once('../core/footer.php');
?>
