<?php
require_once("bootstrap.php");

if (login_check($dbh)) {
    logoutUser($dbh);
    header("Location: login.php");
    exit;
}
