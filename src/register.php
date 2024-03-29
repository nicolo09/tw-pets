<?php
require_once("bootstrap.php");

if (login_check($dbh)) {
    header("Location: index.php");
    exit;
}

if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"])  && isset($_POST["confirm_password"])) {
    $result = register($_POST["username"], $_POST["email"], $_POST["password"], $_POST["confirm_password"], $dbh);
    if ($result[0] == 1) {
        loginUser($_POST["username"], $_POST["password"], $dbh);
        header("Location: home.php");
        exit;
    } else {
        $templateParams["errors"] = $result[1];
    }
}

$templateParams["title"] = "PETS - Iscriviti";
$templateParams["page"] = "register-form.php";
require_once("template/base-outside.php");
