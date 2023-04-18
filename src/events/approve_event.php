<?php
include_once('../core/header.php');
include_once '../core/db.php';


// Check if the form is submitted
if (isset($_POST['submit'])) {
    $universityId = trim($_POST['university_id']);
    $eventId = trim($_POST['event_id']);

    events_approve_event($universityId, $eventId);


    //Redirect!
    header('Location: /events/view_event.php?id='.$eventId);

}