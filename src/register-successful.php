<?php
require_once("bootstrap.php");

if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit;
}


?>