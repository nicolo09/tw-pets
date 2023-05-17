<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

if(isset($_GET["animal"]) && !empty($_GET["animal"])) {

    $templateParams["results"] = $dbh->getAnimalFollowers($_GET["animal"], 0);
    $templateParams["type"] = "animal";
    $templateParams["user"] = $_GET["animal"];
    $templateParams["owners"] = $dbh->getOwners($_GET["animal"]);

} elseif (isset($_GET["person"]) && !empty($_GET["person"])) {

    $templateParams["results"] = $dbh->getPersonFollowers($_GET["person"], 0);
    $templateParams["type"] = "person";
    $templateParams["user"] = $_GET["person"];

} else {
    header("Location: followers.php?person=".getUserName($dbh));
    exit;
}

$templateParams["page"] = "followers-list.php";
require_once("template/base.php");

?>
