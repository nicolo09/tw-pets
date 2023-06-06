<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

$templateParams["title"] = "Seguiti";
$templateParams["page"] = "view-followed.php";
require_once("template/base.php");