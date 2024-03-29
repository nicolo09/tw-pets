<?php
require_once("bootstrap.php");

if (login_check($dbh) == false) {
    header("Location: login.php");
    exit;
}

if(isset($_GET["anim"])){
    $templateParams["animals"]=json_decode($_GET["anim"]);
}
if(isset($_GET["txt"])){
    $templateParams["descrizione"]=htmlspecialchars($_GET["txt"]);
}
if(isset($_GET["alt"])){
    $templateParams["alt"]=htmlspecialchars($_GET["alt"]);
}

$templateParams["timestamp"]= date('d/m/Y H:i', time());
$templateParams["disableLike"]=true;
$templateParams["disableSave"]=true;
$templateParams["username"]=getUserName($dbh);
$templateParams["immagineprofilo"]=getUserProfilePic(getUserName($dbh), $dbh);

$templateParams["title"]="Preview del post";
$templateParams["page"] = "preview-post-single.php";
$templateParams["id"]=-1;
$templateParams["immagine"]="#";
require_once("template/base.php");
?>
