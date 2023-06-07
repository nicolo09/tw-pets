<?php
require_once("bootstrap.php");

if (!login_check($dbh)) {
    header("Location: login.php");
    exit;
}

$n_results = 10;

if(!empty($_SESSION["error"])) {
    $templateParams["errors"] = array($_SESSION["error"]);
    unset($_SESSION["error"]);
}

if(isset($_GET["username"]) && !empty($_GET["username"])) {
    $templateParams["persons"] = $dbh->getPersonsLike($_GET["username"], 0, $n_results);
    $templateParams["animals"] = $dbh->getAnimalsLike($_GET["username"], 0, $n_results);
    $templateParams["search"] = $_GET["username"];
}


$templateParams["title"] = "Cerca";
$templateParams["page"] = "search-users.php";
require_once("template/base.php");
?>
