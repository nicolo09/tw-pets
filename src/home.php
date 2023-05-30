<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

$templateParams["title"] = "Home";
$templateParams["page"] = "home-posts.php";
$templateParams["home"] = true;
require_once("template/base.php");
?>
