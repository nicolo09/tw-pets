<?php
require_once("bootstrap.php");
require_once("utils/notification-utils.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

//TODO: remove this
//Add a notification
$dbh->addNotification(getUserName($dbh), NotificationType::FOLLOW, array("follower" => "pippo"));

if (isset($_GET["number"])) {
    header("Content-Type: application/json");
    if ($dbh->hasMoreThanXNotifications(getUserName($dbh), MAX_NOTIFICATIONS)) {
        echo json_encode(array("count" => MAX_NOTIFICATIONS, "hasMore" => true));
    } else {
        //echo json_encode(array("hasMore" => false));
        echo json_encode(array("count" => $dbh->getNumberOfNotifications(getUserName($dbh)), "hasMore" => false));
    }
    exit;
}
else {
    $notifications = $dbh->getNotifications(getUserName($dbh), 10, 0);
}

$templateParams["page"] = "notifications-list.php";
require_once("template/base.php");

?>