<?php
include_once('../core/header.php');
include_once '../core/db.php';


// Check if the form is submitted
if (isset($_POST['submit'])) {
    $eventId = trim($_POST['event_id']);
    $rating = trim($_POST['rating']);



    try {
        // Insert the RSO
        $stmt = $dbConn->prepare('SELECT COUNT(*) FROM ratings WHERE UserId = :userId AND EventID = :eventId');
        $stmt->bindParam(':userId', $_SESSION["user_id"]);
        $stmt->bindParam(':eventId', $eventId);

        //Execute the statement
        $stmt->execute();

        //Get the result
        $count = $stmt->fetchColumn();


        //If its not zero, it already exists!
        if ($count == 0) {
            $stmt = $dbConn->prepare('INSERT INTO ratings (UserID, EventID, Stars) VALUES (:userId, :eventId, :rating)');
            $stmt->bindParam(':userId', $_SESSION["user_id"]);
            $stmt->bindParam(':eventId', $eventId);
            $stmt->bindParam(':rating', $rating);

            //Execute the statement
            $stmt->execute();

        } else {
            $stmt = $dbConn->prepare('UPDATE ratings SET Stars = :rating WHERE UserID = :userId AND EventID = :eventId');
            $stmt->bindParam(':userId', $_SESSION["user_id"]);
            $stmt->bindParam(':eventId', $eventId);
            $stmt->bindParam(':rating', $rating);

            //Execute the statement
            $stmt->execute();
        }

        $stmt->execute();

    } catch (PDOException $e) {
        $error = "Error creating RSO: " . $e->getMessage();
    }

    //Redirect!
    header('Location: /events/view_event.php?id='.$_POST['event_id']);

}