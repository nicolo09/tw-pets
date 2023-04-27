<?php
require_once("bootstrap.php");

if(!login_check($dbh)){
    header("Location: login.php");
    exit;
}

if(isset($_POST["username"], $_POST["type"]) && !empty($_POST["username"]) && !empty($_POST["type"])){
    $animal = htmlspecialchars($_POST["username"]);
    $type = htmlspecialchars($_POST["type"]);
    if(!empty($_FILES["imgprofile"]["name"])){
        list($result, $msg) = uploadImage(IMG_DIR, $_FILES["imgprofile"]);
        if($result != 0){
            $img = $msg;
        } else {
            header("Location: add_animal.php?msg=".$msg);
            exit;
        }
    } else {
        $img = "default_pet_image.png";
    }
    
    $description = isset($_POST["description"])
        ? htmlspecialchars($_POST["description"]) 
        : "";

    $owners = array();
    $owners[] = $_SESSION["username"];

    if(registerAnimal($animal, $type, $img, $description, $owners, $dbh)){
        // New animal added
        header("Location: my_animals.php");
        exit;
    } else {
        // Unable to add the animal
        header("Location: add_animal.php?error=1");
        exit;
    }
}

$templateParams["page"] = "add-animal-form.php";
$templateParams["img"] = "img/default_pet_image.png";
//$templateParams["user"] = $_SESSION['username']; TODO use it on add-animal-form.php

require_once("template/base.php");
?>