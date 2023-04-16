<?php
include_once('core/header.php');
include_once ('core/db.php');
?>



<?php

    //Check if its a form call!
    if (isset($_POST['submit'])) {

        header('Location: index.php');

        $submit = trim($_POST['submit']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $fullName = trim($_POST['fullName']);
        $universityId = trim($_POST['universityId']);


        //Create the password.
        $sql = 'INSERT INTO Users (Email, Password, FullName, UniversityID) VALUES (:email, :password, :fullName, :universityID)';

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        //Bind so we dont have any sql injection issues!
        $stmt = $dbConn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':fullName', $fullName);
        $stmt->bindParam(':universityID', $universityId);

        if ($stmt->execute()) {
            //header('Location: login.php');
            //exit();
        } else {
            $error = 'Error: Unable to register';
        }

    }

?>

<div class="h-100 d-flex align-items-center justify-content-center">

    <!--Create login card.-->
    <div class="card text-center w-25">
        <div class="card-body">

            <h1 class="h3 mb-3 fw-normal">Register</h1>
            <form class="text-start" action="register.php" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">University Email address</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
                    <div id="emailHelp" class="form-text">University email!</div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullName" name="fullName">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">University ID Number</label>
                    <input type="number" class="form-control" id="universityId" name="universityId">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                </div>
                <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Register</button>
            </form>
        </div>
    </div>
</div>


<?php
include_once('core/footer.php');
?>
