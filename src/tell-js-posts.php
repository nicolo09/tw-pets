<?php
require_once("bootstrap.php");
$postList=getUserCreatedPosts(getUserName($dbh),$dbh);
//This list is to be communicated to javascript to show images
echo json_encode($postList);
?>
