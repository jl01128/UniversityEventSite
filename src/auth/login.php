<?php
include_once('../core/header.php');

$error = null;
?>



<?php

    //Check if its a form call!
    if (isset($_POST['submit'])) {
        //header('Location: index.php');

        $email = trim($_POST['email']);
        $password = trim($_POST['password']);


        auth_login($email, $password);


        //If theres no errors then go back to the index!
        if ($error == null) {
            header('Location: /index.php');
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

            <h1 class="h3 mb-3 fw-normal">Login</h1>
            <form class="text-start" action="login.php" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Login</button>
            </form>
        </div>
    </div>
</div>


<?php
include_once('../core/footer.php');
?>
