<?php
require_once("bootstrap.php");
define("DEFAULT_IMG", "default_pet_image.png");

if(!login_check($dbh)){
    header("Location: login.php");
    exit;
}

if(isset($_POST["username"], $_POST["type"])){
    
    $animal = htmlspecialchars($_POST["username"]);
    $type = htmlspecialchars($_POST["type"]);
    $description = htmlspecialchars($_POST["description"]);

    $owners = $_POST["owners"]; 
    $owners[] = $_SESSION["username"];

    if(!isset($_GET["animal"])){
        /* No animal was set, so a new one has to be added  */
        list($result, $error) = registerAnimal($animal, $type, $_FILES, $description, $owners, $dbh);
        if($result == 1){
            // New animal added
            header("Location: profile-animals.php");
            exit;
        } else {
            // Unable to add the animal
            $templateParams["errors"] = $error;
        }
    }
} elseif(isset($_GET["animal"], $_POST["type"])) {

    $animal = $dbh->getAnimals($_GET["animal"]);

    $type = htmlspecialchars($_POST["type"]);
    $description = htmlspecialchars($_POST["description"]);

    $owners = $_POST["owners"]; 
    $owners[] = $_SESSION["username"];
    /* An animal was set, so it must be updated */
    list($result, $error) = editAnimal($animal[0], $type, $_FILES, $description, $owners, $dbh);
    if($result == 1){
        // Animal profile edited
        header("Location: profile-animals.php");
        exit;
    } else {
        // Unable to edit the animal profile
        $templateParams["errors"] = $error;
    } 
}

$templateParams["page"] = "manage-animal-form.php";
if(isset($_GET["animal"])){
    
    $animal = $dbh->getAnimals($_GET["animal"]);
    if(count($animal) != 1) {
        $msg = "Animale " . $_GET["animal"] . " non trovato";
    } elseif (!$dbh->checkOwnership($_SESSION["username"], $animal[0]["username"])) { 
        $msg = "Non puoi modificare l'account di " . $animal[0]["username"] . " non essendone proprietario";
    }

    if(!empty($msg)){
        header("Location: profile-animals.php?error=" . $msg);
        exit;
    }

    $templateParams["animal"] = $animal[0]["username"];
    $templateParams["type"] = $animal[0]["tipo"];
    $templateParams["img"] = IMG_DIR . $animal[0]["immagine"];
    $templateParams["description"] = $animal[0]["descrizione"];
    $templateParams["title"] = "Modifica - " . $animal[0]["username"];
    $templateParams["subtitle"] = $templateParams["title"];
    $templateParams["owners"] = $dbh->getOwners($animal[0]["username"]);

} else {
    $templateParams["img"] = "img/default_pet_image.png";
    $templateParams["title"] = "Nuovo animale";
    $templateParams["subtitle"] = "Aggiungi un nuovo animale!";
}

$templateParams["mutuals"] = $dbh->getMutualFollowers($_SESSION["username"]);
//$templateParams["user"] = $_SESSION['username']; TODO use it on manage-animal-form.php

require_once("template/base.php");
?>
