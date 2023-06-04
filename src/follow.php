<?php
require_once("bootstrap.php");
require_once("utils/notification-utils.php");

//Possible type parameter values
define("PERSON", "person");
define("ANIMAL", "animal");

$username = "";
$type = "";
$success=1;

if (isset($_GET["username"]) && isset($_GET["type"])) {
    $username = $_GET["username"];
    $type = $_GET["type"];
    if ($type == PERSON) {
        //Checking if user exists and if it isn't the current user
        if (doesPersonUsernameExist($username, $dbh) && $username != getUserName($dbh)) {
            //The user can be followed
            if (doesUserFollowMe($username, getUserName($dbh), $dbh)) {
                //If the user is already being followed it gets unfollowed
                $out = unfollowPerson($username, getUserName($dbh), $dbh);
                if($out==false){
                    $success=0;
                }
            } else {
                $out = followPerson($username, getUserName($dbh), $dbh);
                if($out==false){
                    $success=0;
                }else{
                    //Follow was successfull
                    addFollowNotification(getUserName($dbh), $username, $dbh);
                }
            }
            header("Location: view-user-profile.php?username=" . $username . "&type=" . $type."&success=".$success);
        } else {
            //The account doesn't exist
            header("Location: view-user-profile.php");
        }
    } else if ($type == ANIMAL) {
        //Checking if the animal exist and if the current user owns it
        if (doesAnimalUsernameExist($username, $dbh) && $dbh->checkOwnership(getUserName($dbh), $username) == false) {
            //The animal can be followed
            if (doIFollowAnimal(getUserName($dbh), $username, $dbh)) {
                //If the animal is already being followed it gets unfollowed
                $out = unfollowAnimal($username, getUserName($dbh), $dbh);
                if($out==false){
                    $success=0;
                }
            } else {
                $out = followAnimal($username, getUserName($dbh), $dbh);
                if($out==false){
                    $success=0;
                }else{
                    //Follow was successfull
                    addFollowAnimalNotification(getUserName($dbh), $username, $dbh); 
                }
            }
            header("Location: view-user-profile.php?username=" . $username . "&type=" . $type."&success=".$success);
        } else {
            if ($dbh->checkOwnership(getUserName($dbh), $username)) {
                //The user owns the animal, so it can't be unfollowed
                header("Location: view-user-profile.php?username=" . $username . "&type=" . $type."&success=".$success);
            } else {
                //The animal doesn't exist
                header("Location: view-user-profile.php");
            }
        }
    } else {
        header("Location: view-user-profile.php");
    }
}
