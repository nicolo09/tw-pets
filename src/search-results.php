<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

if(isset($_GET["animals"]) && !empty($_GET["animals"])) {
    $templateParams["results"] = $dbh->getAnimalsLike($_GET["animals"], 0);
    $templateParams["type"] = "Animali";
    $templateParams["search"] = $_GET["animals"];
} elseif (isset($_GET["persons"]) && !empty($_GET["persons"])) {
    $templateParams["results"] = $dbh->getPersonsLike($_GET["persons"], 0);
    $templateParams["type"] = "Persone";
    $templateParams["search"] = $_GET["persons"];
} else {
    header("Location: search.php?error=Errore, ricerca nulla");
    exit;
}

$templateParams["page"] = "full-search-results.php";
require_once("template/base.php");
?>