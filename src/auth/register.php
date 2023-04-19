<?php
include_once('../core/header.php');
include_once('../core/db.php');
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


            //Check if user already exists
            $user = users_user_email_exists($email);

            //If its not zero, it already exists!
            if ($count != 0) {
                $error = "A User with this email already exists!";
            }
        }


        //Bind so we dont have any sql injection issues!
        if ($error == null) {


            //Create the user
            $user = users_get_user($universityId, users_create_user($email, $password, $fullName, $universityId));

            //Login!
            auth_login($email, $password);

            header('Location: /events/index.php');
        }



        //If theres no errors then go back to the index!
        if ($error == null) {
            header('Location: /events/index.php');
        }
    }


    $universities = university_get_all_universities();

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
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required>
                    <div id="emailHelp" class="form-text">University email!</div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" required>
                </div>
                <div class="mb-3">
                    <strong>University: </strong> <select class="form-control" id="universityId" name="universityId">
                        <option disabled selected value> Select an university.... </option>
                        <?php foreach ($universities as $uni) : ?>
                            <option value="<?php echo $uni['UniversityID']; ?>"><?php echo $uni['Name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                </div>
                <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Register</button>
            </form>
        </div>
    </div>
</div>


<?php
include_once('../core/footer.php');
?>
