<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
} else {
    header("Location: home.php");
    exit;
}
?>
