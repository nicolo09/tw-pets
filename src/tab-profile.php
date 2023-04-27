<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

$templateParams["page"] = "my-profile.php";
require_once("template/base.php");

?>
