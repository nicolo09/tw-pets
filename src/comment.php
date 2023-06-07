<?php
require_once("bootstrap.php");
require_once("utils/notification-utils.php");

if (login_check($dbh) == false) {
    header("Location: login.php");
    exit;
}

//User must be logged in
$id_post = -1;
$id_padre = -1;
$text="";
$success = 1;
if (isset($_GET["id_post"])) {
    $id_post = $_GET["id_post"];
}
if (isset($_GET["id_padre"])) {
    $id_padre = $_GET["id_padre"];
}
if (isset($_GET["text"])) {
    $text = $_GET["text"];
}

//Checking if the post id is valid
if (isIdPostValid($id_post, $dbh)&&$text!="") {
    //The post's id is valid
    $text=htmlspecialchars($text);
    //Checking if this comments is answering another comment and if the outer comment'id is valid
    $padre_comment=getCommentInfo($id_padre, $dbh);
    if(empty($padre_comment)){
        //This comment isn't an answer
        $id_comment=newComment(getUserName($dbh), $text, $id_post, $dbh);
        if($id_comment!=-1){
            //Sending notification to post's maker
            addCommentNotification(getUserName($dbh), $id_post, $dbh);
        }
    }else{
        //This comment is an answer
        if($padre_comment["id_post"]==$id_post){
            //Adding the answer
            $id_comment=newCommentAnswer(getUserName($dbh), $id_padre, $text, $id_post, $dbh);
            if($id_comment!=-1){
                //Sending notification to comment-father
                addReplyCommentNotification(getUserName($dbh), $id_comment, $dbh);
            }
        }
    }
} else {
    //Redirects to previous page with success value 0
    $success = 0;
}

//Reloads post page, success=0/1
header("Location: view-post-profile.php?id=" . $id . "&successL=" . $success);
