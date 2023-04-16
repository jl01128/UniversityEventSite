<?php
include_once('core/header.php');
?>



<?php

    //Check if its a form call!
    if (isset($_POST['submit'])) {


    }

?>

<div class="h-100 d-flex align-items-center justify-content-center">

    <!--Create login card.-->
    <div class="card text-center w-25">
        <div class="card-body">

            <h1 class="h3 mb-3 fw-normal">Register</h1>
            <form class="text-start">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" aria-describedby="emailHelp">
                    <div id="emailHelp" class="form-text">University email!</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password">
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword">
                </div>
                <button type="submit" class="btn btn-primary text-center">Register</button>
            </form>
        </div>
    </div>
</div>


<?php
include_once('core/footer.php');
?>
