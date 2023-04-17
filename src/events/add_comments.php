<?php
include_once('../core/header.php');
include_once '../core/db.php';


// Check if the form is submitted
if (isset($_POST['submit'])) {
    $eventId = trim($_POST['event_id']);
    $comment = trim($_POST['comment']);
    $userID = $_SESSION["user_id"];

    comments_add_comment($userID, $eventId, $comment);


    //Redirect!
    header('Location: /events/view_event.php?id='.$_POST['event_id']);

}