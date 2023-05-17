<?php
require_once("bootstrap.php");
$id=-1;
if(isset($_GET["id"])){
    $id=$_GET["id"];
}

$likes=getLikes($id, $dbh);
//This list is to be communicated to javascript to show images
echo json_encode($likes);
?>
