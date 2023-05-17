<?php
require_once("bootstrap.php");
$id=-1;
if(isset($_GET["id"])){
    $id=$_GET["id"];
}

$like=isPostLikedBy($id, getUserName($dbh), $dbh);
//This list is to be communicated to javascript to show images
echo json_encode($like);
?>
