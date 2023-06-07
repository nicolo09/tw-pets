<?php
require_once("bootstrap.php");
require_once("utils/notification-utils.php");

if (!login_check($dbh)) {
    header("Location: login.php");
    exit;
}

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

$templateParams["title"] = "Notifiche";
$templateParams["page"] = "notifications-list.php";
require_once("template/base.php");

?>