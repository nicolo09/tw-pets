<?php
require_once("bootstrap.php");

if(!login_check($dbh)){
    header("Location: login.php");
    exit;
}

$templateParams["page"] = "base-animal-list.php";
$templateParams["user"] = $_SESSION['username']; 
$templateParams["animals"] = $dbh->getAnimalsFromUser($_SESSION['username']);

require_once("template/base.php");
?>
