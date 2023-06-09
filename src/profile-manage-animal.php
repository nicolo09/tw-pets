<?php
require_once("bootstrap.php");

if(!login_check($dbh)){
    header("Location: login.php");
    exit;
}

if(isset($_POST["username"], $_POST["type"])){
    
    $animal = htmlspecialchars($_POST["username"]);
    $type = htmlspecialchars($_POST["type"]);
    $description = htmlspecialchars($_POST["description"]);

    $owners = isset($_POST["owners"]) ? $_POST["owners"] : array(); 
    $owners[] = getUserName($dbh);

    if(!isset($_GET["animal"])){
        /* No animal was set, so a new one has to be added  */
        list($result, $error) = registerAnimal($animal, $type, $_FILES, $description, $owners, $dbh);
        if($result == 1){
            // New animal added
            $_SESSION["message"] = "Aggiunto " . $animal . " con successo";
            header("Location: profile-animals.php");
            exit;
        } else {
            // Unable to add the animal
            $templateParams["errors"] = $error;
        }
    }
} elseif(isset($_GET["animal"], $_POST["type"])) {

    $animal = $dbh->getAnimalFromName($_GET["animal"]);

    $type = htmlspecialchars($_POST["type"]);
    $description = htmlspecialchars($_POST["description"]);

    $owners = isset($_POST["owners"]) ? $_POST["owners"] : array(); 
    $owners[] = getUserName($dbh);
    /* An animal was set, so it must be updated */
    list($result, $error) = editAnimal($animal, $type, $_FILES, $description, $owners, $dbh);
    if($result == 1){
        // Animal profile edited
        $_SESSION["message"] = "Modificato " . $animal["username"] . " con successo";
        header("Location: profile-animals.php");
        exit;
    } else {
        // Unable to edit the animal profile
        $templateParams["errors"] = $error;
    } 
}

if(isset($_GET["animal"])){
    
    $animal = $dbh->getAnimalFromName($_GET["animal"]);
    if(empty($animal)) {
        $msg = "Animale " . $_GET["animal"] . " non trovato";
    } elseif (!$dbh->checkOwnership(getUserName($dbh), $animal["username"])) { 
        $msg = "Non puoi modificare l'account di " . $animal["username"] . " non essendone proprietario";
    }

    if(!empty($msg)){
        $_SESSION["error"] = $msg;
        header("Location: profile-animals.php");
        exit;
    }

    $templateParams["animal"] = $animal["username"];
    $templateParams["type"] = $animal["tipo"];
    $templateParams["img"] = IMG_DIR . $animal["immagine"];
    $templateParams["description"] = $animal["descrizione"];
    $templateParams["title"] = "Modifica - " . $animal["username"];
    $templateParams["subtitle"] = $templateParams["title"];
    $templateParams["owners"] = array_column($dbh->getOwners($animal["username"]), "username");

} else {
    $templateParams["img"] = IMG_DIR . "default_pet_image.png";
    $templateParams["title"] = "Nuovo animale";
    $templateParams["subtitle"] = "Aggiungi un nuovo animale!";
}

$templateParams["page"] = "manage-animal-form.php";
$templateParams["mutuals"] = $dbh->getMutualFollowers(getUserName($dbh));

require_once("template/base.php");
?>
