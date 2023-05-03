<?php
require_once("bootstrap.php");

if(!login_check($dbh)){
    header("Location: login.php");
    exit;
}

if(isset($_GET["user"]) && count($dbh->getUserFromName($_GET["user"])) == 1){
    $templateParams["user"] = $_GET["user"]; 
} else {
    $templateParams["user"] = $_SESSION["username"];
}

if(isset($_GET["error"])){
    $templateParams["error"] = $_GET["error"];
}

$templateParams["title"] = "Animali di " . $templateParams["user"];
$templateParams["page"] = "base-animal-list.php";
$templateParams["animals"] = $dbh->getOwnedAnimals($templateParams["user"]);

require_once("template/base.php");
?>
