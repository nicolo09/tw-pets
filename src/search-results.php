<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

//AJAX request
if(isset($_GET["search"]) && isset($_GET["type"]) && isset($_GET["offset"])){

    $n_results = 10;
    $html = "";

    if($_GET["type"] == "animal") {
        $results = $dbh->getAnimalsLike($_GET["search"], $_GET["offset"], $n_results);
    } elseif($_GET["type"] == "person") {
        $results = $dbh->getPersonsLike($_GET["search"], $_GET["offset"], $n_results); 
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
if(isset($_GET["animals"]) && !empty($_GET["animals"])) {
    $templateParams["type"] = "animal";
    $templateParams["search"] = $_GET["animals"];
} elseif (isset($_GET["persons"]) && !empty($_GET["persons"])) {
    $templateParams["type"] = "person";
    $templateParams["search"] = $_GET["persons"];
} else {
    $_SESSION["error"] = "Errore, ricerca nulla";
    header("Location: search.php");
    exit;
}

$templateParams["title"] = "Risultati per \"" . $templateParams["search"] . "\"";
$templateParams["page"] = "full-search-results.php";
require_once("template/base.php");
?>
