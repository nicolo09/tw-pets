<?php
require_once("bootstrap.php");

if (!login_check($dbh)) {
    header("Location: login.php");
    exit;
}

$n_results = 10;

//AJAX request
if(isset($_GET["user"]) && isset($_GET["type"]) && isset($_GET["offset"])){

    $html = "";

    if($_GET["type"] == "animal"){
        $results = $dbh->getAnimalFollowers($_GET["user"], $_GET["offset"], $n_results);
    } elseif($_GET["type"] == "person") {
        $results = $dbh->getPersonFollowers($_GET["user"], $_GET["offset"], $n_results);
    }

    foreach($results as $user) {
        $username = $user["username"];
        $img = $user["immagine"];
        $href = getProfileHref($username, $_GET["type"]);
        ob_start();
        require("template/result-bar.php");
        $html .= ob_get_clean();
    }
    
    echo json_encode($html);
    exit;
}

//User request
if(isset($_GET["animal"]) && !empty($_GET["animal"])) {

    if(doesAnimalUsernameExist($_GET["animal"], $dbh)) {
        $results = $dbh->getAnimalFollowers($_GET["animal"], 0, $n_results);
        $templateParams["type"] = "animal";
        $templateParams["user"] = $_GET["animal"];
        $owners = $dbh->getOwners($_GET["animal"]);
    } else {
        $_SESSION["error"] = "L'animale " . $_GET["animal"] . " non è stato trovato, impossibile mostrarne i follower";
        header("Location: tab-profile.php");
        exit;
    }

} elseif (isset($_GET["person"]) && !empty($_GET["person"])) {
    
    if(doesPersonUsernameExist($_GET["person"], $dbh)) {
        $results = $dbh->getPersonFollowers($_GET["person"], 0, $n_results);
        $templateParams["type"] = "person";
        $templateParams["user"] = $_GET["person"];
    } else {
        $_SESSION["error"] = "L'utente " . $_GET["person"] . " non è stato trovato, impossibile mostrarne i follower";
        header("Location: tab-profile.php");
        exit;
    }

} else {
    header("Location: profile-followers.php?person=".getUserName($dbh));
    exit;
}

$templateParams["title"] = "Followers di " . $templateParams["user"];
$templateParams["page"] = "followers-list.php";
require_once("template/base.php");

?>
