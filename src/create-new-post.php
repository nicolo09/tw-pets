<?php
require_once("bootstrap.php");

# Se l'utente è già loggato, viene reindirizzato alla home

if (isUserLoggedIn($dbh)==false) {
    header("Location: login.php");
    exit;
}
#Altrimenti può creare un nuovo post

if (empty($_POST)) {
    //Non è stato inviato nulla per post, probabilmente viene fatto accesso alla pagina direttamente
} else {
    $animals=array();
    if(isset($_POST["selectAnimals"])){
        $animals=$_POST["selectAnimals"];
    }

    if (isset($_POST["imgalt"]) && isset($_FILES) && isset($_POST["txtpost"])) {
        $img = $_FILES["imgpost"];
        var_dump($img);
        $alt = $_POST["imgalt"];
        $text = $_POST["txtpost"];
        newPost(getUserName($dbh), $img, $alt, $text, $animals, $dbh);

    } else {
        $templateParams["error"] = "Compila tutti i campi e metti un'immagine con estensione jpg, jpeg, png o gif";
    }
}

$animalList=getManagedAnimals(getUserName($dbh),$dbh);
$templateParams["animals"]=array();
foreach($animalList as $singleAnimal){
    $templateParams["animals"][]=$singleAnimal["username"];
}

$templateParams["page"] = "new-post.php";
$templateParams["title"] = "Crea nuovo post";
require_once("template/base.php");
?>