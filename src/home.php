<?php
require_once("bootstrap.php");
if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

if(isset($_GET["offset"]) && isset($_GET["finished"]) && isset($_GET["startTime"]) && isset($_GET["seed"])) {
    $user = getUserName($dbh);
    $finished = $_GET["finished"];
    $startTime = $_GET["startTime"];
    if($finished == 0) {
        $result = $dbh->getPostsForUser($user, 10, $_GET["offset"], $startTime);
        if(count($result) < 10) {
            $result = array_merge($result, $dbh->getRecentPostsForUser($user, 10, 0, $startTime));
            $finished = 1;
        }
    } elseif($finished == 1) {
        $result = $dbh->getRecentPostsForUser($user, 10, $_GET["offset"], $startTime);
        if(count($result) < 10) {
            $result = array_merge($result, $dbh->getOlderRandomPosts($user, 10, 0, $_GET["seed"], $startTime));
            $finished = 2;
        }
    } else {
        $result = $dbh->getOlderRandomPosts($user, 10, $_GET["offset"], $_GET["seed"], $startTime);
    }

    $postIDs = array_column($result, "id_post");
    ob_start();
    require("template/post-list.php");
    $html = ob_get_clean();

    $data = array(
        "postIDs" => $postIDs,
        "html" => $html,
        "finished" => $finished
    );

    echo json_encode($data);
    exit;
}

$templateParams["title"] = "Home";
$templateParams["page"] = "home-posts.php";
require_once("template/base.php");
?>
