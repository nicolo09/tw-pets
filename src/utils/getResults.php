<?php
require_once("../bootstrap.php");

$n_results = 10;
$results = array();

if(isset($_GET["search"]) && isset($_GET["type"]) && isset($_GET["offset"])){
    if($_GET["type"] == "animal") {
        $results = $dbh->getAnimalsLike($_GET["search"], $_GET["offset"], $n_results);
    } elseif($_GET["type"] == "person") {
        $results = $dbh->getPersonsLike($_GET["search"], $_GET["offset"], $n_results); 
    }  
}

$html = "";

foreach($results as $user) {
    $username = $user["username"];
    $img = $user["immagine"];
    $href = getProfileHref($username, $_GET["type"]);
    ob_start();
    require("../template/result-bar.php");
    $html .= ob_get_clean();
}

echo json_encode($html);
exit;

?>
