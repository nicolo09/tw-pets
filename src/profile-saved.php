<?php
require_once("bootstrap.php");

if(!login_check($dbh)){
    header("Location: login.php");
    exit;
}

$templateParams["title"] = "Post salvati";
$templateParams["page"] = "saved-posts.php";

require_once("template/base.php");
