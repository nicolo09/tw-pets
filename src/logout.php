<?php
require_once("bootstrap.php");

if (isUserLoggedIn($dbh)) {
    logoutUser($dbh);
    header("Location: login.php");
    exit;
}
