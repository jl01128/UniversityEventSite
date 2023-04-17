<?php

include_once '../core/core.php';

//Redirect them to login if they are not logged in
if(!isLoggedIn()) {
    header('Location: /auth/login.php');

}