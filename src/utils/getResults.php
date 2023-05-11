<?php
require_once("../bootstrap.php");

$results = array();

if(isset($_GET["search"]) && isset($_GET["type"]) && isset($_GET["offset"])){
    if($_GET["type"] == "animal"){
        $results = $dbh->getAnimalsLike($_GET["search"], $_GET["offset"]);
    } elseif($_GET["type"] == "person") {
        $results = $dbh->getAnimalsLike($_GET["search"], $_GET["offset"]);
    }  
}

$response = json_encode(['results' => $results]);

header('Content-Type: application/json');
echo $response;

?>
