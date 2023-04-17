<?php
include_once('../core/header.php');
include_once '../core/db.php';


// Check if the form is submitted
if (isset($_POST['submit'])) {
    $eventId = trim($_POST['event_id']);
    $comment = trim($_POST['comment']);
    $userID = $_SESSION["user_id"];
    echo $comment;

    try {
        // Create new row of comments
        $stmt = $dbConn->prepare('INSERT INTO COMMENTS (UserID, EventID, Content) VALUES (:userID, :eventId, :comment)');
    
        $stmt->bindParam(':userId', $userID);
        $stmt->bindParam(':eventId', $eventId);

        //Execute the statement
        $stmt->execute();



    } catch (PDOException $e) {
        $error = "Error creating RSO: " . $e->getMessage();
    }

    //Redirect!
    header('Location: /events/view_event.php?id='.$_POST['event_id']);

}