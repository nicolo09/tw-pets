<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

if (isset($_GET["json"])) {
    header("Content-Type: application/json");
    echo json_encode($dbh->getSettings($_SESSION["username"])[0]);
    exit;
}

if (isset($_GET["success"]) && $_GET["success"] == "1") {
    $templateParams["messages"][] = "Operazione eseguita con successo";
}

if (isset($_POST["setting"]) && isset($_POST["value"])) {
    isset($_SESSION["username"]);
    $dbh->updateSetting($_SESSION["username"], $_POST["setting"], $_POST["value"]);
}

$templateParams["page"] = "settings.php";
require_once("template/base.php");

?>
