<?php

//Include the DB
include_once('../core/core.php');
?>

<!doctype html>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RSO Website</title>
  </head>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark" aria-label="Ninth navbar example">
    <div class="container-xl">
      <a class="navbar-brand" href="#">University Site</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-header" aria-controls="navbar-header" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbar-header">

        <!-- Only if logged in! -->
        <?php if(isLoggedIn()) : ?>

        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/organizations/index.php">RSOs</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/organizations/create_rso.php">Create RSO</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/events/index.php">Events</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/events/create_event.php">Create Event</a>
          </li>
        </ul>
        
        <?php endif; ?>

        <ul class="navbar-nav ml-auto">
        <?php if(isLoggedIn()) : ?>
            <p class ="fw-normal bg-light"> Hello, <?php echo $_SESSION["user_fullname"]; ?></p>
            <li class="nav-item">
              <a class="nav-link" href="/auth/logout.php">Logout</a>
            </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="/auth/login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/auth/register.php">Register</a>
          </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
  <body class="vh-100">
</html>