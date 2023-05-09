<?php
require_once("bootstrap.php");

//Possibili costanti del parametro type
define("PERSON", "person");
define("ANIMAL", "animal");

$username = "";
$type = PERSON;

if (isset($_GET["username"])) {
    $username = $_GET["username"];
    if (isset($_GET["type"])) {
        $type = $_GET["type"];
    } else {
        //Se il tipo non è settato è una persona
        $type = PERSON;
    }
    if ($type == PERSON) {
        //Controlla se utente esiste e se non è se stesso
        if (doesPersonUsernameExist($username, $dbh) && $username != getUserName($dbh)) {
            //Posso seguirlo
            if (doesUserFollowMe($username, getUserName($dbh), $dbh)) {
                //Lo seguo, faccio unfollow
                $out = unfollowPerson($username, getUserName($dbh), $dbh);
                if ($out == false) {
                    $templateParams["error"] = "Errore nello smettere di seguire l'utente";
                }
            } else {
                //Non lo seguo, faccio follow
                $out = followPerson($username, getUserName($dbh), $dbh);
                if ($out == false) {
                    $templateParams["error"] = "Errore nel seguire l'utente";
                }
            }
        }
        header("Location: view-user-profile.php?username" . $username . "&type=" . $type);
    } else if ($type == ANIMAL) {
        //Controlla se animale esiste e se non è il mio
        if (doesAnimalUsernameExist($username, $dbh) && isAnimalManagedByMe(getUserName($dbh), $username, $dbh) == false) {
            //Posso seguirlo
            if (doIFollowAnimal(getUserName($dbh), $animal, $dbh)) {
                //Lo seguo, faccio unfollow
                $out = unfollowAnimal($username, getUserName($dbh), $dbh);
                if ($out == false) {
                    $templateParams["error"] = "Errore nello smettere di seguire l'animale";
                }
            } else {
                //Non lo seguo, faccio follow
                $out = followAnimal($username, getUserName($dbh), $dbh);
                if ($out == false) {
                    $templateParams["error"] = "Errore nel seguire l'animale";
                }
            }
        }
        header("Location: view-user-profile.php?username" . $username . "&type=" . $type);
    }
}

header("Location: view-user-profile.php");
