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

            <h1 class="h3 mb-3 fw-normal">Sign In</h1>
            <form class="text-start">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                    <div id="emailHelp" class="form-text">University email!</div>
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1">
                </div>
                <button type="submit" class="btn btn-primary text-center">Login</button>
            </form>
        </div>
    </div>
</div>


<?php
include_once('core/footer.php');
?>
