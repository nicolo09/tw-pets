<?php
require_once("bootstrap.php");
$id=-1;
if(isset($_GET["id"])){
    $id=$_GET["id"];
}

$comment=getCommentInfo($id, $dbh);
if(empty($comment)==false){
    echo json_encode($comment["username"]);
}
?>