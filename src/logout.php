<?php
require_once("bootstrap.php");

if (login_check($dbh)) {
    logoutUser();
    header("Location: login.php");
    exit;
}
