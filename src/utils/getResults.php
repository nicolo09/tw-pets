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

$response = json_encode(['results' => $results]);

header('Content-Type: application/json');
echo $response;

?>
