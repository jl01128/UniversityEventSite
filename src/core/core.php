<?php

//Include the DB
include 'db.php';

$error = null;

//Start the session
session_start();

function isLoggedIn()
{
    return isset($_SESSION["user_id"]);
}

?>
