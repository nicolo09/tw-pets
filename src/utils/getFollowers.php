<?php
require_once("../bootstrap.php");

$results = array();

if(isset($_GET["user"]) && isset($_GET["type"]) && isset($_GET["offset"])){
    if($_GET["type"] == "animal"){
        $results = $dbh->getAnimalFollowers($_GET["user"], $_GET["offset"]);
    } elseif($_GET["type"] == "person") {
        $results = $dbh->getPersonFollowers($_GET["user"], $_GET["offset"]);
    }  
}

$response = json_encode(['results' => $results]);

header('Content-Type: application/json');
echo $response;

?>
