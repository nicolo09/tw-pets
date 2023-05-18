<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

if(isset($_GET["animal"]) && !empty($_GET["animal"])) {

    if(doesAnimalUsernameExist($_GET["animal"], $dbh)) {
        $templateParams["results"] = $dbh->getAnimalFollowers($_GET["animal"], 0);
        $templateParams["type"] = "animal";
        $templateParams["user"] = $_GET["animal"];
        $templateParams["owners"] = $dbh->getOwners($_GET["animal"]);
    } else {
        $_SESSION["error"] = "L'animale " . $_GET["animal"] . " non è stato trovato, impossibile mostrarne i follower";
        header("Location: tab-profile.php");
        exit;
    }

} elseif (isset($_GET["person"]) && !empty($_GET["person"])) {
    
    if(doesPersonUsernameExist($_GET["person"], $dbh)) {
        $templateParams["results"] = $dbh->getPersonFollowers($_GET["person"], 0);
        $templateParams["type"] = "person";
        $templateParams["user"] = $_GET["person"];
    } else {
        $_SESSION["error"] = "L'utente " . $_GET["person"] . " non è stato trovato, impossibile mostrarne i follower";
        header("Location: tab-profile.php");
        exit;
    }

} else {
    header("Location: followers.php?person=".getUserName($dbh));
    exit;
}

$templateParams["page"] = "followers-list.php";
require_once("template/base.php");

?>
