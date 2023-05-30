<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}
$templateParams["title"]="Home";
require_once("template/base.php");
?>