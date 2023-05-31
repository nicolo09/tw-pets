<?php
require_once("bootstrap.php");

if(!login_check($dbh)){
    header("Location: login.php");
    exit;
}

if(isset($_GET["offset"]) && isset($_GET["number"])){
    $posts = $dbh->getSavedPosts($_SESSION["username"], $_GET["number"], $_GET["offset"]);
    require("template\post-list.php");
    exit;
}

$templateParams["title"] = "Post salvati";
$templateParams["page"] = "saved-posts.php";

require_once("template/base.php");
