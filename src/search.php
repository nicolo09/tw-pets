<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

$templateParams["page"] = "search-users.php";
require_once("template/base.php");
?>