<?php
require_once("bootstrap.php");
$id=-1;
if(isset($_GET["id"])){
    $id=$_GET["id"];
}

$like=isPostLikedBy($id, getUserName($dbh), $dbh);
$likes=getLikes($id, $dbh);
//This list is to be communicated to javascript to show if a post is liked and the number of likes
echo json_encode(array($like, $likes));
?>
