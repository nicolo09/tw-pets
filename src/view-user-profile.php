<?php
require_once("bootstrap.php");

if (isUserLoggedIn($dbh) == false) {
    header("Location: login.php");
    exit;
}

define("PERSON", "person");
define("ANIMAL", "animal");

if (isset($_GET["type"], $_GET["username"]) && ($_GET["type"] == ANIMAL || $_GET["type"] == PERSON)) {

    $type = $_GET["type"];
    $currentUser = getUserName($dbh);
    $username = $_GET["username"];

    if($type == PERSON) {
        if(!doesPersonUsernameExist($username, $dbh) == 1) {
            $_SESSION["error"] = "Profilo utente di " . $username . " non trovato";
            header("Location: tab-profile.php");
            exit;
        }
        $templateParams["animalsDisabled"] = !doesUserOwnAnimals($username, $dbh);
        $followDisabled = $username === $currentUser; //if true it's looking at own account /*TODO change follow button to modify button*/
        $userFollows = !$followDisabled && doesUserFollowMe($username, $currentUser, $dbh);//if true it's following this account
        $data = getUserData($username, $dbh);
    } else {
        if(!doesAnimalUsernameExist($username, $dbh) == 1) {
            $_SESSION["error"] = "Profilo dell'animale " . $username . " non trovato";
            header("Location: tab-profile.php");
            exit;
        }
        $templateParams["animalAccount"] = true;
        $followDisabled = $dbh->checkOwnership($currentUser, $username); //if true it's looking at own animal /*TODO change follow button to modify button*/
        $userFollows = $followDisabled || doIFollowAnimal($currentUser, $username, $dbh); //if true it's following this account
        $data = getAnimalData($username, $dbh);
    }

    if(empty($data)) {
        $_SESSION["error"] = "Si è verificato un problema di comunicazione col server, verificare la connessione";
        header("Location: tab-profile.php");
        exit;
    }

    $templateParams["username"] = $data["username"];
    $templateParams["img"] = IMG_DIR . $data["immagine"];
    $templateParams["role"] = $type == PERSON ? "Persona/" . $data["impiego"] : "Animale/" . $data["tipo"];
    $templateParams["description"] = $data["descrizione"];

    $posts = $type == PERSON ? getUserCreatedPosts($username, $dbh) : getAnimalRelatedPosts($username, $dbh);
    if (!empty($posts)) {
        foreach ($posts as $single) {
            $templateParams["postimg"][] = IMG_DIR . $single["immagine"];
            $templateParams["alt"][] = $single["alt"];
            $templateParams["id"][] = $single["id_post"];
        }
    }

    $templateParams["followDisabled"] = $followDisabled;
    $templateParams["userFollows"] = $userFollows;
    $templateParams["title"] = "Pagina profilo di " . $username;

} elseif (isset($_GET["type"]) || isset($_GET["username"])) {
    //The user must have modified the link, causes error
    $_SESSION["error"] = "Errore, link corrotto";
    header("Location: tab-profile.php");
    exit;

} else {
    //The user is going to its account
    header("Location: view-user-profile.php?username=" . getUserName($dbh) . "&type=" . PERSON);
    exit;
}

if(isset($_GET["success"]) && $_GET["success"] == 0){
    //Outcome del follow
    $templateParams["success"]=$_GET["success"];
}

$templateParams["page"] = "user-profile.php";
require_once("template/base.php");

?>
