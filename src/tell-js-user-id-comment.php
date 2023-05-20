<?php
require_once("bootstrap.php");
$id=-1;
if(isset($_GET["id"])){
    $id=$_GET["id"];
}

$username=getUsernameOfCommenter($id, $dbh);
echo json_encode($username);
?>