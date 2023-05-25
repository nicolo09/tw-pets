<?php
require_once("bootstrap.php");
$id=-1;
$n=5;
$offset=0;
$timestamp=-1;
$id_comment=-1;
if(isset($_GET["id_post"])){
    $id=$_GET["id_post"];
}
if(isset($_GET["id_comment"])){
    $id_comment=$_GET["id_comment"];
}
if(isset($_GET["n"])){
    $n=$_GET["n"];
}
if(isset($_GET["offset"])){
    $offset=$_GET["offset"];
}
if(isset($_GET["timestamp"])){
    $timestamp=$_GET["timestamp"];
}
if($timestamp==-1){
    //timestamp non settato
    $timestamp=date('Y-m-d H:i:s', time());
}

$result=getRecentCommentsAnswers($id, $id_comment, $n, $offset, $timestamp, $dbh);

echo json_encode($result);
?>