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
    $templateParams["error"][]="";
    $animals=array();
    if(isset($_POST["selectAnimals"])){
        $animals=array($_POST["selectAnimals"]);
    }

    if (isset($_POST["imgalt"]) && isset($_FILES) && isset($_POST["txtpost"])) {
        $img = $_FILES["imgpost"];
        $alt = $_POST["imgalt"];
        $text = $_POST["txtpost"];
        $postErrors=newPost(getUserName($dbh), $img, $alt, $text, $animals, $dbh);
        if($postErrors[0]!=1){
            //Ci sono stati errori
            $templateParams["error"][]=$postErrors[1];
        }
    } else {
        $templateParams["error"][]="Compila tutti i campi e metti un'immagine con estensione jpg, jpeg, png o gif";
    }
}

$animalList=getManagedAnimals(getUserName($dbh),$dbh);
$templateParams["animals"]=array();
foreach($animalList as $singleAnimal){
    $templateParams["animals"][]=$singleAnimal["username"];
}

if(empty($_POST)==false&&isset($templateParams["error"])==true&&strlen($templateParams["error"][0])==0){
    //Andato a buon fine l'inserimento di un post
    //Magari redirect a tuo profilo, con nuovo post?
    header("Location: home.php");
    exit;
}

$templateParams["page"] = "new-post.php";
$templateParams["title"] = "Crea nuovo post";
require_once("template/base.php");
?>