<?php
include_once('../core/header.php');
include_once '../core/db.php';


// Check if the form is submitted
if (isset($_POST['submit'])) {
    $commentID = trim($_POST['commentID']);
    $comment = ($_POST['comment']);
 

    edit_comments($commentID, $comment);


    //Redirect!
    header('Location: /events/view_event.php?id='.$_POST['eventId']);

}