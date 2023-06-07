<?php
require_once("bootstrap.php");

if (login_check($dbh) == false) {
    header("Location: login.php");
    exit;
}

// The user is logged in
$id = -1;
$success = 1;
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}

//Checks if the post's id is valid
if (isIdPostValid($id, $dbh)) {
    //The id is valid
    if (isPostSavedBy($id, getUserName($dbh), $dbh)==false) {
        //The user is saving the post
        $out = savePost($id, getUserName($dbh), $dbh);
        if ($out == false) {
            $success = 0;
        }
    } else {
        //The user is removing the post from their saved posts
        $out = unSavePost($id, getUserName($dbh), $dbh);
        if ($out == false) {
            $success = 0;
        }
    }
} else {
    //Redirect to previous page with success value 0
    $success = 0;
}

//Reloads post page, success=0/1
header("Location: view-post-profile.php?id=".$id."&successS=".$success);
?>