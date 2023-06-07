<?php
require_once("bootstrap.php");

// If the user is already logged they gets sent to the home page
if (login_check($dbh)) {
    header("Location: home.php");
    exit;
}

// The user submitted login credentials
if (isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $result = loginUser($username, $password, $dbh);
    if ($result[0] == true) {
        // Login was successful
        header("Location: home.php");
        exit;
    } else {
        // Login failed
        $templateParams["errors"] = $result[1];
    }
}

if (isset($_GET["password_changed"]) && $_GET["password_changed"] == true) {
    $templateParams["messages"][] = "Password cambiata con successo. Effettua nuovamente il login";
}

if(!empty($_SESSION["error"])) {
    $templateParams["errors"] = array($_SESSION["error"]);
    unset($_SESSION["error"]);
}

if(!empty($_SESSION["message"])) {
    $templateParams["messages"] = array($_SESSION["message"]);
    unset($_SESSION["message"]);
}

$templateParams["title"] = "PETS - Accedi";
$templateParams["page"] = "login-form.php";
require_once("template/base-outside.php");

?>
