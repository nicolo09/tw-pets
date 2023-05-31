<?php
require_once("bootstrap.php");
if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

if(isset($_GET["offset"]) && isset($_GET["finished"])) {
    $user = getUserName($dbh);
    $finished = $_GET["finished"];
    if($finished == 0) {
        $result = $dbh->getPostsForUser($user, 10, $_GET["offset"]);
        if(count($result) < 10) {
            $result = array_merge($result, $dbh->getRecentPostsForUser($user, 10, 0));
            $finished = true;
        }
    } else {
        $result = $dbh->getRecentPostsForUser($user, 10, $_GET["offset"]);
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
