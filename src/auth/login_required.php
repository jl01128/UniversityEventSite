<?php

include_once '../core/core.php';

//Redirect them to login if they are not logged in
if(!isset($_SESSION["user_id"])) {
    header('Location: /auth/login.php');

}