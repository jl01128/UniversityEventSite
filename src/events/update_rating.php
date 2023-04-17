<?php
include_once('../auth/login_required.php');
include_once('../core/header.php');
include_once '../core/db.php';


// Check if the form is submitted
if (isset($_POST['submit'])) {
    $eventId = trim($_POST['event_id']);
    $rating = trim($_POST['rating']);



    events_set_event_rating($eventId, $_SESSION["user_id"], $rating);

    //Redirect!
    header('Location: /events/view_event.php?id='.$_POST['event_id']);

}