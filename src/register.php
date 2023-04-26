<?php
require_once("bootstrap.php");

if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    
}

$templateParams["page"] = "register-form.php";
require_once("template/base-outside.php");

?>
