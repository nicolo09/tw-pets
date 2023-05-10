<?php
require_once("bootstrap.php");

# Se l'utente è già loggato, viene reindirizzato alla home

if (isUserLoggedIn($dbh) == false) {
    header("Location: login.php");
    exit;
}
#Altrimenti può guardare un utente
#Carico i dati utente da db

#se non metti nulla o altro->cerco tra persone
#se metti animal->cerco tra animali
#se metti person->cerco tra persone

define("PERSON", "person");
define("ANIMAL", "animal");

$profileType = "";
if (isset($_GET["type"])) {
    if ($_GET["type"] == PERSON) {
        $profileType = PERSON;
    } else if ($_GET["type"] == ANIMAL) {
        $profileType = ANIMAL;
    } else {
        $profileType = PERSON;
    }
} else {
    $profileType = PERSON;
}

$username = "";
if ($profileType == PERSON) {
    //Profilo persona
    if (isset($_GET["username"])) {
        if (doesPersonUsernameExist($_GET["username"], $dbh) == 1) {
            $username = $_GET["username"];
        }
    } else {
        //Se non hai settato username guardo tuo profilo
        $username = getUserName($dbh);
    }
} else {
    //Profilo animale
    if (isset($_GET["username"])) {
        if (doesAnimalUsernameExist($_GET["username"], $dbh) == 1) {
            $username = $_GET["username"];
        }
        //Non esiste l'animale
    } else {
        //Non hai messo un username, ma hai messo un animale->redirect a tuo profilo
        header("Location: view-user-profile.php");
        exit;
    }
}

if ($profileType == PERSON) {
    if (empty($username) == false) {
        $user = getUserData($username, $dbh);
        if (empty($user) == false) {
            $templateParams["username"] = $user["username"];
            $templateParams["img"] = "img/" . $user["immagine"];
            $templateParams["role"] = "Persona/".$user["impiego"];
            $templateParams["description"] = $user["descrizione"];
        }
        $posts = getUserCreatedPosts($username, $dbh);
        if (empty($posts) == false) {
            foreach ($posts as $single) {
                $templateParams["postimg"][] = "img/" . $single["immagine"];
                $templateParams["alt"][] = $single["alt"];
                $templateParams["id"][] = $single["id_post"];
            }
        }
        $templateParams["title"] = "Pagina profilo di " . $username;
        if ($username === getUserName($dbh)) {
            //E' il tuo profilo
            $templateParams["followDisabled"] = true;
            $templateParams["animalsDisabled"] = false;
            $templateParams["followersDisabled"] = false;
        } else {
            //E' il profilo di qualcun altro
            $templateParams["followDisabled"] = false;
            $templateParams["animalsDisabled"] = false;
            $templateParams["followersDisabled"] = false;
            if (doesUserFollowMe($username, getUserName($dbh), $dbh)) {
                //Cambia icona
                $templateParams["userFollows"] = true;
            } else {
                //Utente non mi segue
                $templateParams["userFollows"] = false;
            }
        }
        $ownsAnimals = doesUserOwnAnimals($username, $dbh);
        if ($ownsAnimals == false) {
            //Se non ha animali non posso premere il tasto
            //Altrimenti si, ma solo se utente esiste ecc... quindi seguo le regole sopra
            $templateParams["animalsDisabled"] = true;
        }
    } else {
        //Non c'è l'utente che vuoi
        $templateParams["title"] = "Utente non esiste";
        $templateParams["username"] = "Utente non esiste";
        $templateParams["img"] = "#";
        $templateParams["role"] = "Utente non esiste";
        $templateParams["description"] = "Utente non esiste";
        $templateParams["followDisabled"] = true;
        $templateParams["animalsDisabled"] = true;
        $templateParams["followersDisabled"] = true;
    }
} else {
    if (empty($username) == false) {
        $user = getAnimalData($username, $dbh);
        if (empty($user) == false) {
            $templateParams["username"] = $user["username"];
            $templateParams["img"] = "img/" . $user["immagine"];
            $templateParams["role"] = "Animale/".$user["tipo"];
            $templateParams["description"] = $user["descrizione"];
        }
        $posts = getAnimalRelatedPosts($username, $dbh);
        if (empty($posts) == false) {
            foreach ($posts as $single) {
                $templateParams["postimg"][] = "img/" . $single["immagine"];
                $templateParams["alt"][] = $single["alt"];
                $templateParams["id"][] = $single["id_post"];
            }
        }
        $templateParams["title"] = "Pagina profilo di " . $username;
        //Se è un tuo animale non puoi fare nulla, lo segui di default
        $owned=getManagedAnimals(getUsername($dbh), $dbh);
        $isOwned=false;
        foreach($owned as $animal){
            if($animal["animale"]==$username){
                //Possiedi l'animale
                $isOwned=true;
            }
        }
        //Rimuovi bottone animali, sei nel profilo già per animali
        $templateParams["animalAccount"]=true;
        if($isOwned){
            //Segui animale, spegni bottone 
            $templateParams["followDisabled"]=true;
            $templateParams["followersDisabled"]=false;
            $templateParams["userFollows"] = true;
        }else{
            $templateParams["followDisabled"]=false;
            $templateParams["followersDisabled"]=false;
            //Segui questo animale di qualcun altro?
            if(doIFollowAnimal(getUserName($dbh), $username, $dbh)){
                //Seguo animale
                $templateParams["userFollows"] = true;
            }else{
                //Non seguo animale
                $templateParams["userFollows"] = false;
            }
        }
        
    } else {
        //Non c'è l'animale che vuoi
        $templateParams["animalAccount"]=true;
        $templateParams["title"] = "Animale non esiste";
        $templateParams["username"] = "Animale non esiste";
        $templateParams["img"] = "img/default_pet_image.png";
        $templateParams["role"] = "Animale non esiste";
        $templateParams["description"] = "Animale non esiste";
        $templateParams["followDisabled"] = true;
        $templateParams["animalsDisabled"] = true;
        $templateParams["followersDisabled"] = true;
    }
}

$templateParams["page"] = "user-profile.php";
require_once("template/base.php");
