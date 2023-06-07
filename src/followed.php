<?php
require_once("bootstrap.php");

if (!login_check($dbh)) {
    header("Location: login.php");
    exit;
}

//AJAX request
if (isset($_GET["offset"]) && isset($_GET["number"])){
    $results = $dbh->getFollowedProfiles(getUserName($dbh), $_GET["offset"], $_GET["number"]);

    foreach($results as $user) {
        $username = $user["username"];
        $img = $user["immagine"];
        $type = isset($user["email"])?"person":"animal";
        $href = getProfileHref($username, $type);
        require("template/result-bar.php");
    }
}
