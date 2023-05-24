<?php
require_once("bootstrap.php");
$id=-1;
$timestamp=-1;
if(isset($_GET["id_post"])){
    $id=$_GET["id_post"];
}
if(isset($_GET["timestamp"])){
    $timestamp=$_GET["timestamp"];
}
if($timestamp!=-1){
    //Quanti commenti sono stati pubblicati su questo post dopo timestamp
    $ncomments=allLoadMostRecentCommentsAfter($id, $timestamp, $dbh);
    echo json_encode(array(count($ncomments)));
}else{
    echo json_encode(array(count(array())));
}
?>
