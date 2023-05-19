<?php
require_once("bootstrap.php");

if(!login_check($dbh)){
    header("Location: login.php");
    exit;
}

if(!empty($_SESSION["error"])) {
    $templateParams["errors"] = array($_SESSION["error"]);
    unset($_SESSION["error"]);
}

if(!empty($_SESSION["message"])) {
    $templateParams["messages"] = array($_SESSION["message"]);
    unset($_SESSION["message"]);
}

if(isset($_GET["user"]) && count($dbh->getUserFromName($_GET["user"])) == 1){
    $templateParams["user"] = $_GET["user"]; 
} else {
    $templateParams["user"] = getUserName($dbh);
}

$templateParams["title"] = "Animali di " . $templateParams["user"];
$templateParams["page"] = "base-animal-list.php";
$templateParams["animals"] = $dbh->getOwnedAnimals($templateParams["user"]);

require_once("template/base.php");
?>
