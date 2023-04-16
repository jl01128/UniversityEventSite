<?php
include_once('core/header.php');
include_once ('core/db.php');

$error = null;
?>



<?php

    //Check if its a form call!
    if (isset($_POST['submit'])) {

        //header('Location: index.php');

        $submit = trim($_POST['submit']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirmPassword']);
        $fullName = trim($_POST['fullName']);
        $universityId = trim($_POST['universityId']);




        //Check that passwords are the same
        if ($password != $confirmPassword) {
            $error = "Passwords do not match!";
        }

        //Check that the user does not already exist
        if ($error == null) {


            //Create the statement.
            $statement = 'SELECT COUNT(*) FROM Users WHERE Email = :email';

            $stmt = $dbConn->prepare($statement);
            $stmt->bindParam(':email', $email);

            //Execute the statement
            $stmt->execute();

            //Get the result
            $count = $stmt->fetchColumn();

            //If its not zero, it already exists!
            if ($count != 0) {
                $error = "A User with this email already exists!";
            }
        }


        //Bind so we dont have any sql injection issues!
        if ($error == null) {


            //Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            //Create the statement.
            $statement = 'INSERT INTO Users (Email, Password, FullName, UniversityID) VALUES (:email, :password, :fullName, :universityID)';

            $stmt = $dbConn->prepare($statement);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':fullName', $fullName);
            $stmt->bindParam(':universityID', $universityId);

            //Execute the statement
            $stmt->execute();
        }




        //If theres no errors then go back to the index!
        if ($error == null) {
            header('Location: index.php');
        }
    }

?>

<?php if($error != null) : ?>
    <div class="alert alert-danger text-center" role="alert">
        Error: <?php echo $error; ?>
    </div>
<?php endif; ?>

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
