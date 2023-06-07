<?php
require_once("bootstrap.php");

if (login_check($dbh)==false) {
    header("Location: login.php");
    exit;
}

// The user is logged in, so they can make a post

if (empty($_POST)) {
    // $_POST variable is empty, the user opened the page
} else {
    $animals=array();
    if(isset($_POST["selectAnimals"])){
        $animals=$_POST["selectAnimals"];
    }

    if (isset($_POST["imgalt"]) && isset($_FILES) && isset($_POST["txtpost"])) {
        $img = $_FILES["imgpost"];
        $alt = htmlspecialchars($_POST["imgalt"]);
        $text = htmlspecialchars($_POST["txtpost"]);
        $postErrors=newPost(getUserName($dbh), $img, $alt, $text, $animals, $dbh);
        if($postErrors[0]!=1){
            //Some error occurred
            $templateParams["errors"][]=$postErrors[1];
        }
    } else {
        $templateParams["errors"][]="Compila tutti i campi e metti un'immagine con estensione jpg, jpeg, png o gif";
    }
}

$animalList=getManagedAnimals(getUserName($dbh),$dbh);
$templateParams["animals"]=array();
foreach($animalList as $singleAnimal){
    $templateParams["animals"][]=$singleAnimal["username"];
    $templateParams["animalsImg"][]=IMG_DIR.$singleAnimal["immagine"];
}

if(empty($_POST)==false&&isset($templateParams["errors"])==false){
    // Post was created successfully
    $_SESSION["message"] = "Hai creato un post!";
    header("Location: ".getUserProfileHref(getUserName($dbh)));
    exit;
}

$templateParams["page"] = "new-post.php";
$templateParams["title"] = "Crea nuovo post";
require_once("template/base.php");
?>