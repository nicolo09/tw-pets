<?php
require_once("bootstrap.php");
$id=-1;
if(isset($_GET["id"])){
    $id=$_GET["id"];
}

$like=isPostSavedBy($id, getUserName($dbh), $dbh);
//This list is to be communicated to javascript
echo json_encode($like);
?>