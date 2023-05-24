<?php
require_once("bootstrap.php");
$id=-1;
$n=5;
$offset=0;
$timestamp=-1;
if(isset($_GET["id_post"])){
    $id=$_GET["id_post"];
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

$result=getRecentComments($id, $n, $offset, $timestamp, $dbh);
$hasAnswers=array();
foreach($result as $comment){
    $id=$comment["id_commento"];
    $hasAnswers[]=array($id=> doesCommentHaveComments($id, $dbh));
}

echo json_encode(array($result, $hasAnswers));
?>