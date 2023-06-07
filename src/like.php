<?php
require_once("bootstrap.php");
require_once("utils/notification-utils.php");

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

//Checking if the post's id valid
if (isIdPostValid($id, $dbh)) {
    //The id is valid
    if (isPostLikedBy($id, getUserName($dbh), $dbh)==false) {
        // The user liked the post
        $out = likePost($id, getUserName($dbh), $dbh);
        if ($out == false) {
            $success = 0;
        }else{
            //Sending a notification to the post's maker
            $post=getPost($id, $dbh);
            if(empty($post)==false){
                addLikeNotification(getUserName($dbh),$id, $dbh);
            }
        }
    } else {
        // The user removed the like from the post
        $out = unLikePost($id, getUserName($dbh), $dbh);
        if ($out == false) {
            $success = 0;
        }
    }
} else {
    //Redirect to previous page with success value 0
    $success = 0;
}

//Reloads post page, success=0/1
header("Location: view-post-profile.php?id=".$id."&successL=".$success);
?>
