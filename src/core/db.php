
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

function auth_login($email, $password) {

    //Get connection
    $dbConn = db_get_connection();

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
        return false;

    }else if (!password_verify($password, $queryResult["Password"])) {
        return false;
    } else {
        $_SESSION["user_id"] = $queryResult["UserID"];
        $_SESSION["user_universityid"] = $queryResult["UniversityID"];
        $_SESSION["user_fullname"] = $queryResult["FullName"];
    }

    return true;
}

function university_get_university($universityId) {

    //Get connection
    $dbConn = db_get_connection();

    $stmt = $dbConn->prepare("SELECT * FROM Universities WHERE UniversityID = :universityId");
    $stmt->bindParam(':universityId', $universityId);
    $stmt->execute();

    $user = $stmt->fetch();


    return $user;
}

function university_get_all_universities() {

    //Get connection
    $dbConn = db_get_connection();

    // Fetch RSOs for the user's university
    $stmt = $dbConn->prepare('SELECT * FROM Universities');
    $stmt->execute();
    $unis = $stmt->fetchAll();

    return $unis;
}

function university_check_superadmin($universityId, $userId) {

    //Get the uni
    $uni = university_get_university($universityId);


    return $uni["AdminID"] == $userId;
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

function users_user_email_exists($email) {

    //Get connection
    $dbConn = db_get_connection();

    //Get the rating of the event
    $statement = 'SELECT COUNT(*) FROM Users WHERE Email = :email';

    $stmt = $dbConn->prepare($statement);
    $stmt->bindParam(':email', $email);

    //Execute the statement
    $stmt->execute();

    //Get the result
    $count = $stmt->fetchColumn();

    //Get the result
    return $count > 0;
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

function users_create_user($email, $password, $fullName, $universityId) {

    //Get connection
    $dbConn = db_get_connection();

    //Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    //Create the statement.
    $statement = 'INSERT INTO Users (Email, Password, FullName, UniversityID) VALUES (:email, :password, :fullName, :universityID)';

    $stmt = $dbConn->prepare($statement);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':fullName', $fullName);
    $stmt->bindParam(':universityID', $universityId);
    $userId = $dbConn->lastInsertId();

    //Execute the statement
    $stmt->execute();

    return $userId;
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

    return $rso;
}


function orgs_create_rso($universityId, $rsoName, $rsoDescription, $rsoImage, $adminId, $memberEmails) {

    //Get connection
    $dbConn = db_get_connection();

    // Insert the RSO
    $stmt = $dbConn->prepare("INSERT INTO RSOs (Name, AdminID, Description, ImageURL, UniversityID) VALUES (:name, :adminID, :rsoDescription,:rsoImage, :universityID)");
    $stmt->bindParam(':name', $rsoName);
    $stmt->bindParam(':rsoDescription', $rsoDescription);
    $stmt->bindParam(':rsoImage', $rsoImage);
    $stmt->bindParam(':adminID', $adminId);
    $stmt->bindParam(':universityID', $universityId);
    $stmt->execute();
    $rsoID = $dbConn->lastInsertId();

    // Get UserIDs for member email addresses
    foreach ($memberEmails as $email) {

        //Get the user
        $user = users_get_user_from_email($universityId, $email);

        //Check that theyre in the domain
        if ($user == null)
            continue;

        //Add the user to the RSO
        orgs_add_member($rsoID, $user["UserID"]);
    }
}

function orgs_update_rso($universityId, $rsoId, $rsoName, $adminId, $memberEmails) {


    //Get connection
    $dbConn = db_get_connection();

    //Get the RSO
    $rso = orgs_get_rso($universityId, $rsoId);

    //Check if name is taken
    if ($rso["Name"] != $rsoName)
        if (orgs_get_rsoid($universityId, $rsoName))
        return false;

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

        //Check that theyre in the domain
        if ($user == null)
            continue;

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

function orgs_get_user_orgs_admin($universityId, $userId) {

    //Get connection
    $dbConn = db_get_connection();

    // Fetch RSOs for the user's university
    $stmt = $dbConn->prepare('SELECT * FROM RSOs WHERE UniversityID = :universityId AND AdminID = :adminId');
    $stmt->bindParam(':universityId', $universityId);
    $stmt->bindParam(':adminId', $userId);

    $stmt->execute();

    $rsos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return$rsos;
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
    return $count > 0;
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

function events_create_event($universityId, $eventName, $category, $description, $time, $date, $latitude, $longitude, $address, $contactPhone, $contactEmail, $eventType, $rsoID) {

    //Get connection
    $dbConn = db_get_connection();

    //Create the location
    $locationId = locations_create_location($address, $latitude, $longitude);

    if ($eventType != 'rso')
        $rsoID = null;

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


    $stmt = $dbConn->prepare('SELECT * FROM events WHERE UniversityID = :university_id ORDER BY Date ASC');
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

function events_approve_event($universityId, $eventId)
{
    //Get connection
    $dbConn = db_get_connection();

    $stmt = $dbConn->prepare('UPDATE events SET Approved = 1 WHERE UniversityID = :universityId AND EventID = :eventId');
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
    $rating = $stmt->fetchColumn();

    if ($rating == null)
        $rating = 0;

    return $rating;
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
        echo $error;
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

function location_get_location($locationid) {

    //Get connection
    $dbConn = db_get_connection();

    //Get the rating of the event
    $statement = 'SELECT * FROM locations WHERE LocationID = :locationid';

    $stmt = $dbConn->prepare($statement);
    $stmt->bindParam(':locationid', $locationid);


    //Execute the statement
    $stmt->execute();

    //Get the result
    return $stmt->fetch();
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


function delete_comments($commentID) {

    //Get connection
    $dbConn = db_get_connection();

    //Get the rating of the event
    $statement = 'DELETE FROM comments WHERE commentID = :commentID';

    $stmt = $dbConn->prepare($statement);
    $stmt->bindParam(':commentID', $commentID);


    //Execute the statement
    $stmt->execute();

    //Get the result
    return $stmt->fetch();
}

function edit_comments($commentID, $newContent) {

    //Get connection
    $dbConn = db_get_connection();

    $stmt = $dbConn->prepare('UPDATE comments SET Content = :newContent WHERE CommentID = :commentID');
    $stmt->bindParam(':commentID', $commentID);
    $stmt->bindParam(':newContent', $newContent);

    //Execute the statement
    $stmt->execute();

    //Get the result
    return $stmt->fetch();
}

function user_comments($userId, $eventId) {

    //Get connection
    $dbConn = db_get_connection();

    
    // Insert the RSO
    $stmt = $dbConn->prepare('SELECT * FROM comments WHERE (UserId = :userId) AND (EventID = :eventId)');
    $stmt->bindParam(':userId', $userId);
    $stmt->bindParam(':eventId', $eventId);

    //Execute the statement
    $stmt->execute();

    //Get the result
    //$count = $stmt->fetchColumn();
        

    return $stmt->fetchAll();
}


?>

