
<!-- DATABASE FUNCTIONS! -->
<?php

function db_get_connection() {
    $dbConn = null;

    $host = '127.0.0.1';
    $db = 'universitysite';
    $user = 'universitysite';
    $pass = 'universitysite';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $dbConn = new PDO($dsn, $user, $pass, $options);
        return $dbConn;

    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}

function users_get_user_from_email($universityId, $userEmail) {

    //Get connection
    $dbConn = db_get_connection();

    $stmt = $dbConn->prepare("SELECT * FROM Users WHERE UniversityID = :universityId AND Email = :email");
    $stmt->bindParam(':universityId', $universityId);
    $stmt->bindParam(':email', $userEmail);
    $stmt->execute();

    $user = $stmt->fetch();


    return $user;
}

function users_get_user($universityId, $userId) {

    //Get connection
    $dbConn = db_get_connection();

    $stmt = $dbConn->prepare("SELECT * FROM Users WHERE UniversityID = :universityId AND UserID = :userId");
    $stmt->bindParam(':universityId', $universityId);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();

    $user = $stmt->fetch();


    return $user;
}


function orgs_check_exists($universityId, $rsoName) {

    //Get the rso
    $rso = orgs_get_rsoid($universityId, $rsoName);

    return $rso == null;

}

function orgs_get_all_rsos($universityId) {

    //Get connection
    $dbConn = db_get_connection();

    // Fetch RSOs for the user's university
    $stmt = $dbConn->prepare('SELECT * FROM RSOs WHERE UniversityID = :university_id');
    $stmt->execute(['university_id' => $universityId]);
    $rsos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return$rsos;
}

function orgs_get_rso($universityId, $rsoId) {

    //Get connection
    $dbConn = db_get_connection();

    //Get the event
    $statement = 'SELECT * FROM rsos WHERE RSOID = :rsoId AND UniversityID = :universityId';

    $stmt = $dbConn->prepare($statement);
    $stmt->bindParam(':rsoId', $rsoId);
    $stmt->bindParam(':universityId', $universityId);

    //Execute the statement
    $stmt->execute();

    //Get the result
    $rso = $stmt->fetch();

    return $rso;
}


function orgs_get_rsoid($universityId, $rsoName) {

    //Get connection
    $dbConn = db_get_connection();

    //Get the event
    $statement = 'SELECT * FROM rsos WHERE Name = :rsoName AND UniversityID = :universityId';

    $stmt = $dbConn->prepare($statement);
    $stmt->bindParam(':rsoName', $rsoName);
    $stmt->bindParam(':universityId', $universityId);

    //Execute the statement
    $stmt->execute();

    //Get the result
    $rso = $stmt->fetch();

    return $rso["RSOID"];
}


function orgs_create_rso($universityId, $rsoName, $adminId, $memberEmails) {

    //Get connection
    $dbConn = db_get_connection();

    // Insert the RSO
    $stmt = $dbConn->prepare("INSERT INTO RSOs (Name, AdminID, UniversityID) VALUES (:name, :adminID, :universityID)");
    $stmt->bindParam(':name', $rsoName);
    $stmt->bindParam(':adminID', $adminId);
    $stmt->bindParam(':universityID', $universityId);
    $stmt->execute();
    $rsoID = $dbConn->lastInsertId();

    // Get UserIDs for member email addresses
    foreach ($memberEmails as $email) {

        //Get the user
        $user = users_get_user_from_email($universityId, $email);

        //Add the user to the RSO
        orgs_add_member($rsoID, $user["UserID"]);
    }
}

function orgs_update_rso($universityId, $rsoId, $rsoName, $adminId, $memberEmails) {

    //Check if name is taken
    if (orgs_get_rsoid($universityId, $rsoName))
        return false;

    //Get connection
    $dbConn = db_get_connection();

    //Get the RSO
    $rso = orgs_get_rso($universityId, $rsoId);

    //Get the members
    $rsoCurrentMembers = orgs_get_members($rsoId);

    //Remove all members
    foreach ($rsoCurrentMembers as $rsoMember) {
        orgs_remove_member($rsoId, $rsoMember["UserID"]);
    }

    //Add all the members
    foreach ($memberEmails as $email) {

        //Get the user
        $user = users_get_user_from_email($universityId, $email);

        //Add the user to the RSO
        orgs_add_member($rsoId, $user["UserID"]);
    }

    //Update admin and name
    $stmt = $dbConn->prepare('UPDATE rsos SET Name = :rsoName, AdminID = :adminId WHERE UniversityID = :universityId AND RSOID = :rsoId');
    $stmt->bindParam(':rsoName', $rsoName);
    $stmt->bindParam(':adminId', $adminId);
    $stmt->bindParam(':universityId', $universityId);
    $stmt->bindParam(':rsoId', $rsoId);

    //Execute the statement
    $stmt->execute();



}

function orgs_get_members($rsoId) {

    //Get connection
    $dbConn = db_get_connection();

    //Get the rating of the event
    $statement = 'SELECT * FROM rsomembers WHERE RSOID = :rsoId';

    $stmt = $dbConn->prepare($statement);
    $stmt->bindParam(':rsoId', $rsoId);

    //Execute the statement
    $stmt->execute();

    //Get the result
    return $stmt->fetchAll();

}

function orgs_check_admin($universityId, $rsoId, $userId) {

    //Get the RSO
    $rso = orgs_get_rso($universityId, $rsoId);

    //Return the status
    return $rso["AdminID"] == $userId;
}

function orgs_check_membership($universityId, $rsoId, $userId) {

    //Get connection
    $dbConn = db_get_connection();

    //Get the rating of the event
    $statement = 'SELECT COUNT(*) FROM rsomembers WHERE RSOID = :rsoId AND UserID = :userId';

    $stmt = $dbConn->prepare($statement);
    $stmt->bindParam(':rsoId', $rsoId);
    $stmt->bindParam(':userId', $userId);

    //Execute the statement
    $stmt->execute();

    //Get the result
    $count = $stmt->fetchColumn();

    //Get the result
    return count > 0;
}

function orgs_add_member($rsoId, $newMemberId) {

    //Get connection
    $dbConn = db_get_connection();

    // Add members to the RSOMembers table
    $stmt = $dbConn->prepare("INSERT INTO RSOMembers (UserID, RSOID) VALUES (:userID, :rsoID)");

    $stmt->bindParam(':userID', $newMemberId);
    $stmt->bindParam(':rsoID', $rsoId);
    $stmt->execute();
}

function orgs_remove_member($rsoId, $memberId) {

    //Get connection
    $dbConn = db_get_connection();

    // Add members to the RSOMembers table
    $stmt = $dbConn->prepare("DELETE FROM RSOMembers WHERE UserID = :userId AND RSOID = :rsoId");

    $stmt->bindParam(':userId', $memberId);
    $stmt->bindParam(':rsoId', $rsoId);
    $stmt->execute();
}

function events_create_event($universityId, $eventName, $category, $description, $time, $date, $latitude, $longitude, $contactPhone, $contactEmail, $eventType, $rsoID) {

    //Get connection
    $dbConn = db_get_connection();

    //Create the location
    $locationId = locations_create_location($eventName, $latitude, $longitude);

    $stmt = $dbConn->prepare("INSERT INTO EVENTS (Name, Category, Description, Time, Date, LocationID, ContactPhone, ContactEmail, EventType, RSOID, UniversityID, APPROVED) VALUES (:eventName, :category, :description, :time, :date, :locationId, :contactPhone, :contactEmail, :eventType, :rsoID, :universityID, false)");
    $stmt->bindParam(':eventName', $eventName);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':time', $time);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':locationId', $locationId);
    $stmt->bindParam(':contactPhone', $contactPhone);
    $stmt->bindParam(':contactEmail', $contactEmail);
    $stmt->bindParam(':eventType', $eventType);
    $stmt->bindParam(':rsoID', $rsoID);
    $stmt->bindParam(':universityID', $universityId);

    $stmt->execute();
}

function events_get_all_events($universityId) {

    //Get connection
    $dbConn = db_get_connection();


    $stmt = $dbConn->prepare('SELECT * FROM events WHERE UniversityID = :university_id');
    $stmt->execute(['university_id' => $universityId]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $events;
}

function events_get_event($universityId, $eventId)
{
    //Get connection
    $dbConn = db_get_connection();

    $statement = 'SELECT * FROM Events WHERE EventID = :eventId AND UniversityID = :universityId';
    $stmt = $dbConn->prepare($statement);
    $stmt->bindParam(':universityId', $universityId);
    $stmt->bindParam(':eventId', $eventId);
    $stmt->execute();

    return $stmt->fetch();
}

function events_get_event_rating($eventId) {

    //Get connection
    $dbConn = db_get_connection();

    //Get the rating of the event
    $statement = 'SELECT AVG(Stars) FROM Ratings WHERE EventID = :eventId';

    $stmt = $dbConn->prepare($statement);
    $stmt->bindParam(':eventId', $eventId);


    //Execute the statement
    $stmt->execute();

    //Get the result
    return $stmt->fetchColumn();
}

function events_set_event_rating($eventId, $userId, $rating) {

    //Get connection
    $dbConn = db_get_connection();

    try {
        // Insert the RSO
        $stmt = $dbConn->prepare('SELECT COUNT(*) FROM ratings WHERE UserId = :userId AND EventID = :eventId');
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':eventId', $eventId);

        //Execute the statement
        $stmt->execute();

        //Get the result
        $count = $stmt->fetchColumn();


        //If its not zero, it already exists!
        if ($count == 0) {
            $stmt = $dbConn->prepare('INSERT INTO ratings (UserID, EventID, Stars) VALUES (:userId, :eventId, :rating)');
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':eventId', $eventId);
            $stmt->bindParam(':rating', $rating);

            //Execute the statement
            $stmt->execute();

        } else {
            $stmt = $dbConn->prepare('UPDATE ratings SET Stars = :rating WHERE UserID = :userId AND EventID = :eventId');
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':eventId', $eventId);
            $stmt->bindParam(':rating', $rating);

            //Execute the statement
            $stmt->execute();
        }

        $stmt->execute();

    } catch (PDOException $e) {
        $error = "Error creating RSO: " . $e->getMessage();
    }
}

function locations_create_location($name, $latitude, $longitude) {

    //Get connection
    $dbConn = db_get_connection();

    // Add members to the RSOMembers table
    $stmt = $dbConn->prepare("INSERT INTO locations (Name, Latitude, Longitude) VALUES (:name, :latitude, :longitude)");

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':latitude', $latitude);
    $stmt->bindParam(':longitude', $longitude);
    $stmt->execute();

    return $dbConn->lastInsertId();
}

function comments_add_comment($userId, $eventId, $content) {

    //Get connection
    $dbConn = db_get_connection();

    $stmt = $dbConn->prepare('INSERT INTO comments (UserID, EventID, Content) VALUES (:userId, :eventId, :content)');
    $stmt->bindParam(':userId', $userId);
    $stmt->bindParam(':eventId', $eventId);
    $stmt->bindParam(':content', $content);

    //Execute the statement
    $stmt->execute();

    //Get the result
    return $stmt->fetch();
}

function comments_get_comments($eventId) {

    //Get connection
    $dbConn = db_get_connection();

    //Get the rating of the event
    $statement = 'SELECT * FROM comments WHERE EventID = :eventId';

    $stmt = $dbConn->prepare($statement);
    $stmt->bindParam(':eventId', $eventId);


    //Execute the statement
    $stmt->execute();

    //Get the result
    return $stmt->fetchAll();
}




?>

